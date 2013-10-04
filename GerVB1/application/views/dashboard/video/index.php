<?php
// $this->output->enable_profiler(TRUE); // VERY USEFUL FOR DEBUGGING QUERIES!

$user = $this->ion_auth->user()->row();
$publisher_id  = null;
$no_publishers = false;
$is_search     = false;

$searchWhere         = '(1 = 1)';
$filter_organisation = '';
$filter_status       = '';
$filter_date_from    = '';
$filter_date_to      = '';

// SEARCH FUNCTIONALITY.
// Do not forget to escape strings if using own queries.
if($this->input->get()!==false){


	if($this->input->get('search')){
		$is_search = true;
		$search = $this->input->get('search');
		$search = explode(' ', $search);
		
		$whereTitle    = '';
		$whereKeywords = '';
		$whereId       = '';
		$whereUser     = '';
		foreach($search as $word){
			$word = $this->db->escape_like_str($word); // escape
			if(!empty($whereTitle)){
				$whereTitle    .= ' AND ';
				$whereKeywords .= ' AND ';
				$whereId       .= ' AND ';
				$whereUser     .= ' AND ';
			}
			$whereTitle    .= "`title` LIKE '%$word%'";
			$whereKeywords .= "`keywords` LIKE '%$word%'";
			$whereId       .= "`minoto_id` LIKE '%$word%'";
			$whereUser     .= "CONCAT(`users`.`first_name`, ' ', `users`.`last_name`) LIKE  '%$word%'"; // need to join on `users`
		}
		$searchWhere = ("( ($whereTitle) OR ($whereKeywords) OR ($whereId) OR {$whereUser} )" );
	}
	
	if($this->input->get('organisation')){
		$search_org = $this->db->get_where('organisation', array('minoto_id' => $this->input->get('organisation')))->row();
		if($search_org){
			$is_search = true;
			$filter_organisation = $this->db->escape_str( $search_org->id );
		}
	}
	
	if($this->input->get('status')){
		$is_search = true;
		$filter_status = $this->db->escape_str( $this->input->get('status') );
	}
	
	if($this->input->get('from')){
		$filter_date_from = $this->db->escape_str($this->input->get('from'));
	}
	
	if($this->input->get('to')){
		$filter_date_to = $this->db->escape_str($this->input->get('to'));
	}
}


$publishers = array();

if($user->organisation_id){
	$organisation = $this->db->get_where('organisation', array('id' => $user->organisation_id))->row();
	
	if($organisation->type == 'publisher'){
		$publisher_id = $organisation->minoto_id;
		// publisher videos
		if($is_search)
			$this->db->where($searchWhere);

		if($filter_status)
			$this->db->where('video.status', $filter_status);

		if($filter_date_from)
			$this->db->where('video.date_created >=', $filter_date_from);

		if($filter_date_to)
			$this->db->where('video.date_created <', $filter_date_to);
		
		$results = $this->db->select('video.*')->join('users', 'users.id = video.created_by', 'left')->get_where('video', array('video.organisation_id' => $organisation->id))->result();
		$publishers[] = $organisation; // add self, reseller does not need to be added to filter
	}
	if($organisation->type == 'reseller'){
		$publishers = $this->organisation_model->publishers($organisation->id);
		$publisher_ids = array();
		
		foreach( $publishers as $publisher ){
			$publisher_ids[] = $publisher->id;
		}
		
		if( count($publishers) <= 0){
			$no_publishers = true;
			$results = array();
		} else {
			if($is_search)
				$this->db->where($searchWhere);

			if($filter_status)
				$this->db->where('video.status', $filter_status);

			if($filter_date_from)
				$this->db->where('video.date_created >=', $filter_date_from);

			if($filter_date_to)
				$this->db->where('video.date_created <', $filter_date_to);

			if($filter_organisation)
				$this->db->where('video.organisation_id', $filter_organisation);

			$results = $this->db->select('video.*')->join('users', 'users.id = video.created_by', 'left')->where_in('video.organisation_id', $publisher_ids)->get('video')->result();
		}
	}
} else { // admin, top reseller can see all publishers
	// no publisher case --> must be top reseller OR administrator.
	$publishers = $this->organisation_model->publishers(null);
	if( count($publishers) <= 0){
		$no_publishers = true;
		$results = array();
	} else {

		if($is_search)
			$this->db->where($searchWhere);
			
		if($filter_status)
			$this->db->where('video.status', $filter_status);

		if($filter_date_from)
			$this->db->where('video.date_created >=', $filter_date_from);

		if($filter_date_to)
			$this->db->where('video.date_created <', $filter_date_to);

		if($filter_organisation)
			$this->db->where('video.organisation_id', $filter_organisation);

		$results = $this->db->select('video.*')->join('users', 'users.id = video.created_by', 'left')->get('video')->result();
	}
}


//
// Publishers, Users
//
$publisherValues = array();
foreach( $publishers as $item ){
	$publisherValues[$item->id] = $item->name;
}

$userValues = array();
$all_users = $this->user_model->get();
foreach($all_users as $item){
	$userValues[$item->id] = $item->first_name.' '.$item->last_name;
}

$fields['organisation_id'] = array(
	'label' => 'Publisher',
	'type' => 'select',
	'value' => '',
	'values' => $publisherValues
);

$fields['created_by'] = array(
	'label' => 'Created by',
	'type' => 'select',
	'value' => '',
	'values' => $userValues
);

?>

