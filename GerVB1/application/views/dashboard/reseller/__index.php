<p>
	Available actions:
	<a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/view"><i class="icon-white icon-list-alt"></i> View Reseller</a>
	<a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/announce"><i class="icon-white icon-plus"></i> New Reseller</a>
	<a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/edit"><i class="icon-white icon-pencil"></i> Edit Reseller</a>

	<a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/resellers"><i class="icon-white icon-user"></i> View Resellers</a>
	<a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/publishers"><i class="icon-white icon-user"></i> View Publishers</a>
	<a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/synchronize"><i class="icon-white icon-refresh"></i> Synchronize</a>
</p>

<?php
$results = $this->minoto->reseller->publishers() ;
$results = array_reverse($results);
$fields = array(
	'id' => array( 'label' => 'Identifier' ),
	'name' => array( 'label' => 'Name' ),
	'url' => array( 'label' => 'Url', 'display' => 'url' ),
	'enabled' => array( 'label' => 'Enabled' )
);
$tableKeys = array( 'id', 'name', 'url', 'enabled' );
?>
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
			<td><?php echo display_value($key, $result, $fields[$key]); //echo html_escape($result->$key); ?></td>
		<?php endforeach; ?>
			<td class="row-actions">
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/view/<?php echo $result->id; ?>" class="btn btn-small" data-toggle="tooltip" title="View"><i class="icon-list-alt"></i></a>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/edit/<?php echo $result->id; ?>" class="btn btn-small" data-toggle="tooltip" title="Edit"><i class="icon-pencil"></i></a>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/delete/<?php echo $result->id; ?>" class="btn btn-small confirmation-modal" data-toggle="tooltip" title="Remove"><i class="icon-remove"></i></a>
				<!--<a href="#" class="btn btn-small" data-toggle="tooltip" title="publiceren"><i class="icon-share-alt"></i></a>-->
			</td>
		</tr>
		<?php endif; ?>
	<?php endforeach; ?>
	</tbody>
</table>

<!-- --
<pre>
<?php
print_r($results);
?>
</pre>
<!-- -->