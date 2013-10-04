<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	var $title = 'Dashboard';
	// var $viewName = 'dashboard';

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		// place statistics and stuff here
		$this->_render_page('default');
	}
	
	function _requirePermission($permissionName)
	{
		// always override if administrator
		if( $this->ion_auth->is_admin() ) {
			return true;
		}
		
		// is_reseller
		$is_reseller = false;
		if( is_null( $this->currentOrganisation ) ){
			$is_reseller = true; // null means top-level reseller.
		} else {
			$is_reseller = ($this->currentOrganisation->type=='reseller');
		}
		
		// reseller is basically an administrator for the dashboard.
		if($is_reseller)
			return true;
			
		if($permissionName=='is_reseller'){
			redirect($this->directoryName, 'refresh');
		}
		// is_reseller
		
		// $hasPermission = false;
		$hasPermission = in_array($permissionName, $this->currentUserPermissions);
		if($hasPermission)
			return true;
		else {
			$this->_setMessage('error', 'Error!!', 'Not enough privileges to perform action');
			redirect($this->directoryName .'/'. $this->controllerName, 'refresh');
		}
	}
	
	function _generateSubNavigation()
	{
		// todo: menu generation based on permissions
		$submenu = array();
		$submenu[] = array( 'name' => 'Home'          , 'url' => 'dashboard'           , 'active' => $this->controllerName=='home'         ? 'class="active"' : '');
		
		$is_reseller = false;
		if( is_null( $this->currentOrganisation ) ){
			$is_reseller = true; // null means top-level reseller.
		} else {
			$is_reseller = ($this->currentOrganisation->type=='reseller');
		}
		$submenu[] = array( 'name' => 'Videos'        , 'url' => 'dashboard/video'     , 'active' => $this->controllerName=='video'        ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => '');
		$submenu[] = array( 'name' => 'Users'         , 'url' => 'dashboard/user'      , 'active' => $this->controllerName=='user'         ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => ''); // maybe only for managers?

		if($this->ion_auth->is_admin() || $is_reseller){
			$submenu[] = array( 'name' => 'Resellers'     , 'url' => 'dashboard/reseller'  , 'active' => $this->controllerName=='reseller'     ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => '');
			$submenu[] = array( 'name' => 'Publishers'    , 'url' => 'dashboard/publisher' , 'active' => $this->controllerName=='publisher'    ? 'class="active"' : '', 'attributes' => '', 'anchorAttributes' => '');
		}
		
		// Add items that are pull-right in reverse order.
		$submenu[] = array( 'name' => '<i class="icon-question-sign"></i> Help', 'url' => 'docs/Getting_Started', 'active' => '', 'attributes' => 'class="pull-right"', 'anchorAttributes' => 'target="_blank"'); // TODO make `icon` field?
		$submenu[] = array( 'name' => 'Usage', 'url' => 'dashboard/usage', 'active' => $this->controllerName=='usage' ? 'class="active pull-right"' : '', 'attributes' => 'class="pull-right"', 'anchorAttributes' => '' );
		return $submenu;
	}
	
}
