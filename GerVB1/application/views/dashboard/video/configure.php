<?php
define('JAVASCRIPT_PLAYER_ENABLED', false);
define('FLASH_PLAYER_ENABLED', true);

$pub          = $this->db->get_where('organisation', array('id' => $result['organisation_id'] ))->row();
$video        = $this->minoto->video->getVideo( $pub->minoto_id, $result['minoto_id'] );
$thisUrl      = $this->directoryName .'/'. $this->controllerName .'/'.  $this->actionName . '/' . $result['minoto_id'];


$color_1 = $organisation ? $organisation['color_1'] : '#EA650D';

$screens = $this->minoto->video->getScreenshots( $pub->minoto_id, $result['minoto_id'] ); // available
$screen  = $this->minoto->video->getScreenshot( $pub->minoto_id, $result['minoto_id'] ); // current

$thumbs  = $this->minoto->video->getThumbnails( $pub->minoto_id, $result['minoto_id'] ); // available
$thumb   = $this->minoto->video->getThumbnail( $pub->minoto_id, $result['minoto_id'] ); // current

$perRow  = 5;
$perRowCount = 0;

$transcodings = $this->minoto->video->getTranscodings( $pub->minoto_id, $result['minoto_id'] ); // current
// $transcodingDetails = -trk

$transcodingPresets = $this->minoto->publisher->getTranscodingPresets($pub->minoto_id); // available

// /publishers/pid/config/presets
// /publishers/pid/config/presets/prk

// ->video->getTranscodings
// ->publisher->getTranscodingPresets( ... );
// TODO can we ajaxify this shit?

// http://fwd.datiq.com/embed/3132/fTgqtvr7Rhbm?signature=b114bc044ecae494fc1d4b994c47f53a
// $embedUrl = 'http://embed.minoto-video.com/3132/'.$result['minoto_id'];
?>
<style>
/* superior overrides */
.my-image-radio.radio.inline,
.my-image-radio.radio.inline+.my-image-radio.radio.inline {
	padding : 5px;
	margin  : 0px;
}

.my-image-radio input[type="radio"] {
	display:none;
}

.my-image-radio input[type="radio"]+img {
	border-width: 2px;
} 

.my-image-radio input[type="radio"]+img:hover {
	border-color : #000;
} 

.my-image-radio input[type="radio"]:checked+img {
	border-color : <?php echo $color_1 ?>;
} 
</style>
<div class="container">
	{breadcrumbs}
		<h3>Configure Video </h3>
		<div class="row">
			<div class="span12">
			
<div class="tabbable tabs-left">
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-transcodings" data-toggle="tab">Transcodings</a></li>
	<li><a href="#tab-screenshots" data-toggle="tab">Screenshots</a></li>
    <!--<li><a href="#tab-thumbnails" data-toggle="tab">Thumbnails</a></li>-->
	<li><a href="#tab-security" data-toggle="tab">Security</a></li>
	<li><a href="#tab-preview" data-toggle="tab">Preview</a></li>
	<li><a href="#tab-share" data-toggle="tab">Share</a></li>
</ul>

