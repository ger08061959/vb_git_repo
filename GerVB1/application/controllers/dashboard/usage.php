<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("dashboard.php");

class Usage extends Dashboard {

	var $title = 'Usage';
	var $model = null; //'organisation_model';

	function __construct()
	{
		parent::__construct();
		
		// $this->_requirePermission('is_reseller'); // special
		// $this->_requirePermission('view_usage'); // only for managers... ?
	}

	function index()
	{
		$this->_render_page('default');
	}
}
