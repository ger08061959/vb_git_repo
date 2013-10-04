<?php if(isset($model) && !empty($model)) :?>
	<p>
		<a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/create"><i class="icon-white icon-plus"></i> New <?php echo $modelName; ?></a>
	</p>
	
	<?php if(isset($results) && empty($results)) : ?>
	<div class="well">
	<p class="text-center">Nothing to see here. Use <a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/create"><i class="icon-white icon-plus"></i> New <?php echo $modelName; ?></a> to make one.</p>
	</div>
	<?php endif; ?>

	<?php if(isset($results) && !empty($results)) : ?>
			<table class="table table-hover table-sortable">
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
							<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/view/<?php echo $result->id; ?>" class="btn btn-small" data-toggle="tooltip" title="View"><i class="icon-list-alt"></i></a>
							<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/edit/<?php echo $result->id; ?>" class="btn btn-small" data-toggle="tooltip" title="Edit"><i class="icon-pencil"></i></a>
							<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/delete/<?php echo $result->id; ?>" class="btn btn-small confirmation-modal" data-toggle="tooltip" title="Remove"><i class="icon-remove"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
	<?php endif; ?>
<?php endif; ?>