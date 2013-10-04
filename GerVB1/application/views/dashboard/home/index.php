<?php
// if ADMINISTRATOR -> all publishers
// if RESELLER -> all publishers belonging to this reseller
// if PUBLISHER -> this
// -----------------------------------------------------------
$user = $this->ion_auth->user()->row();
$publisher_id = null;

if($user->organisation_id)  {
	$organisation = $this->db->get_where('organisation', array('id' => $user->organisation_id))->row();
	$publisher_id = $organisation->minoto_id;
}
?>

<?php
// Publishers and Videos
$publisher_ids = array(); // PUBLISHERS
$publisher_minoto_ids = array(); // PUBLISHERS
$limit = 10;

if(isset($organisation) && $organisation){
	if($organisation->type == 'publisher'){
		$videos = $this->db->limit($limit)->order_by('date_created', 'desc')->get_where('video', array('organisation_id' => $organisation->id))->result();
		$publisher_ids[] = $organisation->id;
		$publisher_minoto_ids[] = $organisation->minoto_id;
	}
	
	if($organisation->type == 'reseller'){
		$publishers = $this->organisation_model->publishers($organisation->id);
		foreach( $publishers as $publisher ){
			$publisher_ids[] = $publisher->id;
			$publisher_minoto_ids[] = $publisher->minoto_id;
		}
		if(!empty($publisher_ids))
			$videos = $this->db->limit($limit)->order_by('date_created', 'desc')->where_in('organisation_id', $publisher_ids)->get('video')->result();
		else
			$videos = false;
	}
} else { // Datiq
	$all_publishers = $this->db->get_where('organisation', array('type' => 'publisher'))->result();
	foreach( $all_publishers as $publisher ){
		$publisher_ids[] = $publisher->id;
		$publisher_minoto_ids[] = $publisher->minoto_id;
	}
	$videos = $this->db->limit($limit)->order_by('date_created', 'desc')->get('video')->result();
}

// filter users by organisation.
$all_belonging_users = array();
if(isset($organisation) && $organisation)
{
	$all_belonging_organisations = $this->organisation_model->suborganisations($organisation->id);
	$org_ids = array();
	$org_ids[] = $organisation->id;

	foreach($all_belonging_organisations as $org)
		$org_ids[] = $org->id;

	$all_belonging_users = $this->db->where_in('organisation_id', $org_ids)->get('users')->result(); // may be any sub-organisation...
}
else
{
	$all_belonging_users = $this->db->get('users')->result(); // gets Datiq users
}
	
$all_belonging_user_ids = array();
foreach($all_belonging_users as $belonging_user)
	$all_belonging_user_ids[] = $belonging_user->id;

if(!empty($all_belonging_user_ids))
{
$activities = $this->db
					->limit($limit)
					->order_by('date_created', 'desc')
					->where('context', 'dashboard') // no authentications, but might be useful for administrators
					->where_in('user_id',  $all_belonging_user_ids)
					->get('activity')
					->result();
}
?>
<!-- $all_belonging_user_ids:
<?php 
print_r( $all_belonging_user_ids );
?>
-->

<p><a class="btn btn-primary" href="dashboard/video/announce"><i class="icon-white icon-plus"></i> Upload New Video</a></p>
<br />
<br />

<div class="row">
	<div class="span4">
		<h4>Welcome <a href="" class="profile-modal"><?php echo $user->first_name; ?> <?php echo $user->last_name; ?></a></h4>
		<!--
		<?php if($organisation) :?>
		<p>You are currently logged in as a user from the  <a href="<?php echo $organisation->url; ?>"><?php echo $organisation->name; ?></a>.</p>
		<?php else : ?>
		<p>You are currently logged in as a user from <a href="http://www.datiq.com/">Datiq</a>.</p>
		<?php endif; ?>
		-->
			
		<!--<p><i class="icon-user"></i> <?php echo html_escape($user->first_name)?> <?php echo html_escape($user->last_name)?></p>-->
		<p><i class="icon-envelope"></i> <?php echo html_escape($user->email)?></p>
		<p><i class="icon-th"></i> <?php echo html_escape($user->phone)?></p>
		<p><i class="icon-home"></i> <?php if($organisation) : echo '[' . $organisation->type . '] ' .html_escape($organisation->name); else : echo "Datiq"; endif; ?></p>
		<p><i class="icon-tag"></i> <?php echo html_escape($user->business_unit)?></p>
		<p><a href="" class="profile-modal btn"><i class="icon-user"></i> Edit Profile</a></p>
	</div>
	<div class="span4">
<?php
// -- TOP VIDEOS
$top_videos = array();
foreach($publisher_minoto_ids as $publisher_minoto_id)
{
	$minoto_result = $this->minoto->publisher->getTopVideos( $publisher_minoto_id );
	$top_videos = array_merge($top_videos, $minoto_result );
}
uasort($top_videos, function($a, $b) {
	$ab = $a->viewers - $b->viewers; // unique views
	if($ab == 0)
		return $a->views - $b->views; // total views
	return $ab;
});
$top_videos = array_reverse($top_videos);
// $top_videos = array_slice($top_videos, 0, 10);

$top_videos_keys = array_keys($top_videos);
/*
So, these videos are taken from Minoto. But it is possible that these videos are not in OUR database...
A few reasons may be:
 - video is deleted from our database, and not from Minoto
 - PROD, STAGE, and DEV have different, uploaded videos (the changes are not synced between databases).

 Variables in the Minoto array:
	->video (= Minoto ID)
	->title
	->viewers
	->views
*/
// I want to sort my DB videos by the ids of the videos from MINOTO.
class mySortByKeys {
	public $keys;
	public function compare($a, $b) {
		$a_index = array_search($a->minoto_id, $this->keys);
		$b_index = array_search($b->minoto_id, $this->keys);
		return $a_index - $b_index;
    }
}

