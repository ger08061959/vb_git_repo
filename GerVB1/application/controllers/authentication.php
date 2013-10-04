<?php defined('BASEPATH') OR exit('No direct script access allowed');

// copied from Auth
class Authentication extends MY_Controller {
	var $title = 'Authentication';
	
	//redirect if needed, otherwise display the user list
	function index()
	{

		if (!$this->ion_auth->logged_in())
			redirect('authentication/login', 'refresh');
		else
			redirect('dashboard/', 'refresh');
	}

	//log the user in
	function login()
	{
		if($post = $this->input->post())
		{
			$remember = (bool) $this->input->post('remember');
			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully logged in.' );
				$this->currentUser = $this->ion_auth->user()->row();
				$this->_log_activity();
				redirect('/dashboard', 'refresh');
			} else {
				$this->_setMessage( 'error' , 'Error!!' , 'Invalid credentials.' );
				redirect('authentication/login', 'refresh');
			}
		}
		
		// if logged in, redirect to dashboard
		if ($this->ion_auth->logged_in()){
			redirect('/dashboard', 'refresh');
		}
		
		$this->_render_page('simple');
	}

	//log the user out
	function logout()
	{
		if($this->ion_auth->user()->row())
			$this->_log_activity();
	
		$logout = $this->ion_auth->logout();
		$this->_setMessage( 'success' , 'Success!!' , 'Successfully logged out.' );
		redirect('authentication/login', 'refresh');
	}

	//change password
	function change_password()
	{
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

		if (!$this->ion_auth->logged_in())
		{
			redirect('authentication/login', 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() == false)
		{
			if(validation_errors())
				$this->_setMessage( 'error' , 'Error!!' , validation_errors() );

			$this->_render_page('simple');
		}
		else
		{
			$identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));
			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
			if ($change)
			{
				//if the password was successfully changed
				$this->_setMessage( 'success' , 'Success!!' , $this->ion_auth->messages() );
				$this->logout();
			}
			else
			{
				$this->_setMessage( 'error' , 'Error!!' , $this->ion_auth->errors() );
				redirect('authentication/change_password', 'refresh');
			}
		}
	}

	//forgot password
	function forgot_password()
	{
		$this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required');
		if ($this->form_validation->run() == false)
		{
			if ( $this->config->item('identity', 'ion_auth') == 'username' )
				$this->data['identity_label'] = $this->lang->line('forgot_password_username_identity_label');
			else
				$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');

			if(validation_errors())
				$this->_setMessage( 'error' , 'Error!!' , validation_errors() );

			$this->_render_page('simple');
		}
		else
		{
			// get identity for that email
			$config_tables = $this->config->item('tables', 'ion_auth');
			$identity = $this->db->where('email', $this->input->post('email'))->limit('1')->get($config_tables['users'])->row();

			//run the forgotten password method to email an activation code to the user
			$forgotten = false;
			if($identity)
				$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten)
			{
				//if there were no errors
				$this->_setMessage( 'success' , 'Success!!' , $this->ion_auth->messages() );
				redirect("authentication/login", 'refresh'); //we should display a confirmation page here instead of the login page
			}
			else
			{
				$this->_setMessage( 'error' , 'Error!!' , $this->ion_auth->errors() );
				redirect("authentication/forgot_password", 'refresh');
			}
		}
	}

	//reset password - final step for forgotten password
	function reset_password($code = NULL)
	{
		if (!$code) show_404();

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			//if the code is valid then display the password reset form
			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() == false)
			{
				if(validation_errors())
					$this->_setMessage( 'error' , 'Error!!' , validation_errors() );


				$this->data['user_id'] = $user->id;
				$this->data['csrf']    = $this->_get_csrf_nonce();
				$this->data['code']    = $code;

				//render
				$this->_render_page('simple');
			}
			else
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
				{
					//something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);
					show_error($this->lang->line('error_csrf'));
				}
				else
				{
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};
					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change) {
						//if the password was successfully changed
						$this->_setMessage( 'success' , 'Success!!' , $this->ion_auth->messages() );
						$this->logout();
					} else {
						$this->_setMessage( 'error' , 'Error!!' , $this->ion_auth->errors() );
						redirect('authentication/reset_password/' . $code, 'refresh');
					}
				}
			}
		}
		else
		{
			//if the code is invalid then send them back to the forgot password page
			$this->_setMessage( 'error' , 'Error!!' , $this->ion_auth->errors() );
			redirect("authentication/forgot_password", 'refresh');
		}
	}

}
