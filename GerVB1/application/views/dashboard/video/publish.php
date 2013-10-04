<?php
$pub          = $this->db->get_where('organisation', array('id' => $result['organisation_id'] ))->row();
$video        = $this->minoto->video->getVideo( $pub->minoto_id, $result['minoto_id'] );
$thisUrl      = $this->directoryName .'/'. $this->controllerName .'/'.  $this->actionName . '/' . $result['minoto_id'];
?>

<div class="container">
	{breadcrumbs}
	<form class="form-horizontal" action="<?php echo $thisUrl; ?>" method="post">
		<h3>Publish Video </h3>
		<div class="row">
			<div class="span12">
				<input type="hidden" id="minoto_id" name="minoto_id" value="<?php echo $result['minoto_id']; ?>"></input>
				<?php // inputs here ?>
			</div>
		</div>

		<div class="form-actions">
			<div class="pull-right">
				<a class="btn" href="<?php echo $directoryName?>/<?php echo $controllerName ?>"><i class="icon-arrow-left"></i> Back</a>
				<button type="submit" class="btn btn-primary"><i class="icon-share-alt icon-white"></i> Publish</button>
			</div>
		</div>
	</form>
</div>