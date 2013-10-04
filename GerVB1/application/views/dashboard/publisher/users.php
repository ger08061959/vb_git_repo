<?php
$users = $this->db->get_where('users', array('organisation_id' => $result['id']))->result();
$no_users = false;

if( count($users) <= 0){
	$no_users = true;
	$results = array();
} else {
	$results = $users;
}

$results = array_reverse($results);
$fields = $this->user_model->fields;
$tableKeys = array( 'first_name', 'last_name', 'email' ); // roles
?>

<?php if( $no_users ) : ?>
<div class="well">
<p class="text-center">No users found. Use <a class="btn btn-primary" href="<?php echo $directoryName?>/user/create"><i class="icon-white icon-plus"></i> New User</a> to make one.</p>
</div>

<?php else : ?>
<h3>Users for Publisher "<?php echo $result['name'] ?>"</h3>
<p>
<a class="btn btn-primary" href="<?php echo $directoryName?>/user/create"><i class="icon-white icon-plus"></i> New User</a>
</p>
<table class="table table-hover">
	<thead>
		<tr>
			<?php foreach($tableKeys as $key) : ?>
			<th><?php echo $fields[$key]['label']; ?></th>
			<?php endforeach; ?>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($results as $result) : ?>
		<tr class="">
		<?php foreach($tableKeys as $key) : ?>
			<td><?php echo display_value($key, $result, $fields[$key]); ?></td>
		<?php endforeach; ?>
			<td class="row-actions">
			<!--
				<a href="<?php echo $directoryName?>/user/view/<?php echo $result->minoto_id; ?>" class="btn btn-small" data-toggle="tooltip" title="View"><i class="icon-list-alt"></i></a>
				<a href="<?php echo $directoryName?>/user/edit/<?php echo $result->minoto_id; ?>" class="btn btn-small" data-toggle="tooltip" title="Edit"><i class="icon-pencil"></i></a>
				<a href="<?php echo $directoryName?>/user/delete/<?php echo $result->minoto_id; ?>" class="btn btn-small confirmation-modal" data-toggle="tooltip" title="Remove"><i class="icon-remove"></i></a>
				<a href="<?php echo $directoryName?>/user/users/<?php echo $result->minoto_id; ?>" class="btn btn-small btn-primary" data-toggle="tooltip" title="Users"><i class="icon-user icon-white"></i></a>
			-->
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>