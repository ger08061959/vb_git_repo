<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

	var $model = '';
	var $title = 'Administration';
	var $viewName = 'admin';

	function __construct()
	{
		parent::__construct();
		
		if( !empty($this->model) )
		{
			$model = $this->model;
			
			$this->load->model($model);
			$this->data['model']       = $model;
			$this->data['modelName']   = $this->$model->modelName;
			$this->data['viewKeys']    = $this->$model->viewKeys;
			$this->data['createKeys']  = isset($this->$model->createKeys) ? $this->$model->createKeys : $this->$model->editKeys;
			$this->data['editKeys']    = $this->$model->editKeys;
			$this->data['tableKeys']   = $this->$model->tableKeys;
			$this->data['fields']      = $this->$model->fields;
		}
		elseif( $this->actionName != 'index' ) // || empty
		{
			redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
		}
	}

	function index()
	{
		if( !empty($this->model) )
		{	
			$model = $this->model;
			$this->data['results']   = $this->$model->get();
		}
		$this->_render_page('default');
	}
	
	// override
	// - index    // list all items
	// - create   // create new item
	// - view     // view existing item (no edit functionality)
	// - edit     // edit existing item with $id
	// - delete   // delete existing item with $id
	
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
				$this->$model->create($this->input->post());
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully created item.' );
				redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
			}
		}
		$this->_render_page('default');
	}
	
	function view($id)
	{
		// check for $id
		$model = $this->model;
		$result = $this->_checkID($id);
		// $result = $this->$model->get($id); // found/not found...
		$this->data['result'] =  (array)$result;
		$this->_render_page('default');
	}
	
	function edit($id)
	{
		$model  = $this->model;
		$result = $this->_checkID($id);
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
				$this->$model->update($id, $this->input->post());
				$this->data['result'] = $this->input->post();
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully updated item.' );
			}
		}
		else
		{
			// otherwise post has already been set...
			// $result = $this->$model->get($id); // found?
			$this->data['result'] = (array) $result;
		}
		
		// check for post
		$this->_render_page('default');
	}
	
	function delete($id = null)
	{
		$result = $this->_checkID($id);

		if(isset($id) && !empty($id) && is_numeric($id))
		{
			$model = $this->model;
			$this->$model->delete($id);
			
			$this->_setMessage( 'success' , 'Success!!' , 'Successfully removed item.' );
		}
		else
		{
			$this->_setMessage( 'error' , 'Warning!!' , 'Invalid or no ID set.' );
		}
		redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
	}
	
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
	
	function _checkID($id){
		$model = $this->model;
		
		if($id){
			$result = $this->$model->get($id); // $this->db->get_where('', array('id' => $id))->row();
			if($result) return $result;
		}
		$this->_setMessage( 'error' , 'Warning!!' , 'Invalid or no ID set.' );
		redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
	}

}
