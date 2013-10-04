<?php defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('session.gc_probability', 0);
// ini_set('session.gc_divisor', 100);

session_start();
/** fixes the following...
 * session_start() [function.session-start]: ps_files_cleanup_dir: opendir(/var/lib/php5) failed: Permission denied (13)
 */

class MY_Controller extends CI_Controller {

	var $directoryName  = '';
	var $controllerName = '';
	var $actionName     = '';
	var $viewName       = ''; // view folder to use
	
	var $model          = '';
	var $title          = '';
	var $data           = array();
	
	var $model_id       = NULL;
	
	var $currentUser            = '';
	var $currentDate            = '';
	var $currentOrganisation    = null;
	var $currentUserPermissions = array();

	var $url                 = '';
	
	const MESSAGE_TYPE_SUCCESS = 'success';
	const MESSAGE_TYPE_ERROR   = 'error';
	const MESSAGE_TYPE_INFO    = 'info';
	const MESSAGE_TYPE_WARNING = 'warning';
	
	function __construct()
	{
		parent::__construct();
		// Libraries and Helpers
		$this->load->library('ion_auth');
		$this->load->library('form_validation');
		$this->load->library('parser');
		$this->load->library('whitelist');
		$this->load->library('main');
		$this->load->driver('minoto'); // minoto! don't forget the config/minoto.php!
		$this->load->helper('url');
		$this->load->helper('myform');

		// Load MongoDB library instead of native db driver if required
		$this->config->item('use_mongodb', 'ion_auth') ? $this->load->library('mongo_db') : $this->load->database();
		
		$this->lang->load('auth');
		$this->load->helper('language');
		
		// Load models (can also use auto-loading if necessary)
		$this->load->model('organisation_model');
		$this->load->model('role_model');
		$this->load->model('video_model');
		$this->load->model('user_model');
		$this->load->model('permission_model');
		$this->load->model('whitelist_model');
		$this->load->model('whitelistdomain_model');
		$this->load->model('activity_model');

		$router               =& load_class('Router', 'core');
		$this->directoryName  = $this->uri->segment(1);
		$this->controllerName = $this->router->fetch_class();
		$this->actionName     = $this->router->fetch_method();
		$this->url            = $this->directoryName .'/'. $this->controllerName .'/'. $this->actionName;
		$this->currentDate    = date('Y-m-d H:i:s');
		
		// Redirect to login if no permission (??) Or .. redirect to index if no permisison
		if (!$this->_checkPermissions())
		{
			// set message...
			redirect('authentication/login', 'refresh');
		}
		// Permissions
		// $user = $this->ion_auth->user()->row(); // current user
		
		// something like ...
		// user->hasPermission( action, context = null )
		/*
		if(!$this->ion_auth->is_admin() || !$this->ion_auth->hasPermission($actionName, $controllerName))
		{
			redirect('/', 'refresh');
		}
		*/

		/*-- DATA FOR THE VIEW --*/
		
		// Basic values
		$this->data['base_url']       = base_url();
		$this->data['site_name']      = THE_SITE_NAME;
		$this->data['videobank_name'] = THE_VIDEOBANK_NAME;
		$this->data['title']          = $this->title;
		$this->data['directoryName']  = $this->directoryName;
		$this->data['controllerName'] = $this->controllerName;
		$this->data['actionName']     = $this->actionName;
		$this->data['this_url']       = $this->url;
		
		if($this->ion_auth->logged_in())
		{
			$this->currentUser          = $this->ion_auth->user()->row();
			$this->data['user']         = (array)$this->currentUser;
			$this->data['username']     = $this->currentUser->first_name .' '.$this->currentUser->last_name;
			
			$this->currentUserPermissions = $this->role_model->getAllPermissionsForUser($this->currentUser->id);
			
			if(!empty( $this->currentUser->organisation_id )) {
				$this->currentOrganisation  = $this->organisation_model->get( $this->currentUser->organisation_id );
				$this->data['organisation'] = (array)$this->currentOrganisation;
				$this->data['site_name']    = $this->currentOrganisation->name; // override
			}
		}
		$this->data['currentUserPermissions'] = $this->currentUserPermissions; // array()
		$this->data['organisation'] = (array)$this->currentOrganisation; // null
		
		// Menus
		$this->data['navigation'] = $this->_generateMenu();
		$this->data['subnav']     = $this->_generateSubNavigation();
		
		// Sections
		$this->data['header']  = $this->parser->parse('includes/header'  , $this->data, TRUE);
		$this->data['footer']  = $this->parser->parse('includes/footer'  , $this->data, TRUE);
		$this->data['menu']    = $this->parser->parse('includes/menu'    , $this->data, TRUE);
		$this->data['head']    = $this->parser->parse('includes/head'    , $this->data, TRUE);
		$this->data['foot']    = $this->parser->parse('includes/foot'    , $this->data, TRUE);
	}
	