<div class="tab-content">
	<!-- screenshots -->
	<div class="tab-pane fade" id="tab-screenshots">
		<?php if(count($screens) <= 0) : ?>
		<div class="well">
			<p class="text-center">No screenshots available yet. Please wait until the video is encoded.</p>
		</div>
		<?php else : ?>
		<form class="ajax-form-simple-DISABLED" action="<?php echo $directoryName?>/<?php echo $controllerName ?>/configure/<?php echo $result['minoto_id'] ?>" method="post" accept-charset="utf-8">
		<div class="my-message"></div>
		<?php foreach($screens as $screenshot) : ?>
		<label class="my-image-radio radio inline">
			<input type="radio" name="screenshot" value="<?php echo $screenshot->uri; ?>" <?php echo ($screen->uri == $screenshot->uri ? 'checked' : '') ?>>
			<img src="<?php echo $screenshot->uri; ?>" width="100" class="img-polaroid" />
		</label>
		<?php endforeach; ?>
		<div class="form-actions">
			<div class="pull-right">
				<button type="submit" class="btn btn-primary" data-loading-text="Saving ..."><i class="icon-ok icon-white"></i> Save</button>
			</div>
		</div>
		</form>
		<?php endif; ?>
	</div>
	<!-- /screenshots -->
	<!-- thumbnails -->
	<div class="tab-pane fade" id="tab-thumbnails">
		<?php if(count($screens) <= 0) : ?>
		<div class="well">
			<p class="text-center">No thumbnails available yet. Please wait until the video is encoded.</p>
		</div>
			
		<?php else : ?>
		<form class="ajax-form-simple-DISABLED" action="<?php echo $directoryName?>/<?php echo $controllerName ?>/configure/<?php echo $result['minoto_id'] ?>" method="post" accept-charset="utf-8">
		<div class="my-message"></div>
		<?php foreach($thumbs as $thumbnail) : ?>
		<label class="my-image-radio radio inline">
			<input type="radio" name="thumbnail" value="<?php echo $thumbnail->uri; ?>" <?php echo ($thumb->uri == $thumbnail->uri ? 'checked' : '') ?>>
			<img src="<?php echo $thumbnail->uri; ?>" width="100" class="img-polaroid" />
		</label>
		<?php endforeach; ?>
		<div class="form-actions">
			<div class="pull-right">
				<button type="submit" class="btn btn-primary" data-loading-text="Saving ..."><i class="icon-ok icon-white"></i> Save</button>
			</div>
		</div>
		</form>
		<?php endif; ?>
	</div>
	<!-- /thumbnails -->
	<!-- transcodings -->
	<div class="tab-pane fade in active" id="tab-transcodings">

<?php 
$fields = array(
	'key' => array(
		'label' => 'Key'
	),
	'name' => array(
		'label' => 'Name'
	),
	'description' => array(
		'label' => 'Description'
	),
);
$transcodingKeys = array('key', 'name', 'description');
?>
<table class="table table-hover">
	<thead>
		<tr>
			<?php foreach($transcodingKeys as $key) : ?>
			<th><?php echo $fields[$key]['label']; ?></th>
			<?php endforeach; ?>
			<th>Status</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($transcodingPresets as $preset) : ?>
		<?php
		// remove legacy presets... UGLY
		$allowedTranscodingKeys = array(
			'original',
			'web_lq',
			'web_mq',
			'web_hq',
			'web_hqa'
		);
		if($preset->extension != 'webm' && $preset->key != 'lq' && $preset->key != 'mq' && in_array( $preset->key, $allowedTranscodingKeys ) ) : 
		?>
		<tr class="">
		<?php foreach($transcodingKeys as $key) : ?>
			<td><?php echo $preset->$key; //display_value($key, $result, $fields[$key]); //echo html_escape($result->$key); ?></td>
		<?php endforeach; ?>
			<td>
				<?php
				$theTranscoding = null;
				foreach($transcodings as $transcoding){
					if( $preset->key==$transcoding->key )
					{
						$theTranscoding = $transcoding;
						break;
					}
				}
				
				if($theTranscoding){
					switch($theTranscoding->status){
						case 'unconverted':
							echo '<span class="label">Unconverted</span>';
							break;
						case 'busy':
							echo '<span class="label label-warning">Busy</span>';
							break;
						case 'completed':
							echo '<span class="label label-success">Completed</span>';
							break;
						case 'failed':
							echo '<span class="label label-important">Failed</span>';
							break;
					}
				}
				?>
			</td>
			<td class="row-actions-DISABLE text-right">
				<?php if($theTranscoding) : ?>
					<?php if($theTranscoding->key != 'original') : ?>
				<form class="form-inline" style="margin:0;padding:0;" action="<?php echo $directoryName?>/<?php echo $controllerName; ?>/configure/<?php echo $result['minoto_id']; ?>" method="post">
					<input type="hidden" name="transcoding" value="1" />
					<input type="hidden" name="key" value="<?php echo $preset->key; ?>" />
					<input type="hidden" name="action" value="remove" />
					
					<?php if($theTranscoding->status=='completed'):?>
					<button type="submit" class="btn btn-small" data-toggle="tooltip" title="Remove"><i class="icon-trash"></i></button>
					<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/download/<?php echo $result['minoto_id']; ?>/<?php echo $preset->key; ?>" target="_blank" class="btn btn-small btn-primary" data-toggle="tooltip" title="Download"><i class="icon-download icon-white"></i></a>
					<?php endif; ?>
					
				</form>
					<?php endif; ?>
				<?php else : ?>
				<form class="form-inline" style="margin:0;padding:0;" action="<?php echo $directoryName?>/<?php echo $controllerName; ?>/configure/<?php echo $result['minoto_id']; ?>" method="post" >
					<input type="hidden" name="transcoding" value="1" />
					<input type="hidden" name="key" value="<?php echo $preset->key; ?>" />
					<input type="hidden" name="action" value="add" />
					<button type="submit" class="btn btn-small btn-primary" data-toggle="tooltip" title="Generate"><i class="icon-ok icon-white"></i></button>
				</form>
				<?php endif; ?>
			</td>
		</tr>
		<?php endif; ?>
	<?php endforeach; ?>
	</tbody>
