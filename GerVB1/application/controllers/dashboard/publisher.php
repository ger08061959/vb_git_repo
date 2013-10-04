<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("dashboard.php");
/*
USER must be  [RESELLER] in order to access PUBLISHERS/RESELLERS!
 */
class Publisher extends Dashboard {

	var $title = 'Publishers';
	var $model = 'organisation_model';
		/*
	 Permissions
	 - 1. need to be logged in
	 - 2. need to be reseller
	 - delete : remove_publisher
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->_requirePermission('is_reseller'); // special
		
		$model = $this->model;
		
		$this->load->model($model);
		$this->load->model('organisation_model');
		$this->data['model']       = $model;
		$this->data['modelName']   = $this->$model->modelName;
		$this->data['viewKeys']    = $this->$model->viewKeys;
		$this->data['createKeys']  = isset($this->$model->createKeys) ? $this->$model->createKeys : $this->$model->editKeys;
		$this->data['editKeys']    = $this->$model->editKeys;
		$this->data['tableKeys']   = $this->$model->tableKeys;
		$this->data['fields']      = $this->$model->fields;
		
		// override some values
		$this->data['fields']['type'] = array(
			'label' => 'Type',
			'type' => 'hidden',
			'values' => array('publisher' => 'Publisher', 'reseller' => 'Reseller'),
			'value' => 'publisher',
			'rules' => 'required'
		);
		
		$this->data['fields']['enabled'] = array(
			'label' => 'Enabled',
			'type' => 'hidden',
			'values' => array('true' => 'true', 'false' => 'false'),
			'value' => 'true',
			'rules' => 'required'
		);
	}

	function index()
	{
		$this->_render_page('default');
	}
	
	function _checkID($id = null){ // TODO: also check whether publisher is a child of this->currentOrganisation
		if($id){
			$publisher = $this->db->get_where('organisation', array('minoto_id' => $id, 'type' => 'publisher'))->row();
			if($publisher){
				$this->model_id = $publisher->id;
				
				$all_publishers = array();
				if( $this->currentOrganisation ){
					// add this organisation
					$all_publishers[] = $this->currentOrganisation->id;
					
					// add all publishers if reseller
					if($this->currentOrganisation->type=="reseller"){
						$publishers = $this->organisation_model->publishers( $this->currentOrganisation->id );
						foreach( $publishers as $org ){
							$all_publishers[] = $org->id;
						}
					}
				} else {
					// administrator/Datiq
					$all_resellers[] = null; // NULL is "Datiq"
					$publishers = $this->organisation_model->publishers( null );
					foreach( $publishers as $org ){
						$all_publishers[] = $org->id;
					}
				}
				$allowed = in_array($publisher->id, $all_publishers);
				
				if($allowed)
					return $publisher;

				$this->_setMessage( 'error' , 'Warning!!' , 'Invalid privileges.' );
				redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
			}
		}
		$this->_setMessage( 'error' , 'Warning!!' , 'Invalid or no ID set.' );
		redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
	}
	
	function view($id = null)
	{
		$publisher = $this->_checkID($id);
		$this->data['result'] = (array)$publisher;
		$this->_render_page('default');
	}
	
	function edit($id = null)
	{
		$publisher = $this->_checkID($id);
		$model = $this->model;
		
		if($this->input->post())
		{
			$request = $this->input->post('request');
			unset($_POST['request']);
			$post = $this->input->post();
			
			// only basic information is required, see validation rules in model
			if($request == 'basic'){
				$this->_setValidationRules();
				
				if ($this->form_validation->run() == false)
				{
					$this->data['result'] = $this->input->post();
					echo $this->_makeMessage('error', 'Error!!', validation_errors());
				}
				else
				{
					$minoto_data = $this->minoto->publisher->update( $id , $post );
					$this->$model->update($publisher->id, $post);
					$this->data['result'] = $post;
					echo $this->_makeMessage('success', 'Success!!', ' Successfully updated organisation.');
				}
				return;
			}
			
			if($request == 'settings'){
				$this->$model->update($publisher->id, $post);
				echo $this->_makeMessage('success', 'Success!!', ' Successfully updated settings!');
				return;
			}
			
			// no ajax here, need to refresh table with every action.
			if($request == 'metadata'){
				$action = $this->input->post('action');
				if($action=='create'){
					$this->db->insert('video_metadata', array(
						'sort_order' => $this->input->post('sort_order'),
						'name' => $this->input->post('name'),
						'label' => $this->input->post('label'),
						'type' => $this->input->post('type'),
						'values' => $this->input->post('values'),
						'value' => $this->input->post('value'),
						'organisation_id' => $publisher->id
					));
					$this->_setMessage('success', 'Success!!', ' Successfully created metadata!');
				}

				if($action=='edit'){
					$this->db->where('id', $this->input->post('id'));
					$this->db->where('organisation_id', $publisher->id);
					$this->db->update('video_metadata', array(
						'sort_order' => $this->input->post('sort_order'),
						'name' => $this->input->post('name'),
						'label' => $this->input->post('label'),
						'type' => $this->input->post('type'),
						'values' => $this->input->post('values'),
						'value' => $this->input->post('value')
					));
					$this->_setMessage('success', 'Success!!', ' Successfully updated metadata!');
				}

				if($action=='remove'){
					$this->db->delete('video_metadata', array(
						'id' => $this->input->post('id'),
						'organisation_id' => $publisher->id
					)); 
					$this->_setMessage('success', 'Success!!', ' Successfully deleted metadata!');
				}
				
				redirect($this->directoryName.'/'.$this->controllerName.'/edit/'.$publisher->minoto_id.'#tab-metadata', 'refresh');
			}
			
			// no ajax here, need to refresh table with every action.
			if($request == 'whitelist'){
				$action = $this->input->post('action');

				if($action=='create'){
					$this->whitelist_model->create(array(
						'ip' => $this->input->post('ip'),
						'description' => $this->input->post('description'),
						'organisation_id' => $publisher->id
					));
					$this->_setMessage('success', 'Success!!', ' Successfully created whitelist!');
				}

				if($action=='edit'){
					$this->whitelist_model->update($this->input->post('id'), array(
						'ip' => $this->input->post('ip'),
						'description' => $this->input->post('description'),
						'organisation_id' => $publisher->id
					));
					$this->_setMessage('success', 'Success!!', ' Successfully updated whitelist!');
				}

				if($action=='remove'){
					$this->db->delete('whitelist', array(
						'id' => $this->input->post('id'),
						'organisation_id' => $publisher->id
					)); 
					$this->_setMessage('success', 'Success!!', ' Successfully deleted whitelist!');
				}

				//$this->data['result'] = (array)$publisher; // post is not for publisher data...
				redirect($this->directoryName.'/'.$this->controllerName.'/edit/'.$publisher->minoto_id.'#tab-whitelist', 'refresh');
			}
			
			// no ajax here, need to refresh table with every action.
			if($request == 'whitelistdomain'){
				$action = $this->input->post('action');

				if($action=='create'){
					$this->whitelistdomain_model->create(array(
						'domain' => $this->input->post('domain'),
						'description' => $this->input->post('description'),
						'organisation_id' => $publisher->id
					));
					$this->_setMessage('success', 'Success!!', ' Successfully created whitelist!');
				}

				if($action=='edit'){
					$this->whitelistdomain_model->update($this->input->post('id'), array(
						'domain' => $this->input->post('domain'),
						'description' => $this->input->post('description'),
						'organisation_id' => $publisher->id
					));
					$this->_setMessage('success', 'Success!!', ' Successfully updated whitelist!');
				}

				if($action=='remove'){
					$this->db->delete('whitelist_domain', array(
						'id' => $this->input->post('id'),
						'organisation_id' => $publisher->id
					)); 
					$this->_setMessage('success', 'Success!!', ' Successfully deleted whitelist!');
				}

				//$this->data['result'] = (array)$publisher; // post is not for publisher data...
				redirect($this->directoryName.'/'.$this->controllerName.'/edit/'.$publisher->minoto_id.'#tab-whitelist-domains', 'refresh');
			}
		}
		else
		{
			$this->data['result'] = (array)$publisher;
		}
		$this->_render_page('default');
	}
	
	function delete($id = null)
	{
		$this->_requirePermission('remove_publisher');
		$publisher = $this->_checkID($id);
		$model = $this->model;
		
	
		$this->minoto->publisher->remove( $id ); // todo check
		$this->$model->delete($publisher->id);
		
		$this->_setMessage( 'success' , 'Success!!' , 'Successfully removed item.' );
		redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
	}
	
	function create()
	{
		// TODO -- add to reseller
		// $organisation = $this->currentOrganisation; // must be reseller type
		// $minoto_id = $organisation ? $organisation->minoto_id : null; // null defaults to FWD account
		
		if($this->input->post())
		{
			$model = $this->model;
			$this->_setValidationRules();
			if ($this->form_validation->run() == false)
			{
				// continue editing
				$this->data['result'] = $this->input->post();
				$validation_errors = validation_errors();
				$this->_setMessage('error', 'Error!!', $validation_errors);
			}
			else
			{
				$post = $this->input->post();
				$rid  = $this->currentOrganisation ? $this->currentOrganisation->minoto_id : null;
				
				$minoto_data = $this->minoto->reseller->addPublisher($rid, $post);
				$post['minoto_id'] = $minoto_data->id;
				$post['parent_id'] = $this->currentOrganisation ? $this->currentOrganisation->id : null;
				$this->model_id = $this->$model->create($post);
				
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully created item.' );
				$this->_log_activity();
				redirect($this->directoryName.'/'.$this->controllerName.'/edit/'.$post['minoto_id'], 'refresh');
			}
		}
		$this->data['result']['type'] = 'publisher';
		$this->_render_page('default');
	}
	
	// users
	function users($id){
		$publisher = $this->_checkID($id);
		$this->data['result'] = (array)$publisher;
		// $users = $this->db->get_where('users', array('organisation_id' => $publisher->id))->result();
		$this->_render_page('default');
	}
	// videos
	function videos($id){
		$publisher = $this->_checkID($id);
		redirect($this->directoryName.'/video?organisation='.$id);
		// $this->data['result'] = (array)$publisher;
		// $videos = $this->db->get_where('video', array('organisation_id' => $publisher->id))->result();
		// $this->_render_page('default');
	}
	
	// from admin controller. TODO move to dashboard controller...
	function _setValidationRules($keys = 'editKeys')
	{
		$model  = $this->model;
		$fields = $this->$model->fields;
		
		foreach( $this->$model->$keys as $key)
		{
			$field = $fields[$key];
			
			if(isset($field['rules']))
				$this->form_validation->set_rules($key, 'lang:'.$field['label'], $field['rules']);
		}
	}
}