	function _log_activity()
	{
		$model = null;
		if($this->model){
			$model = $this->model;
			$model = $this->$model->table;
		}
		
		$this->activity_model->create(array(
			'user_id' => $this->currentUser ? $this->currentUser->id : $this->model_id /* for authentication controller */, 
			'context' => $this->directoryName,
			'controller' => $this->controllerName,
			'action' => $this->actionName,
			'description' => NULL,
			'model' => $model,
			'model_id' => $this->model_id,
			'date_created' => $this->currentDate,
			'ip' => $_SERVER['REMOTE_ADDR']
		));

		// see http://stackoverflow.com/questions/3003145/how-to-get-client-ip-address-in-php
		// 'proxy' => $_SERVER['HTTP_X_FORWARDED_FOR']
	}

	function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	var $dashboardControllers = array( 'home', 'user', 'publisher', 'reseller', 'video', 'profile', 'usage');
	var $adminControllers     = array( 'admin', 'user', 'group', 'role', 'permissions', 'organisation', 'video', 'whitelister', 'whitelisterdomain');
	function _checkPermissions()
	{
		// Public functions
		$allowedActions = array(
			'auth'           => array('login', 'logout', 'forgot_password', 'reset_password', ''),
			'authentication' => array('login', 'logout', 'forgot_password', 'reset_password', ''),
			'video'          => array('embed', 'play', 'player', 'iframe', 'download')
		);
		if(isset($allowedActions[$this->controllerName]) && in_array($this->actionName, $allowedActions[$this->controllerName])){
			return true;
		} 
	
		// Not logged in
		if (!$this->ion_auth->logged_in()){ return false; }
		
		// Administrators
		if( $this->ion_auth->is_admin() ) {
			return true;
		}
		// Logged in users, based on database values
		// !in_array($this->controllerName, $this->adminControllers)
		// TODO: not allowed for non-admins
		if( $this->directoryName == 'dashboard' && in_array($this->controllerName, $this->dashboardControllers) ) {
			// check permissions further...
			return true;
		}
		
		return false;
		// $user = $this->ion_auth->user();
		
		// $this->roles->getByUser($user->id);
		// $permissions = authorisation->getPermissionsByUser($user->id);
		// $authorised = authorisation->hasPermission($this->controllerName, $this->actionName);
		// return $authorised;
	}
	
	function _requirePermission($permissionName)
	{
		// always override if administrator
		if( $this->ion_auth->is_admin() ) {
			return true;
		}
		
		// $hasPermission = false;
		$hasPermission = in_array($permissionName, $this->currentUserPermissions);
		if($hasPermission)
			return true;
		else {
			$this->_setMessage('error', 'Error!!', 'Not enough privileges to perform action');
			redirect($this->directoryName .'/'. $this->controllerName, 'refresh');
		}
	}
	
	function _generateMenu()
	{
		// todo: menu generation based on permissions
		// todo: make availableModels or something...
		$menu = array();
		// $menu[] = array( 'name' => 'Home'           , 'url' => '/'         , 'icon' => '<i class="icon-white icon-home"></i> ' , 'active' => $this->controllerName=='home'           ? 'class="active"' : ''),
		if($this->ion_auth->is_admin()){
			$menu[] = array( 'name' => 'Dashboard'      , 'url' => 'dashboard' , 'icon' => '<i class="icon-white icon-film"></i> ' , 'active' => ($this->directoryName=='dashboard') ? 'class="active"' : '');
			$menu[] = array( 'name' => 'Administration' , 'url' => 'admin'     , 'icon' => '<i class="icon-white icon-cog"></i> '  , 'active' => ($this->directoryName=='admin')     ? 'class="active"' : '');
		}
		return $menu;
		
	}
	
