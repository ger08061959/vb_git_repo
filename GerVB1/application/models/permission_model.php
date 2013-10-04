<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permission_model extends MY_Model
{
	var $table     = 'permissions';
	var $modelName = 'Permission';
	
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
		),
		'controller' => array(
			'label' => 'Controller',
			'type' => 'text',
			'rules' => 'required'
		),
		'action' => array(
			'label' => 'Action',
			'type' => 'text',
			'rules' => 'required'
		)
	);
	
	var $viewKeys   = array(
		'id', 'name', 'controller', 'action', 'description'
	);
	var $editKeys   = array(
		// 'id',
		'name', 'controller', 'action', 'description'
	);
	var $tableKeys  = array(
		'id', 'name', 'controller', 'action', 'description'
	);
}
