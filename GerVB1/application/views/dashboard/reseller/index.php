<?php
$organisation_id = $organisation ? $organisation['id'] : null;

$resellers = $this->organisation_model->resellers( $organisation_id );
$no_resellers = false;

if( count($resellers) <= 0){
	$no_resellers = true;
	$results = array();
} else {
	$results = $resellers;
}

$results = array_reverse($results);
$tableKeys = array( 'minoto_id', 'name', 'url' );
?>

<?php if( $no_resellers ) : ?>
<div class="well">
<p class="text-center">You don't have any resellers yet. Use <a class="btn btn-primary" href="<?php echo $directoryName?>/reseller/create"><i class="icon-white icon-plus"></i> New Reseller</a> to make one.</p>
</div>

<?php else : ?>
<p>
<a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/create"><i class="icon-white icon-plus"></i> New Reseller</a>
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
		<?php if($result->type=='reseller') : ?>
		<tr class="">
		<?php foreach($tableKeys as $key) : ?>
			<td><?php echo display_value($key, $result, $fields[$key]); ?></td>
		<?php endforeach; ?>
			<td class="row-actions">
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/view/<?php echo $result->minoto_id; ?>" class="btn btn-small" data-toggle="tooltip" title="View"><i class="icon-list-alt"></i></a>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/edit/<?php echo $result->minoto_id; ?>" class="btn btn-small" data-toggle="tooltip" title="Edit"><i class="icon-pencil"></i></a>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/delete/<?php echo $result->minoto_id; ?>" class="btn btn-small confirmation-modal" data-toggle="tooltip" title="Remove"><i class="icon-remove"></i></a>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/users/<?php echo $result->minoto_id; ?>" class="btn btn-small btn-primary" data-toggle="tooltip" title="Users"><i class="icon-user icon-white"></i></a>
			</td>
		</tr>
		<?php endif; ?>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>