	function _generateSubNavigation()
	{
		// todo: menu generation based on permissions
		// sub navigation based on sub-controllers.
		
		$info = '<i class="icon-info-sign" data-toggle="tooltip" title="These items are synched in the cloud."></i>';
		
		return array(
			array( 'name' => 'Users'                , 'url' => 'admin/user'         , 'active' => $this->controllerName=='user'         ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => ''),
			//array( 'name' => 'Resellers'     , 'url' => 'reseller'     , 'active' => $this->controllerName=='reseller'     ? 'class="active"' : ''),
			//array( 'name' => 'Publishers'    , 'url' => 'publisher'    , 'active' => $this->controllerName=='publisher'    ? 'class="active"' : ''),
			array( 'name' => 'Groups'               , 'url' => 'admin/group'        , 'active' => $this->controllerName=='group'        ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => ''),
			array( 'name' => 'Roles'                , 'url' => 'admin/role'         , 'active' => $this->controllerName=='role'         ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => ''),
			array( 'name' => 'Permissions'          , 'url' => 'admin/permission'   , 'active' => $this->controllerName=='permission'   ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => ''),
			array( 'name' => 'IP Whitelist'            , 'url' => 'admin/whitelister'   , 'active' => $this->controllerName=='whitelister'   ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => ''),
			array( 'name' => 'Domain Whitelist'            , 'url' => 'admin/whitelisterdomain'   , 'active' => $this->controllerName=='whitelisterdomain'   ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => '')//,
			//array( 'name' => 'Organisations '.$info , 'url' => 'admin/organisation' , 'active' => $this->controllerName=='organisation' ? 'class="active"' : ''),
			//array( 'name' => 'Videos '.$info        , 'url' => 'admin/video'        , 'active' => $this->controllerName=='video'        ? 'class="active"' : '')
		);
	}
	
	function _makeMessage($type, $title, $text)
	{
		$data = array(
			'message' => array(
				'title' => $title,
				'text' => $text,
				'type' => $type // success|error|warning|info
			)
		);
		$result = $this->parser->parse('includes/message', $data, TRUE);
		return $result;
	}
	
	function _hasMessage()
	{
		if(isset($_SESSION['mymessage']))
			return true;
		return false;
	}
	
	function _setMessage($type, $title, $text)
	{
		$array = array(
			'title' => $title,
			'text' => $text,
			'type' => $type // success|error|warning|info
		);
		$_SESSION['mymessage'] = $array;
		// $this->session->userdata['mymessage'] = $array;
		// codeigniter sessions does not take arrays by default.
		// codeigniter sessions' flashdata is always used for the NEXT request.
		
		// $this->data['message'] = $array; // for current use
		// $this->session->set_flashdata('message', $array); // for re-directs...
	}
	
	function _getMessage()
	{
		$msg = '';
		if(isset($_SESSION['mymessage']))
		{
			$msg = $_SESSION['mymessage'];
			unset($_SESSION['mymessage']);
		}
		return $msg;
	
		//if(isset($this->session->userdata['mymessage'])){
			//$msg = $this->session->userdata['mymessage'];
			//unset($this->session->userdata['mymessage']);
			//return $msg;
		//} else return '';
		/*
		if( $this->session->flashdata('message') )
			$message = array( 'message' => $this->session->flashdata('message') );
		else
			$message = isset($this->data['message']) ? $this->data['message'] : '';
		
		return $message;
		*/
	}
	
	function _render_page($view, $data=null, $render=false)
	{
		$this->data = (empty($data)) ? $this->data: $data; // override with params, usually use data = null;
		
		// Dynamically load the body contents:
		// - <directoryName>/<controllerName>/<actionName>.php -- a very specific view for the controller, overrides all
		// - <directoryName>/<viewName>/<actionName>.php       -- viewName usually for inheritance
		// - <controllerName>/<actionName>.php                 -- 
		// - <viewName>/<actionName>.php                       -- useful if you want to inherit a generic view category (e.g., from the 'admin')
		// - default/<actionName>.php                          -- useful for a very generic view that does almost nothing (useful for static content pages)
		//                                         @todo: maybe force a default/default.php instead --> 
		$view_path = APPPATH . 'views/' ;
		$views = array(
			$this->directoryName.'/'.$this->controllerName.'/'.$this->actionName .'.php',
			$this->directoryName.'/'.$this->viewName . '/' . $this->actionName.'.php',
			$this->controllerName.'/'.$this->actionName .'.php',
			$this->viewName . '/' . $this->actionName.'.php',
			'default/'.$this->actionName .'.php'
		);
		
		foreach($views as $filename)
		{
			if( file_exists( $view_path . $filename ) )
			{
				$this->data['body'] = $this->parser->parse($filename, $this->data, TRUE);
				break;
			}
		}
		
		// $this->data['message'] = ; // (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		$this->data['message'] = $this->parser->parse('includes/message' , array('message' => $this->_getMessage()), TRUE);
		
		// $view_html = $this->load->view($view, $this->viewdata, $render);
		$view_html = $this->parser->parse($view, $this->data, $render);
		if (!$render) return $view_html;
	}
	
}
