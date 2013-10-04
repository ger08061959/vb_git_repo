<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Whitelist_model extends MY_Model
{
	var $table     = 'whitelist';
	var $modelName = 'Whitelist';
	
	var $fields = array(
		'id' => array(
			'label' => 'ID',
			'type' => 'hidden'
		),
		'ip' => array(
			'label' => 'IP',
			'type' => 'text',
			'rules' => 'required'
		),
		'description' => array(
			'label' => 'Description',
			'type' => 'text',
			'rules' => 'required'
		)
	);
	
	var $viewKeys   = array(
		'id', 'ip', 'description'
	);
	var $editKeys   = array(
		//'id',
		'ip', 'description'
	);
	var $tableKeys  = array(
		'id', 'ip', 'description'
	);
}
