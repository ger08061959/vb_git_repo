<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("dashboard.php");
class Home extends Dashboard {

	var $title = 'Dashboard';
	// var $viewName = 'dashboard';

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->_render_page('default');
	}
	
	function style()
	{
		if( $this->currentOrganisation )
		{
		
		
		$color_1 = $this->currentOrganisation->color_1;
		$color_2 = $this->currentOrganisation->color_2;

		echo "
@linkColor                     : $color_1;
@linkColorHover                : $color_2;
@btnPrimaryBackground          : $color_1;
@btnPrimaryBackgroundHighlight : $color_2;
		";
		}
	}
	
}
