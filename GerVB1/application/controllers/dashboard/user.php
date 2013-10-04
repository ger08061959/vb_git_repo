<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("dashboard.php");

class User extends Dashboard {

	var $model = 'user_model';
	var $title = 'Users';
	
	function __construct()
	{
		parent::__construct();
		
		if($this->actionName!='index') {
			$this->_requirePermission('view_user');
		}
		
		// copied from admin controller... maybe add to dashboard controller
		$model = $this->model;
		
		$this->load->model($model);
		$this->data['model']       = $model;
		$this->data['modelName']   = $this->$model->modelName;
		$this->data['viewKeys']    = $this->$model->viewKeys;
		$this->data['createKeys']  = isset($this->$model->createKeys) ? $this->$model->createKeys : $this->$model->editKeys;
		$this->data['editKeys']    = $this->$model->editKeys;
		$this->data['tableKeys']   = $this->$model->tableKeys;
		$this->data['fields']      = $this->$model->fields;
		// end
	}
	
	function index()
	{
		$model = $this->model;
		$organisation = $this->currentOrganisation;
		$organisation_id = $organisation ? $organisation->id : null;

		$sub_orgs = $this->organisation_model->suborganisations( $organisation_id );
		$sub_orgs_ids   = array();
		
		if($organisation_id==null)
			$sub_orgs_ids[] = null;
		else
			$sub_orgs_ids[] = $organisation_id; // WHERE IN (NULL, ...) does NOT work!!

		$sub_orgs_with_name = array();
		$sub_orgs_with_name[$organisation_id] = $organisation_id == null ? 'Datiq' : $organisation->name;
		
		foreach($sub_orgs as $sub_org)
		{
			$sub_orgs_ids[] = $sub_org->id;
			// $sub_orgs_ids[$sub_org->id] = $sub_org->name;
		}
		// $results = $this->db->get_where('users', array('organisation_id' => $sub_orgs) )->result();
		$this->db->where_in('organisation_id', $sub_orgs_ids);
		
		if($organisation_id==null)
			$this->db->or_where('organisation_id', null);
		
		$results = $this->db->get('users')->result();
		
		//debug
		// $query = $this->db->last_query();
		// $this->_setMessage( 'info' , 'Debugging: ' , $query );
		
		$no_results = false;

		if( count($results) <= 0){
			$no_results = true;
			$results = array();
		}
		$results = array_reverse($results); // sort
		// $tableKeys = array( 'minoto_id', 'name', 'url' );
		
		$this->_addForeignOrganisation(); // adds organisation data to user model.
		$this->data['results'] = $results;
		$this->data['fields']['organisation_id']['values'][''] = 'Datiq'; // quick fix?
		$this->_render_page('default');
	}
	
	function _checkID($id = null){
		if($id){
			$user = $this->db->get_where('users', array('id' => $id))->row();

			if($user){
				$this->model_id = $user->id;
				
				$all_organisations = array();
				if( $this->currentOrganisation ){
					// add this organisation
					$all_organisations[] = $this->currentOrganisation->id;
					
					// add all publishers if reseller
					if($this->currentOrganisation->type=="reseller"){
						$publishers = $this->organisation_model->suborganisations( $this->currentOrganisation->id );
						foreach( $publishers as $publisher ){
							$all_organisations[] = $publisher->id;
						}
					}
				} else {
					// administrator/Datiq
					$all_organisations[] = null; // NULL is "Datiq"
					$publishers = $this->organisation_model->suborganisations( null );
					foreach( $publishers as $publisher ){
						$all_organisations[] = $publisher->id;
					}
				}
				$allowed = in_array($user->organisation_id, $all_organisations);
				
				if($allowed)
					return $user;

				$this->_setMessage( 'error' , 'Warning!!' , 'Invalid privileges.' );
				redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
			}
		}
		$this->_setMessage( 'error' , 'Warning!!' , 'Invalid or no ID set.' );
		redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
	}
	