</table>

<!--
		<pre>
		<?php // print_r($transcodings);
		//print_r($transcodingPresets);
		//$test = $this->minoto->video->getTranscodingDetails($pub->minoto_id, $result['minoto_id'], 'original');
		//print_r($test);
		
		//$test1 = $this->minoto->video->getTranscodingDetails($pub->minoto_id, $result['minoto_id'], 'web_hq');
		//print_r($test1);
		
		//$test2 = $this->minoto->publisher->getTranscodingPresetDetails($pub->minoto_id, 'oq');
		//print_r($test2);
		?>
		</pre>
-->
	</div>
	<!-- /transcodings -->
	<!-- security -->
	<div class="tab-pane fade" id="tab-security">
		<!-- administrators only -->
		<form class="ajax-form-simple-DISABLED" action="<?php echo $directoryName?>/<?php echo $controllerName ?>/configure/<?php echo $result['minoto_id'] ?>" method="post" accept-charset="utf-8">
		<div class="form-actions" style="margin:0px;">
			<span>
				<label for="protection" style="inline">Protection: </label>
				<?php if( $this->ion_auth->is_admin() ) : ?>
				<select class="inline" name="protection">
					<option <?php echo ($video->protected=='true') ? 'selected' : ''; ?> value="true">True</option>
					<option <?php echo ($video->protected=='' || $video->protected=='false' ) ? 'selected' : ''; ?> value="false">False</option>
				</select>
				<?php else : ?>
					<?php if ($video->protected=='true') : ?>
						<span class="label label-success">ON</span> This video is only viewable for those on the whitelist.
					<?php else : ?>
						<span class="label label-error">OFF</span> This video is public.
					<?php endif; ?>
				<?php endif; ?>
			</span>
			<?php if( $this->ion_auth->is_admin() ) : ?>
			<span class="pull-right">
				<button type="submit" class="btn btn-primary" data-loading-text="Saving ..."><i class="icon-ok icon-white"></i> Save</button>
			</span>
			<?php endif; ?>
		</div>
		</form>
	</div>
	<!-- /security -->
	<!-- preview -->
	<div class="tab-pane fade" id="tab-preview">
		<!--
		<object width="800" height="450">
			<param name="movie" value="<?php echo $embedUrl ?>"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<embed src="<?php echo $embedUrl ?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="800" height="450"></embed>
		</object>
		-->
		<?php if(JAVASCRIPT_PLAYER_ENABLED): ?>
		<iframe type="text/html" width="800" height="450" src="<?php echo $iframeUrl; ?>" frameborder="0" scrolling="no" style="overflow:hidden;" allowfullscreen webkitAllowFullScreen mozallowfullscreen allowFullScreen>Video cannot be shown in this browser.</iframe>
		<?php else : ?>
		<object width="800" height="450"><param name="movie" value="<?php echo $embedUrl; ?>"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="<?php echo $embedUrl; ?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="800" height="450"></embed></object>
		<?php endif; ?>
	</div>
	<!-- /preview -->
	<!-- embed -->
	<div class="tab-pane fade" id="tab-share">
	<h3>Share Link</h3>
	<p><span class="label label-info">Note</span> Use the following link to share this video with colleagues. If the video is protected, then the video can only be seen by those who are on the whitelists, defined by ip ranges.</p>
	<?php if(JAVASCRIPT_PLAYER_ENABLED): ?>
	<div class="row">
		<div class="span8">
		<pre><?php echo $shareUrl; ?></pre>
		</div>
		<div class="span2 pull-right">
		<a class="btn btn-primary pull-right" target="_blank" href="<?php echo $shareUrl; ?>"><i class="icon-white icon-star"></i> Preview</a>
		</div>
	</div>
	<?php endif; ?>
	<?php if(FLASH_PLAYER_ENABLED): ?>
	<div class="row">
		<div class="span8">
		<pre><?php echo $shareUrl; ?>?flash=1</pre>
		</div>
		<div class="span2 pull-right">
		<a class="btn btn-primary pull-right" target="_blank" href="<?php echo $shareUrl; ?>?flash=1"><i class="icon-white icon-star"></i> Preview</a>
		</div>
	</div>
	<?php endif; ?>
	
	<h3>Download Screenshot</h3>
	<?php if(count($screens) <= 0) : ?>
	<p>Video is being transcoded.</p>
	<?php else : ?>
	<div class="row">
		<div class="span8">
		<pre><a href="<?php echo $screen->uri; ?>" download><img style="height:150px;" src="<?php echo $screen->uri; ?>" /></a></pre>
		</div>
		<div>
			<a class="btn pull-right" href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/screenshot/<?php echo $result['minoto_id']; ?>" target="_blank"><i class="icon-download"></i> Download</a>
		</div>
	</div>
	<?php endif; ?>
	
	<h3>Download Links</h3>
	<p><span class="label label-info">Note</span> These are direct download links. Everyone who has access to these links will be able to download them. If no links are shown, then they might be in the process of transcoding.</p>
