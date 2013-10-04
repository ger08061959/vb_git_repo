<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Organisation_model extends MY_Model
{
	var $table     = 'organisation';
	var $modelName = 'Organisation';
	
	// if null, get all publishers
	function publishers( $organisation_id = null )
	{
		// todo: recursion -- does the reseller need to see sub-sub items?
		$where = array();
		if( $organisation_id ){
			$where['parent_id'] = $organisation_id;
		}
		$where['type'] = 'publisher';

		return $this->db->get_where($this->table, $where)->result();
	}
	
	function resellers( $organisation_id = null )
	{
		// todo: recursion -- does the reseller need to see sub-sub items?
		$where = array();
		if( $organisation_id ){
			$where['parent_id'] = $organisation_id;
		}
		$where['type'] = 'reseller';

		return $this->db->get_where($this->table, $where)->result();
	}
	
	// used in dashboard/user
	function suborganisations( $organisation_id = null )
	{
		$where = array();
		$where['parent_id'] = $organisation_id;

		$orgs =  $this->db->get_where($this->table, $where)->result();
		$suborgs = array();
		foreach($orgs as $org) {
			$results = $this->suborganisations($org->id);
			if($results)
				$suborgs = array_merge($suborgs, $results);
		}
		return array_merge($orgs, $suborgs);
	}
	
	var $fields = array(
		'id' => array(
			'label' => 'ID',
			'type' => 'hidden'
		),
		'minoto_id' => array(
			'label' => 'External ID',
			'type' => 'hidden'
		),
		'type' => array(
			'label' => 'Type',
			'type' => 'select',
			'values' => array('publisher' => 'Publisher', 'reseller' => 'Reseller'),
			'rules' => 'required'
		),
		'name' => array(
			'label' => 'Name',
			'type' => 'text',
			'rules' => 'required'
		),
		'url' => array(
			'label' => 'URL',
			'type' => 'text',
			'rules' => 'required',
			'display' => 'url'
		),
		'enabled' => array(
			'label' => 'Enabled',
			'type' => 'select',
			'values' => array('true' => 'true', 'false' => 'false')
		),
		'player_minoto_id' => array(
			'label' => 'Player ID',
			'type' => 'text',
			'value' => '3133'
		),
		'theme' => array(
			'label' => 'Theme',
			'type' => 'select',
			'values' => array('' => 'Default ING (Orange)', 'default' => 'Default Blue' , 'custom' => 'Custom' ) /* todo, change '' to 'ing' */
		),
		'logo_url' => array(
			'label' => 'Logo',
			'type' => 'text'
		),
		'color_1' => array(
			'label' => 'Color #1',
			'type' => 'text',
			'value' => '#EA650D'
		),
		'color_2' => array(
			'label' => 'Color #2',
			'type' => 'text',
			'value' => '#E64415'
		),
		'publish_url_1' => array(
			'label' => 'Publish URL External',
			'type' => 'text'
		),
		'publish_url_2' => array(
			'label' => 'Publish URL Internal',
			'type' => 'text'
		)
	);
	var $viewKeys   = array(
		//'id',
		//'minoto_id',
		'type',
		'name',
		'url'
		//'enabled'
	);
	var $editKeys   = array(
		'id',
		'id',
		'minoto_id',
		'type',
		'name',
		'url',
		'enabled',
		'player_minoto_id',
		'theme',
		'logo_url',
		'color_1',
		'color_2',
		'publish_url_1',
		'publish_url_2'
	);
	var $tableKeys  = array(
		'id',
		'minoto_id',
		'type',
		'name',
		'url',
		'enabled'
	);
}
