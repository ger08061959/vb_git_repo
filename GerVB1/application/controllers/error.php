<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends MY_Controller {

	public function index()
	{
		$this->output->set_status_header('404');
		$this->_render_page('simple');
	}
}