<?php
$controllerUrl = $directoryName.'/'.$controllerName;
// result may be an post thing, because announce()
/**
 * THIS FILE NEEDS MASSIVE CLEAN UP. - XIAO
 */
?>
<style>
ul.breadcrumb li.active a { font-weight:bold; }
</style>
			<!-- breadcrumbs -->
			<ul class="breadcrumb">
				<?php
					$video_id = null;
					$hasVideo = false;
					$status   = null;
					
					if(isset($result)
						&& !empty($result)
						&& isset($result['minoto_id'])
						&& !empty($result['minoto_id']))
					{
						$hasVideo       = true;
						$video_id       = $result['minoto_id'];
						$video_status   = $result['status']; // empty when edit->post
					}
					
					// Check whether it's announce|view|edit
					if( $actionName == 'announce' ){
						$the_action = $actionName;
					} elseif($actionName == 'view' || $actionName == 'edit'){
						$the_action = $actionName.'/'.$video_id;
					} else {
						$the_action = 'edit/'.$video_id; // if not allowed, redirect to view.
					}
					
					// Check for uploaded status or not.
				?>
				<li><a href="<?php echo $controllerUrl ?>"><i class="icon-home"></i></a> <span class="divider"> &raquo; </span></li>
				<li class="<?php if( in_array( $actionName, array('announce', 'view', 'edit'))) echo 'active'; ?>"><a href="<?php echo $controllerUrl ?>/<?php echo $the_action; ?>"> Metadata</a> <span class="divider"> &raquo; </span></li>
				
				<?php if(!$hasVideo || $video_status=='error') : // announce ?>
				<li>Upload    <span class="divider"> &raquo; </span></li>
				<li>Configure <span class="divider"> &raquo; </span></li>
				<li>Publish   <span class="divider"> &raquo; </span></li>
				<li>Statistics</li>
				<?php else : ?>
				<li class="<?php if( $actionName == 'upload')    echo 'active' ?>"><a href="<?php echo $controllerUrl ?>/upload/<?php    echo $video_id ?>"> Upload</a>    <span class="divider"> &raquo; </span></li>
				
				<?php if(in_array( $video_status, array( "uploaded", "authorised", "published" ) )) : ?>
					<li class="<?php if( $actionName == 'configure')     echo 'active' ?>"><a href="<?php echo $controllerUrl ?>/configure/<?php  echo $video_id ?>"> Configure</a> <span class="divider"> &raquo; </span></li>
					<li class="<?php if( $actionName == 'publish')    echo 'active' ?>"><a href="<?php echo $controllerUrl ?>/publish/<?php    echo $video_id ?>"> Publish</a> <span class="divider"> &raquo; </span></li>
					<li class="<?php if( $actionName == 'statistics') echo 'active' ?>"><a href="<?php echo $controllerUrl ?>/statistics/<?php echo $video_id ?>"> Statistics</a></li>
				<?php else : //if($video_status == "announced") : ?>
					<li>Configure <span class="divider"> &raquo; </span></li>
					<li>Publish <span class="divider"> &raquo; </span></li>
					<li>Statistics</li>
				<?php endif; ?>
					
				<?php endif; ?>
			</ul>
			<!-- /breadcrumbs -->