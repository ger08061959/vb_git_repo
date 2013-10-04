<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Role_model extends MY_Model
{
	var $table     = 'roles';
	var $modelName = 'Role';
	
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
	
	var $hasMany = array(
		// many-to-many
		'permissions' => array(
			'many' => true,
			'type'  => 'checkbox',
			'label' => 'Permissions',
			'model' => 'permission_model',
			'through' => 'roles_permissions', // pivot table (optional)
			'foreign_key' => 'role_id',
			'far_key' => 'permission_id' // only for pivot tables
		)
		// many-to-one, one-to-many
		/*
		'permissions' => array(
			'label' => 'Permissions',
			'model' => 'permission_model'
			'foreign_key' => 'role_id'
		)
		*/
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
	
	// $user->id --> permission->names
	function getAllPermissionsForUser($id)
	{
		$roles = $this->db->select('roles.id')
					->join('users_roles', 'users_roles.role_id = roles.id')
					->join('users', 'users_roles.user_id = users.id')
					->where('users.id', $id)
					->get('roles')->result();
		$role_ids = array();
		
		foreach($roles as $role){
			$role_ids[] = $role->id;
		}
		
		if(empty($role_ids))
			return array();
			
		$permissions = $this->db->select('permissions.name')
				->join('roles_permissions', 'roles_permissions.permission_id = permissions.id')
				->join('roles', 'roles_permissions.role_id = roles.id')
				->where_in('roles.id', $role_ids)
				->get('permissions')->result();
		
		$userPermissions = array();
		foreach($permissions as $permission){
			$userPermissions[]  = $permission->name;
		}
		return $userPermissions;
	}
	
	// $role->id --> permission.ids
	function permissions($id = null)
	{
/*
SELECT  `permissions` . * 
FROM  `permissions` 
JOIN  `roles_permissions` ON  `roles_permissions`.`permission_id` =  `permissions`.`id` 
JOIN  `roles` ON  `roles_permissions`.`role_id` =  `roles`.`id` 
WHERE  `roles`.`id` = $id
*/	
	
		if(is_array($id)){
			return $this->db->select('permissions.id')//select('permissions.*')
							->join('roles_permissions', 'roles_permissions.permission_id = permissions.id')
							->join('roles', 'roles_permissions.role_id = roles.id')
							->where_in('roles.id', $id)
							->get('permissions')->result_array();
		} elseif(is_numeric($id)) {
			return $this->db->select('permissions.id')//select('permissions.*')
							->join('roles_permissions', 'roles_permissions.permission_id = permissions.id')
							->join('roles', 'roles_permissions.role_id = roles.id')
							->where('roles.id', $id)
							->get('permissions')->result_array();
		/*
			return $this->db->select('roles_permissions.permission_id as id, permissions.name, permissions.description')
							->where('roles_permissions.role_id', $id)
							->join('permissions', 'roles_permissions.permission_id = permissions.id')
							->get('roles_permissions');
							*/
		} else {
			return $this->db->get('permissions')->result_array();
			/*
			foreach ($query->result() as $row)
			{
				echo $row->title;
			}
			*/
		}
						
	}
}
