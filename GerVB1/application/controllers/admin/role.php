<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("admin.php");

class Role extends Admin {

	var $model = 'role_model';
	var $title = 'Administration';
	
	function create()
	{
		if($this->input->post())
		{
			$model = $this->model;
			$this->_setValidationRules();
			if ($this->form_validation->run() == false)
			{
				$this->data['result'] = $this->input->post();
				$validation_errors = validation_errors();
				$this->_setMessage('error', 'Error!!', $validation_errors);
			}
			else
			{
				//-- added --//
				//-- added --//
				//-- added --//
				$post = $this->input->post();
				$permissions = $this->input->post('permissions');
				unset($post['permissions']); 
				//-- added --//
				//-- added --//
				//-- added --//
				
				$id = $this->$model->create($post);
				
				//-- added --//
				//-- added --//
				//-- added --//
				$this->db->delete('roles_permissions', array('role_id' => $id)); // remove all permissions
				foreach($permissions as $permission_id){
					$this->db->insert('roles_permissions', array('role_id' => $id, 'permission_id' => $permission_id));
				}
				//-- /added --//
				//-- /added --//
				//-- /added --//
				
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully created item.' );
				redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
			}
		}
		//-- added --//
		//-- added --//
		//-- added --//
		$this->_addForeignPermissions();
		//-- added --//
		//-- added --//
		//-- added --//
		$this->_render_page('default');
	}
	
	function view($id)
	{
		$model = $this->model;
		$result = $this->$model->get($id); // found/not found...
		
		//-- added --//
		//-- added --//
		//-- added --//
		$this->_addForeignPermissions($id);
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
				$permissions = $this->input->post('permissions');
				unset($post['permissions']); 
				
				$this->db->delete('roles_permissions', array('role_id' => $id)); // remove all permissions
				foreach($permissions as $permission_id){
					$this->db->insert('roles_permissions', array('role_id' => $id, 'permission_id' => $permission_id));
				}
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
		
		// check for post
		$this->_addForeignPermissions($id); // added.......
		$this->_render_page('default');
	}
	
	function _addForeignPermissions($role_id = null){
		$model = $this->model;
		$all   = $this->$model->permissions();
		
		$mine = array();
		if($role_id)
			$mine = $this->$model->permissions($role_id);

		// clean ups
		$myPermissions = array();
		foreach($mine as $permission){
			$myPermissions[] = $permission['id'];
		}
		$thePermissions = array();
		foreach($all as $permission){
			$thePermissions[$permission['id']] = $permission['name'];
		}
		$this->data['fields']['permissions']           = $this->$model->hasMany['permissions'];
		$this->data['fields']['permissions']['value']  = $myPermissions;
		$this->data['fields']['permissions']['values'] = $thePermissions;
		
		$this->data['createKeys'][] = 'permissions';
		$this->data['viewKeys'][] = 'permissions';
		$this->data['editKeys'][] = 'permissions';
	}
}