	// override
	function create(){
		if($this->input->post())
		{
			$model = $this->model;
			$this->_setValidationRules('createKeys'); // default is editKeys
			if ($this->form_validation->run() == false)
			{
				// continue editing
				$this->data['result'] = $this->input->post();
				$validation_errors = validation_errors();
				$this->_setMessage('error', 'Error!!', $validation_errors);
			}
			else
			{
				$username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
				$username = str_replace(' ', '.', $username);
				$email    = $this->input->post('email');
				$password = $this->input->post('password');
				
				
				$additional_data = $this->input->post();
				if($additional_data['organisation_id']=='0') $additional_data['organisation_id'] = null; // addded
				unset($additional_data['password']);
				unset($additional_data['password_confirm']);
				unset($additional_data['email']);
				
				$success = $this->ion_auth->register($username, $password, $email, $additional_data);
				if($success)
				{
					if($this->input->post('active')=='1')
						$this->ion_auth->activate($success);
					else // =='0'
						$this->ion_auth->deactivate($success);
					
					//-- added --//
					//-- added --//
					//-- added --//
					$id = $success;
					$post = $this->input->post();
					$roles = $this->input->post('roles');
					unset($post['roles']); 
					
					$this->db->delete('users_roles', array('user_id' => $id)); // remove all roles
					if($roles){
						foreach($roles as $role_id){
							$this->db->insert('users_roles', array('user_id' => $id, 'role_id' => $role_id));
						}
					}
					///////////////////////
					
					$this->model_id = $id;
					$this->_log_activity();
					
					$this->_setMessage( 'success' , 'Success!!' , 'Successfully created item.' );
					redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
				}
				else
				{
					$this->data['result'] = $this->input->post();
					$this->_setMessage('error', 'Error!!', $this->ion_auth->errors());
				}
			}
		}
		$this->_addForeignOrganisation();
		$this->_addForeignRoles();
		$this->_render_page('default');
	}
	
	function view($id)
	{
		$model = $this->model;
		$result = $this->_checkID($id);
		// $result = $this->$model->get($id); // found/not found...
		
		//-- added --//
		//-- added --//
		//-- added --//
		$this->_addForeignOrganisation();
		$this->_addForeignRoles($id);
		//-- /added --//
		//-- /added --//
		//-- /added --//
		
		$this->data['result'] =  (array)$result;
		$this->_render_page('default');
	}
	
	function edit($id)
	{
		$model = $this->model;
		// check for $id // check if found
		// set message for success / fail and/or redirect
		
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
				//-- added --//
				//-- added --//
				//-- added --//
				$post = $this->input->post();
				$roles = $this->input->post('roles');
				unset($post['roles']); 
				
				$this->db->delete('users_roles', array('user_id' => $id)); // remove all roles
				foreach($roles as $role_id){
					$this->db->insert('users_roles', array('user_id' => $id, 'role_id' => $role_id));
				}
				
				if($post['organisation_id']=='0')
					$post['organisation_id'] = null;

				//-- added --//
				//-- added --//
				//-- added --//
				
				$this->$model->update($id, $post);
				$this->data['result'] = $post; // $this->input->post();
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully updated item.' );
			}
		}
		else
		{
			// otherwise post has already been set...
			$result = $this->_checkID($id);
			// $result = $this->$model->get($id); // found?
			$this->data['result'] = (array) $result;
		}

		$this->_addForeignOrganisation();
		$this->_addForeignRoles($id);
		// check for post
		$this->_render_page('default');
	}
	
	function delete($id = null)
	{
		if(isset($id) && !empty($id) && is_numeric($id))
		{
			$this->_setMessage( 'error' , 'Warning!!' , 'Deleting users is restricted. Please set the user to <strong>Inactive</strong> in the edit user panel.' );
			redirect($this->directoryName.'/'.$this->controllerName.'/edit/'.$id, 'refresh');
		}
		else
		{
			$this->_setMessage( 'error' , 'Warning!!' , 'Invalid or no ID set.' );
			redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
		}
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
	
	function _addForeignRoles($user_id = null){
		$model = $this->model;
		$all     = $this->$model->roles();
		$mine = array();
		if($user_id)
			$mine   = $this->$model->roles($user_id);

		$this->data['fields']['roles']           = $this->$model->hasMany['roles'];
		$this->data['fields']['roles']['value']  = $mine;
		$this->data['fields']['roles']['values'] = $all;
		
		$this->data['createKeys'][] = 'roles';
		$this->data['viewKeys'][] = 'roles';
		$this->data['editKeys'][] = 'roles';
	}
	
	function _addForeignOrganisation(){
		$model = $this->model;

		$organisation    = $this->currentOrganisation;
		$organisation_id = $organisation ? $organisation->id : null; // maybe recursive ?
		
		// direct organisations
		// $organisations = $this->db->get_where('organisation', array('parent_id' => $organisation_id))->result();
		
		// get ALL sub organisations
		$organisations = $this->organisation_model->suborganisations($organisation_id);
		
		if($organisation==null)
			$all   = array('0' => 'Datiq'); // default is possible to have no organisation..
		else
			$all   = array($organisation->id => $organisation->name); // this current organisation
		
		foreach($organisations as $org)
		{
			$all[$org->id] = $org->name;
		}
		
		
		$this->data['fields']['organisation_id']           = $this->$model->belongsTo['organisation_id'];
		// $this->data['fields']['organisation_id']['value']  = $organisation_id;
		$this->data['fields']['organisation_id']['values'] = $all;
		
		$this->data['createKeys'][] = 'organisation_id';
		$this->data['viewKeys'][]   = 'organisation_id';
		$this->data['editKeys'][]   = 'organisation_id';
		$this->data['tableKeys'][] =  'organisation_id';
	}
	
	// @TODO: updated emails may not already exist in the database...
}
