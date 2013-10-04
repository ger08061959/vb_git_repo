<?php
$pub          = $this->db->get_where('organisation', array('id' => $result['organisation_id'] ))->row();
$video        = $this->minoto->video->getVideo( $pub->minoto_id, $result['minoto_id'] );
$redirect_url = $base_url.$directoryName.'/'.$controllerName.'/uploaded/'.$video->id;

if(!isset( $video->upload_uri))
{
	// set 'error'; 4 14 18 20
}
?>

<div class="container">
	{breadcrumbs}
	<form class="form-horizontal" enctype="multipart/form-data" action="<?php echo $video->upload_uri ?>" method="post">
		<h4>Upload file for <span><?php echo $result['title'] ?></span></h4>
		<div class="row">
			<div class="span12">
				<input type="hidden" name="UPLOAD_IDENTIFIER" id="UPLOAD_IDENTIFIER" value="<?php echo $video->upload_token ?>">
				<input type="hidden" name="redirect_url" id="redirect_url" value="<?php echo $redirect_url;?>">
				<input type="hidden" id="vid" name="vid" value="<?php echo $video->id ?>">
				<input type="hidden" id="pid" name="pid" value="<?php echo $pub->minoto_id ?>">
					<?php
					echo input( array(
						'type' => 'file',
						'name' => 'uploadedfile',
						'label' => 'Upload'
					) );
					?>
				<div id="progress" class="hide">
					<div class="remaining text-right"></div>
					<div class="progress">
					  <div class="bar" style="width: 60%;"></div>
					</div>
				</div>
				
			</div>
		</div>

		<div class="form-actions">
			<div class="pull-right">
				<a class="btn" href="<?php echo $directoryName?>/<?php echo $controllerName ?>"><i class="icon-arrow-left"></i> Back</a>
				<img src='assets/application/loading3.gif' width='16' height='16' style='width:16px; height:16px; display:none;'/>
				<button type="submit" id="uploadButton" class="btn btn-primary" data-loading-text="Uploading <img src='assets/application/loading3.gif' width='16' height='16' style='width:16px; height:16px;'/>"><i class="icon-upload icon-white"></i> Upload</button> <!--  -->
				<script>
				$('form button[type="submit"]').click(function(e){
					$(this).button('loading');
				});
				</script>
			</div>
		</div>
	</form>
</div>

<!--
<?php
// print_r( $video );

// $uploadform = $this->minoto->video->getFileUploadForm($pub->minoto_id, $video);
// echo $this->minoto->video->getFileUploadFormUrl($pub->minoto_id, $video->id, $video->upload_token, $redirect_url);

// print_r( $uploadform );

?>
-->