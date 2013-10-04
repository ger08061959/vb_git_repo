<?php
// Generate a layout
// $num_columns must be valid, i.e. (1|2|3|4|6|12)

$myKeys = $createKeys;

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
	<form class="form-horizontal" action="<?php echo $directoryName?>/<?php echo $controllerName ?>/announce" method="post" accept-charset="utf-8">
		<h3>New <?php echo $modelName ?></h3>
		
		<div class="row">
			<?php foreach ($myKeys as $i => $chunk) : ?>
			<div class="span<?php echo $span_size ?>">
				<?php
				foreach($chunk as $key):
					$field = $fields[$key];
					$field['name']  = $key;
					// $field['value'] = display_value($key, null, $field);
					$field['value'] = isset($result) && isset($result[$key]) ? $result[$key] : (isset($field['value']) ? $field['value'] : '');
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
				<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Create</button>
			</div>
		</div>
	</form>
</div>
<?php
$enabled = false;
if($enabled && isset($hasPublishers) && $hasPublishers) :
//
// if administrator or reseller, i.e. we have multiple publishers.
// do a pop-up and force them to choose a publisher first, because 
// based on the publisher, we will have the metadata.
//
?>
<!-- Video Modal -->
<div id="video-modal" class="modal hide fade" tabindex="-1">
	<form class="form-horizontal <!--ajax-form-simple-->" style="margin:0;padding:0;" action="dashboard/video/announce" method="post">
	<div class="modal-header">
		<a class="close" href="dashboard/video">&times;</a>
		<h3>New Video</h3>
	</div>
	<div class="modal-body">
		<div class="my-message"></div>

		<?php
			$videoKeys = array('organisation_id', 'identifier', 'title'); // add required fields.
			foreach($videoKeys as $key){
				$field = $fields[$key];
				$field['name']  = $key;
				echo input($field);
			}
		?>
	</div>
	<div class="modal-footer">
		<a href="dashboard/video" class="btn">Cancel</a>
		<button type="submit" class="btn btn-primary">Create</button>
	</div>
	</form>
</div>
<script>
	$('#video-modal').modal({
		keyboard : false,
		show: true,
		backdrop: 'static' // 'static' iff true modal
	});
</script>
<!-- /Video Modal -->
<?php endif; ?>