<?php
foreach($transcodings as $transcoding){
	if($transcoding->status=='completed' && in_array( $transcoding->key, $allowedTranscodingKeys ) && $transcoding->key != 'original') :  // cant download [original]
	    $transcodingDownloadlink  = $overrideUrl;
		$transcodingDownloadlink .= 'dashboard/video/download/'.$result['minoto_id'].'/'.$transcoding->key; // ADD SIGNATURE FOR PUBLIC (i.e. without IP)
		$signature = $this->minoto->video->generateStreamSignature($result['minoto_id'], $transcoding->key);
		$transcodingDownloadlink .= '/'.$signature;
	?>
	<div class="row">
		<div class="span8">
		<pre><?php echo $transcodingDownloadlink; ?></pre>
		</div>
		<div class="span2 pull-right">
			<?php
			// They want to show the name somewhere, but this is only available on presets (NOT on $transcoding)
			foreach($transcodingPresets as $preset){
				if( $preset->key==$transcoding->key )
				{
					$thePreset = $preset;
					break;
				}
			}
			?>
			<a class="btn pull-right" href="<?php echo $transcodingDownloadlink; ?>" target="_blank" data-toggle="tooltip" title="" data-original-title="<?php echo $thePreset->name ?>"><i class="icon-download"></i> Download</a>
		</div>
	</div>
<?php
	endif;
}
?>
	<h3>Embed</h3>
	<p>Use the following codes to embed this video on a website.</p>
	<?php if(JAVASCRIPT_PLAYER_ENABLED): ?>
	<h5>Embed Javascript Player</h5>
	<pre><?php echo html_escape('<iframe type="text/html" width="800" height="450" src="'.$iframeUrl.'" frameborder="0" scrolling="no" style="overflow:hidden;" allowfullscreen webkitAllowFullScreen mozallowfullscreen allowFullScreen>Video cannot be shown in this browser.</iframe>'); ?></pre>
	<?php endif; ?>
	
	<?php if(FLASH_PLAYER_ENABLED): ?>
	<h5>Embed Flash Player</h5>
	<pre><?php
echo html_escape('<object width="800" height="450">
<param name="movie" value="'.$embedUrl.'"></param>
<param name="allowFullScreen" value="true"></param>
<param name="allowscriptaccess" value="always"></param>
<embed src="'.$embedUrl.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="800" height="450"></embed>
</object>'); ?>
	</pre>
	<?php endif; ?>

	</div>
	<!-- /embed -->
</div>
</div><!-- /.tabbable -->
</div><!-- /.span12 -->
</div><!-- /.row -->
</div><!-- /.container -->