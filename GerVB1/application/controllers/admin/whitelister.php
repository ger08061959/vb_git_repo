<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("admin.php");

class Whitelister extends Admin {

	var $model = 'whitelist_model';
	var $title = 'Administration';
	
	function index()
	{
		$this->data['results'] = $this->db->get_where('whitelist', array('organisation_id' => null))->result();
		if(!$this->_hasMessage())
			$this->_setMessage('info', '', 'This is the global whitelist. Every ip address listed here is allowed to view protected videos from any publishers. Set ip addresses for specific publishers in publisher\'s settings.');
		$this->_render_page('default');
	}
}
