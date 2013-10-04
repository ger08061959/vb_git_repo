<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("admin.php");

class Whitelisterdomain extends Admin {

	var $model = 'whitelistdomain_model';
	var $title = 'Administration';
	
	function index()
	{
		$this->data['results'] = $this->db->get_where('whitelist_domain', array('organisation_id' => null))->result();
		if(!$this->_hasMessage())
			$this->_setMessage('info', '', 'This is the global domain whitelist. Every domain listed here is allowed to embed iframe with protected videos from any publisher. Set domains for specific publishers in publisher\'s settings.');
		$this->_render_page('default');
	}
}
