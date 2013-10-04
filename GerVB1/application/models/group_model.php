<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group_model extends MY_Model
{
	var $table     = 'groups';
	var $modelName = 'Group';
	
	var $fields = array(
		'id' => array(
			'label' => 'ID',
			'type' => 'hidden'
		),
		'name' => array(
			'label' => 'Name',
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
		'id', 'name', 'description'
	);
	var $editKeys   = array(
		// 'id',
		'name', 'description'
	);
	var $tableKeys  = array(
		'id', 'name', 'description'
	);
}
