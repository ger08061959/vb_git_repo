<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activity_model extends MY_Model
{
	var $table     = 'activity';
	var $modelName = 'Activity';
	
	var $fields = array(
		'id' => array(
			'label' => 'ID',
			'type' => 'hidden'
		),
		'user_id' => array(
			'label' => 'User',
			'type' => 'text'
		),
		'context' => array(
			'label' => 'Context',
			'type' => 'text'
		),
		'controller' => array(
			'label' => 'Controller',
			'type' => 'text'
		),
		'action' => array(
			'label' => 'Action',
			'type' => 'text'
		),
		'description' => array(
			'label' => 'Description',
			'type' => 'text'
		),
		'model' => array(
			'label' => 'Model',
			'type' => 'text'
		),
		'model_id' => array(
			'label' => 'Model ID',
			'type' => 'text'
		),
		'ip' => array(
			'label' => 'IP',
			'type' => 'text'
		),
		'date_created' => array(
			'label' => 'Date',
			'type' => 'text'
		),
	);
	
	var $viewKeys   = array(
		// 'id', 'ip', 'description'
		'user_id', 
	);
	var $editKeys   = array(
		// 'id', 'ip', 'description'
	);
	var $tableKeys  = array(
		// 'id', 'ip', 'description'
	);
}
