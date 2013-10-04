<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model
{
	var $table  = 'users';
	var $modelName = 'User';
	
	var $fields = array(
		'id' => array(
			'label' => 'ID',
			'type' => 'hidden'
		),
		'email' => array(
			'label' => 'Email',
			'type' => 'text',
			'rules' => 'required|valid_email'
		),
		'first_name' => array(
			'label' => 'First name',
			'type' => 'text',
			'rules' => 'required'
		),
		'last_name' => array(
			'label' => 'Last name',
			'type' => 'text',
			'rules' => 'required'
		),
		'active' => array(
			'label' => 'Active',
			'type' => 'select',
			'values' => array('1' => 'Active', '0' => 'Inactive'),
			'value' => '1'
		),
		'phone' => array(
			'label' => 'Telephone',
			'type' => 'text'
		),
		'company' => array(
			'label' => 'Company',
			'type' => 'text'
		),
		'password' => array(
			'label' => 'Password',
			'type' => 'password',
			'rules' => 'required|min_length[4]|matches[password_confirm]'
		),
		'password_confirm' => array(
			'label' => 'Confirm password',
			'type' => 'password',
			'rules' => 'required'
		),
		'business_unit' => array(
			'label' => 'Business Unit',
			'type' => 'select-custom-fwd'
		)
	);
	var $viewKeys   = array(
		// 'username',
		'email',
		'first_name',
		'last_name',
		'active',
		'phone',
		'business_unit'
		//'company'
	);
	var $createKeys   = array(
		// 'username',
		'email',
		'first_name',
		'last_name',
		'active',
		'phone',
		//'company',
		'password',
		'password_confirm',
		'business_unit'
	);
	var $editKeys   = array(
		// 'username',
		'email',
		'first_name',
		'last_name',
		'active',
		'phone',
		'business_unit'
		//'company'
	);
	var $tableKeys  = array(
		'id',
		//'username',
		'email',
		'first_name',
		'last_name',
		'active'
		// roles
		// groups
	);
	
	var $hasMany = array(
		// many-to-many
		'roles' => array(
			'many' => true,
			'type'  => 'checkbox',
			'label' => 'Roles',
			'model' => 'role_model',
			'through' => 'users_roles', // pivot table (optional)
			'foreign_key' => 'user_id',
			'far_key' => 'role_id' // only for pivot tables
		)
	);
	
	var $belongsTo = array(
		'organisation_id' => array(
			'type' => 'select',
			'label' => 'Organisation',
			'model' => 'organisation_model',
			'foreign_key' => 'organisation_id',
			'value' => null, // to be filled in by controller
			'values' => array() // to be filled in by controller
		)
	);
	
	function roles($id = null)
	{
		if(is_numeric($id)) {
			$roles = $this->db->select('roles.id')
							->join('users_roles', 'users_roles.role_id = roles.id')
							->join('users', 'users_roles.user_id = users.id')
							->where('users.id', $id)
							->get('roles')->result();
		} else {
			$roles = $this->db->get('roles')->result();
		}
		
		$role_ids = array();
		foreach($roles as $role) {
			if(is_numeric($id)) // only ids
				$role_ids[] = $role->id;
			else // all
				$role_ids[$role->id] = $role->name;
		}
		return $role_ids;
	}
}
