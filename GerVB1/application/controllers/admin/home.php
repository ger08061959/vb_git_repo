<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("admin.php");
class Home extends Admin {

	var $title = 'Administration';
	// var $viewName = 'dashboard';

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->_render_page('default');
	}
	
}