<!-- filter -->
	<form action="<?php echo $directoryName?>/<?php echo $controllerName ?>" class="form-inline" method="get" accept-charset="utf-8">
	<div class="form-actions" style="margin:0px;">
		<div>
		<span>
			<input class="inline" type="text" name="search" placeholder="Search ... " value="<?php echo $this->input->get('search'); ?>">
		</span>
		<?php if(!empty( $publishers )) : ?>
		<span>
			<select class="inline" name="organisation">
				<option value="">-- Select Publisher --</option>
				<?php foreach($publishers as $publisher) : ?>
				<option <?php echo ($filter_organisation==$publisher->id) ? 'selected' : ''; ?> value="<?php echo $publisher->minoto_id ?>"><?php echo $publisher->name ?></option>
				<?php endforeach;?>
			</select>
		</span>
		<?php endif; ?>
		<span>
			<select class="inline" name="status">
				<option value="">-- Select Status --</option>
				<option <?php echo ($filter_status=='error') ? 'selected' : ''; ?> value="error">Error</option>
				<option <?php echo ($filter_status=='announced') ? 'selected' : ''; ?> value="announced">Announced</option>
				<option <?php echo ($filter_status=='uploaded') ? 'selected' : ''; ?> value="uploaded">Uploaded</option>
				<option <?php echo ($filter_status=='published') ? 'selected' : ''; ?> value="published">Published</option>
			</select>
		</span>
		<span>
		<label for="from" style="inline"> &nbsp;From: </label>
			<div class="input-append datepicker">
				<input class="" type="text" name="from" value="<?php echo $filter_date_from; ?>" data-format="yyyy-MM-dd" style="width:100px;">
				<span class="add-on"><i class="icon-calendar"></i></span>
			</div>
			&nbsp;To:&nbsp;
			<label for="to"  style="inline"></label>
			<div class="input-append datepicker">
				<input class="" type="text" name="to" value="<?php echo $filter_date_to; ?>" data-format="yyyy-MM-dd"  style="width:100px;">
				<span class="add-on"><i class="icon-calendar"></i></span>
			</div>
		</span>
		</div>
		<br />
		<div>
		<span class="pull-right">
			<a href="<?php echo $directoryName?>/<?php echo $controllerName ?>" class="btn"><i class="icon-repeat"></i> Clear</a>
			<button type="submit" class="btn btn-primary" data-loading-text="Searching ..."><i class="icon-search icon-white"></i> Search</button>
		</span>
		</div>
	</div>
	</form>
<!-- /filter -->
<?php
$results = array_reverse($results);
$tableKeys = array( 'thumbnail', 'title', 'organisation_id', 'created_by', 'date_created', 'status' );
?>
<?php if( $no_publishers ) : ?>
<div class="well">
<p class="text-center">You don't have any publishers yet. Use <a class="btn btn-primary" href="<?php echo $directoryName?>/publisher/create"><i class="icon-white icon-plus"></i> New Publisher</a> to make one.</p>
</div>
<?php elseif( empty($results) ) : ?>
	<?php if(($is_search) ) : ?>
<div class="well">
<p class="text-center">No search results found. Use <a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/announce"><i class="icon-white icon-plus"></i> New <?php echo $modelName; ?></a> to make a new video.</p>
</div>
	<?php else : ?>
<div class="well">
<p class="text-center">Nothing to see here. Use <a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/announce"><i class="icon-white icon-plus"></i> New <?php echo $modelName; ?></a> to make one.</p>
</div>
	<?php endif; ?>
<?php else : ?>

<p>
<a class="btn btn-primary" href="<?php echo $directoryName?>/<?php echo $controllerName?>/announce"><i class="icon-white icon-plus"></i> Upload New Video</a>
</p>
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
			<td><?php echo display_value($key, $result, $fields[$key]); //echo html_escape($result->$key); ?></td>
		<?php endforeach; ?>
			<td class="row-actions">
				<!-- basic actions -->
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/view/<?php echo $result->minoto_id; ?>"       class="btn btn-small" data-toggle="tooltip" title="View"><i class="icon-list-alt"></i></a>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/edit/<?php echo $result->minoto_id; ?>"       class="btn btn-small" data-toggle="tooltip" title="Edit"><i class="icon-pencil"></i></a>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/delete/<?php echo $result->minoto_id; ?>"     class="btn btn-small confirmation-modal" data-toggle="tooltip" title="Remove"><i class="icon-remove"></i></a>
				
				<!-- primary actions -->
				<?php if($result->status == 'announced') : ?>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/upload/<?php echo $result->minoto_id; ?>"     class="btn btn-small btn-primary" data-toggle="tooltip" title="Upload"><i class="icon-white icon-upload"></i></a>
				<?php endif; ?>
				
				<?php if($result->status == 'uploaded') : ?>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/configure/<?php echo $result->minoto_id; ?>"  class="btn btn-small btn-primary" data-toggle="tooltip" title="Configure"><i class="icon-white icon-cog"></i></a>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/publish/<?php echo $result->minoto_id; ?>"    class="btn btn-small btn-primary" data-toggle="tooltip" title="Publish"><i class="icon-white icon-share-alt"></i></a>
				<?php endif; ?>
				
				<?php if($result->status == 'published') : ?>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/configure/<?php echo $result->minoto_id; ?>"  class="btn btn-small btn-primary" data-toggle="tooltip" title="Configure"><i class="icon-white icon-cog"></i></a>
				<a href="<?php echo $directoryName?>/<?php echo $controllerName; ?>/statistics/<?php echo $result->minoto_id; ?>" class="btn btn-small btn-primary" data-toggle="tooltip" title="Statistics"><i class="icon-white icon-signal"></i></a>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<!-- --
<?php
print_r($results);
?>
<!-- -->
<?php endif; ?>