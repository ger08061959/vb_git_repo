<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//
// Usage:
//     $this->load->library('main');
//
//
// A class with useful, core stuff.
// This class shall be used when refactoring.
//
class Main {
	
	public $user         = null;
	public $organisation = null;
	// $publisher    = null;
	// $reseller     = null;
	public $url          = '';
	public $permissions  = array();
	
	public $directory    = '';
	public $controller   = '';
	public $action       = '';

	// Authorisation: user checks.
	public function isAdministrator(){
		return false;
	}
	public function isReseller(){
		return false;
	}
	public function isPublisher(){
		return false;
	}
}

/* End of file Main.php */