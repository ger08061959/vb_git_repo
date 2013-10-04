<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Whitelistdomain_model extends MY_Model
{
	var $table     = 'whitelist_domain';
	var $modelName = 'Whitelist';
	
	var $fields = array(
		'id' => array(
			'label' => 'ID',
			'type' => 'hidden'
		),
		'domain' => array(
			'label' => 'Domain',
			'type' => 'text',
			'rules' => 'required'
		),
		'description' => array(
			'label' => 'Description',
			'type' => 'text'
			//, 'rules' => 'required'
		)
	);
	
	var $viewKeys   = array(
		'id', 'domain', 'description'
	);
	var $editKeys   = array(
		//'id',
		'domain', 'description'
	);
	var $tableKeys  = array(
		'id', 'domain', 'description'
	);
}