if($top_videos_keys){ // no videos?
$topVideos = $this->db
					->limit($limit)
					->where_in('minoto_id',  $top_videos_keys)
					->get('video')
					->result();
$mySortByKeys = new mySortByKeys();
$mySortByKeys->keys = $top_videos_keys;

uasort($topVideos, array( $mySortByKeys , 'compare'));
}

?>

		<h4>Top videos this month <!--<?php echo date( 'M Y' , strtotime('-3 week')) ?>--><span class="pull-right"><a href="dashboard/video"><span class="btn btn-small btn-primary">All videos &raquo;</span></a></span></h4>
		<table class="table table-condensed table-hover">
			<tbody>
				<?php if($top_videos_keys && isset($topVideos) && !empty($topVideos)) : foreach( $topVideos as $video ) : ?>
				<tr>
					<td><a href="dashboard/video/edit/<?php echo $video->minoto_id ?>"><?php echo $video->title ?></a></td>
					<td><?php echo $this->video_model->fields['status']['values'][$video->status]; ?></td>
					<td>
						<span class="label" data-toggle="tooltip" title="" data-original-title="<?php echo $top_videos[$video->minoto_id]->views ?> views / <?php echo $top_videos[$video->minoto_id]->viewers ?> viewers"></span>
						<i class="icon-white icon-film"></i>
					</td>
				</tr>
				<?php endforeach; else: ?>
				<tr>
					<td><em>No statistics available.</em></td>
				</tr>
				<? endif; ?>
			</tbody>
		</table>
		<!--
		<h4>Latest Videos <span class="pull-right"><a href="dashboard/video"><span class="btn btn-small btn-primary">All videos &raquo;</span></a></span></h4>
		<table class="table table-condensed table-hover">
			<tbody>
				<?php if($videos) : foreach( $videos as $video ) : ?>
				<tr>
					<td><a href="dashboard/video/edit/<?php echo $video->minoto_id ?>"><?php echo $video->title ?></a></td>
					<td><?php echo $this->video_model->fields['status']['values'][$video->status]; ?></td>
				</tr>
				<?php endforeach; endif; ?>
			</tbody>
		</table>
		-->
	</div>
	<div class="span4">
		<h4>Recent Activities <!--<span class="pull-right"><a href="dashboard/activity"><span class="btn btn-small btn-primary">All activities &raquo;</span></a></span>--></h4>
		<table class="table table-condensed table-hover">
			<tbody>
<?php // FROM dashboard/videos/index
$userValues = array();
$users = $this->user_model->get();
foreach($users as $item){
	$userValues[$item->id] = $item->first_name.' '.$item->last_name;
}
?>
			<?php if(count($activities) > 0) : ?>
				<?php foreach( $activities as $activity ) : ?>
				<tr>
<?php

$activityUsername = $userValues[ $activity->user_id ];
$activityAction   = $activity->action;
$targetObjectName = $activity->model_id ? $activity->model_id : ''; // nothing if empty.

switch($activity->action)
{
	case 'login':
		$activityAction = 'logged in';
		break;
	case 'logout':
		$activityAction = 'logged out';
		break;
	case 'create':
	case 'announce':
		$activityAction = 'created';
		break;
	case 'publish':
		$activityAction = 'published';
		break;
	case 'uploaded':
		$activityAction = 'uploaded';
		break;
	case 'configure':
		$activityAction = 'configured';
		break;
	// case  'edit':
	//	$activityAction = 'edited';
	//	break;
}

if($activity->model && $activity->model_id)
{
	$targetObject = $this->db->get_where( $activity->model, array('id' => $activity->model_id) )->row();
	$targetObjectName = $activity->model_id;
	$targetObjectDefaultAction = 'edit';
	$targetObjectIdentifier = $activity->model_id;
	
	if($targetObject){
		switch($activity->model)
		{
			case 'organisation':
				$targetObjectName = $targetObject->name;
				$targetObjectIdentifier = $targetObject->minoto_id;
				break;
			case 'users':
				$targetObjectName = $targetObject->first_name.' '.$targetObject->last_name;
				break;
			case 'video':
				if($activity->action=='configure') $targetObjectDefaultAction = 'configure';
				$targetObjectIdentifier = $targetObject->minoto_id;
				$targetObjectName = $targetObject->title;
				break;
		}
	}
	
	if($targetObject)
		$targetObjectName = '<a href="'.$activity->context.'/'.$activity->controller.'/'.$targetObjectDefaultAction.'/'.$targetObjectIdentifier.'">'.$targetObjectName.'</a>';
	else
		$targetObjectName = '[deleted ' . $activity->model .' ('. $targetObjectIdentifier .')]'; // no link, because deleted or soomething...
}

?>
					<td><?php echo $activityUsername; ?> <?php echo $activityAction; ?> <?php echo $targetObjectName; ?></td>
					<td><span class="label" data-toggle="tooltip" title="" data-original-title="<?php echo $activity->date_created; /*date( 'j M Y, H:i' , strtotime($activity->date_created) );*/ ?>"><i class="icon-white icon-time"></i></span></td>
				</tr>
				<?php endforeach; ?>
				<?php else: ?>
				<tr>
					<td><em>No activities available.</em></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>