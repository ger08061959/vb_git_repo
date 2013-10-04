<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("admin.php");

class User extends Admin {

	var $model = 'user_model';
	var $title = 'Administration';
	
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
		$result = $this->$model->get($id); // found/not found...
		
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
			$result = $this->$model->get($id); // found?
			$this->data['result'] = (array) $result;
		}

		$this->_addForeignOrganisation();
		$this->_addForeignRoles($id);
		// check for post
		$this->_render_page('default');
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
		$organisations = $this->db->get('organisation')->result(); // TODO - only get organisations from publisher->reseller
		
		$all   = array('0' => 'Datiq'); // default is possible to have no organisation..
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
	}
	
	// @TODO: updated emails may not already exist in the database...
}
