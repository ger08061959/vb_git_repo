<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("dashboard.php");
/*
USER must be  [RESELLER] in order to access PUBLISHERS/RESELLERS!
 */
class Reseller extends Dashboard {

	var $title = 'Resellers';
	var $model = 'organisation_model';

		/*
	 Permissions
	 - 1. need to be logged in
	 - 2. need to be reseller
	 - delete : remove_reseller
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
			'value' => 'reseller',
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
	
	function _checkID($id = null){ // TODO: also check whether reseller is a child of this->currentOrganisation
		if($id){
			$reseller = $this->db->get_where('organisation', array('minoto_id' => $id, 'type' => 'reseller'))->row();
			if($reseller){
				$this->model_id = $reseller->id;
				
				$all_resellers = array();
				if( $this->currentOrganisation ){
					// add this organisation
					$all_resellers[] = $this->currentOrganisation->id;
					
					// add all publishers if reseller
					if($this->currentOrganisation->type=="reseller"){
						$resellers = $this->organisation_model->resellers( $this->currentOrganisation->id );
						foreach( $resellers as $org ){
							$all_resellers[] = $org->id;
						}
					}
				} else {
					// administrator/Datiq
					$all_resellers[] = null; // NULL is "Datiq"
					$resellers = $this->organisation_model->resellers( null );
					foreach( $resellers as $org ){
						$all_resellers[] = $org->id;
					}
				}
				$allowed = in_array($reseller->id, $all_resellers);
				
				if($allowed)
					return $reseller;

				$this->_setMessage( 'error' , 'Warning!!' , 'Invalid privileges.' );
				redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
			}
		}
		$this->_setMessage( 'error' , 'Warning!!' , 'Invalid or no ID set.' );
		redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
	}
	
	function view($id = null)
	{
		$reseller = $this->_checkID($id);
		$this->data['result'] = (array)$reseller;
		$this->_render_page('default');
	}
	
	function edit($id = null)
	{
		$reseller = $this->_checkID($id);
		$model = $this->model;
		
		if($this->input->post())
		{
			$this->_setValidationRules();
			
			if ($this->form_validation->run() == false)
			{
				$this->data['result'] = $this->input->post();
				$this->_setMessage('error', 'Error!!', validation_errors());
			}
			else
			{
				$post = $this->input->post();
				$minoto_data = $this->minoto->reseller->update( $id , $post );
				$this->$model->update($reseller->id, $post);
				$this->data['result'] = $post;
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully updated item.' );
			}
		}
		else
		{
			$this->data['result'] = (array)$reseller;
		}
		$this->_render_page('default');
	}
	
	function delete($id = null)
	{
		$this->_requirePermission('remove_reseller');
		$reseller = $this->_checkID($id);
		$model = $this->model;
		
	
		$this->minoto->reseller->remove( $id ); // todo check
		$this->$model->delete($reseller->id);
		
		$this->_setMessage( 'success' , 'Success!!' , 'Successfully removed item.' );
		redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
	}
	
	function create()
	{
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
				
				$minoto_data = $this->minoto->reseller->addReseller($rid, $post);
				$post['minoto_id'] = $minoto_data->id;
				$post['parent_id'] = $this->currentOrganisation ? $this->currentOrganisation->id : null;
				$this->model_id = $this->$model->create($post);
				
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully created item.' );
				$this->_log_activity();
				redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
			}
		}
		$this->data['result']['type'] = 'reseller';
		$this->_render_page('default');
	}
	
	// users
	function users($id){
		$reseller = $this->_checkID($id);
		$this->data['result'] = (array)$reseller;
		// $this->db->get_where('users', array('organisation_id' => $publisher->id))->result();
		$this->_render_page('default');
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
