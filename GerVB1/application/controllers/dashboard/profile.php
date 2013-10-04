<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("dashboard.php");

class Profile extends Dashboard {

	var $model = 'user_model';
	var $title = 'Profile';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		// $this->_render_page('default');
		return;
	}
	
	function update()
	{
		// There's a bug with older Internet Explorer versions, where the form keeps submitting
		// even with event.preventDefault() or event.returnValue = false.
		
		$isAjax = $this->input->is_ajax_request();
		
		// user profile
		if($this->input->post())
		{
			$data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'business_unit' => $this->input->post('business_unit'),
				'phone' => $this->input->post('phone')
			);
			
			if($this->input->post('password') && $this->input->post('password_confirm'))
			{
				$password = $this->input->post('password');
				$password_confirm = $this->input->post('password_confirm');
				
				if($password == $password_confirm)
				{
					// $password = $this->input->post('password');
					$hashed = $this->ion_auth->hash_password($password);
					$data['password'] = $hashed;
				}
				else
				{
					if(!$isAjax){ // disgusting
						$this->_setMessage('error', 'Error!!', ' User profile not updated. Passwords do not match!');
						redirect($_SERVER['HTTP_REFERER'], 'refresh');
					}
					
					echo $this->_makeMessage('error', 'Error!!', ' User profile not updated. Passwords do not match!');
					return;
				}
			}
			$this->user_model->update($this->currentUser->id, $data);
		}
		if(!$isAjax){ // disgusting
			$this->_setMessage('success', 'Success!!', ' User profile successfully updated.');
			redirect($_SERVER['HTTP_REFERER'], 'refresh');
		}
		$this->_setMessage('success', 'Success!!', ' User profile successfully updated.');
		echo $this->_makeMessage('success', 'Success!!', ' <script>location.reload();</script>.');
		return;
	}
	
	function password()
	{
		// change password
		return;
	}
}
