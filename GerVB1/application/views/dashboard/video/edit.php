<!-- generate form -->
<?php if(isset($model) && !empty($model) && isset($result)) :?>

<?php
// Generate a layout
// $num_columns must be valid, i.e. (1|2|3|4|6|12)
$myKeys = $editKeys;

$default_num_columns = 2;
$num_columns = isset($num_columns) ? $num_columns : $default_num_columns;
$num_columns = $num_columns > 12 ? 12 : $num_columns;
$num_columns = $num_columns <  1 ?  1 : $num_columns;
$num_columns = !in_array($num_columns, array(1, 2, 3, 4, 6, 12)) ? $default_num_columns : $num_columns;
$num_fields  = count( $myKeys );
$span_size   = 12 / $num_columns;
if($num_fields > 0) :
	if($num_fields >= 8)
		$myKeys = array_chunk($myKeys, ceil($num_fields / $num_columns));
	else // if too few keys, multiple columns are hideous
		$myKeys = array( $myKeys, array() ) ;
else :
	$this->load->view('includes/message', array('message' => array(
		'type' => 'error',
		'title' => 'Error!',
		'text' => 'An error has occurred generating this form.'
	)));
endif; ?>

<div class="container">
	{breadcrumbs}
	<form class="form-horizontal" action="<?php echo $directoryName?>/<?php echo $controllerName ?>/edit/<?php echo $result['minoto_id'] ?>" method="post" accept-charset="utf-8">
		<h3>Edit <?php echo $modelName ?></h3>

		<div class="row">
			<?php foreach ($myKeys as $i => $chunk) : ?>
			<div class="span<?php echo $span_size ?>">
				<input type="hidden" name="minoto_id" value="<?php echo $result['minoto_id'] ?>">
				<input type="hidden" name="status" value="<?php echo $result['status'] ?>">
				<?php
				foreach($chunk as $key):
					$field = $fields[$key];
					$field['name']  = $key;
					$field['value'] = $field['value'] = isset($result) && isset($result[$key]) ? $result[$key] : (isset($field['value']) ? $field['value'] : '');
					$field['value'] = html_escape($field['value']);
					echo input( $field );
				endforeach;
				?>
			</div>
			<?php endforeach; ?>
		</div>

		<div class="form-actions">
			<div class="pull-right">
				<a class="btn" href="<?php echo $directoryName?>/<?php echo $controllerName ?>"><i class="icon-remove"></i> Cancel</a>
				<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Save</button>
			</div>
		</div>
	</form>
</div>
<!--
	<pre>
	<?php print_r($result); ?>
	</pre>
-->
<?php endif; ?>