<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

	var $table      = '';
	var $modelName  = '';
	var $fields     = array(); // all fields with metadata
	var $viewKeys   = array(); // fields that can be viewed
	var $editKeys   = array(); // fields that can be edited
	var $tableKeys  = array(); // fields that are shown in tables
	
	// var $currentDate = '';
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		// $this->load->library('ion_auth');
		$this->load->helper('cookie');
		$this->load->helper('date');
		// $this->load->helper('language');
		// $this->lang->load('ion_auth');

		// current date time is useful for many things.
		// $this->currentDate = date('Y-m-d H:i:s');
	}
	
	// todo: pagination
	function get($id = null, $limit = -1, $offset = 0)
	{
		if($id)
			$query = $this->db->get_where($this->table, array('id' => $id))->row();
		elseif($limit > 0)
			$query = $this->db->get($this->table, $limit, $offset)->result();
		else
			$query = $this->db->get($this->table)->result();
			
		return $query; // ->row();
	}
	
	function create(array $data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
		/*
		else // may be for future ...
			$this->fromArray($data);
			$this->db->insert($this->table, $this);
		*/
	}

	function update($id, array $data)
	{
		$this->db->where('id', $id);
		$this->db->update($this->table, $data);
		return $id;
	}

	function delete($id)
	{
		$this->db->delete($this->table, array('id' => $id)); 
	}
	
}
