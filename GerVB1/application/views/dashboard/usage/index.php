<?php
$organisation_id = $organisation ? $organisation['id'] : null;

$publishers = $this->organisation_model->publishers( $organisation_id );
$no_publishers = false;

if( count($publishers) <= 0){
	$no_publishers = true; // usesful for resellers
	$results = array();
} else {
	$results = $publishers;
}

$results = array_reverse($results);

if($organisation_id && $organisation['type']=='publisher')
{
	$results[] = $this->organisation_model->get($organisation_id);
	$no_publishers = false;
}
// Add Usage statistics from Minoto.

$from   = null;
$to     = null;
$params = array();
if($this->input->get('from') && $this->input->get('to') ){
	$from = $this->input->get('from'); // check formats
	$to   = $this->input->get('to');   // check formats
	
	$params = array(
		// 'yyyy-mm-dd' or 'Y-m-d' format
		'from' => $from,
		'to'   => $to
	);
}

$usage            = array();
$totalTraffic     = 0;
$totalStorage     = 0;
$totalTranscoding = 0;
$totalPeakStorage = 0;

foreach($results as $result){
	$result->traffic     = 0;
	$result->storage     = 0;
	$result->transcoding = 0;
	$result->peakStorage = 0; // highest storage in interval
	
	$usage = $this->minoto->publisher->getUsage( $result->minoto_id, $params );
	
	foreach($usage as $u)
	{
		$result->traffic     += $u->traffic;
		$result->transcoding += $u->transcoding;
		$result->storage     = $u->storage; // do NOT increment, because storage is static day-by-day value

		if( $result->peakStorage < $u->storage )
			$result->peakStorage =  $u->storage;

		$totalTraffic        += $u->traffic;
		$totalTranscoding    += $u->transcoding;
	}
	
	$totalStorage        += $result->storage; // increment per publisher
	$totalPeakStorage    += $result->peakStorage;
}

if(!empty($usage))
{
	$first_item = $usage[0];
	$last_item  = end($usage);
	$from       = DateTime::createFromFormat('Y-m-d H:i:s', $first_item->from);
	$from       = $from->format('Y-m-d');
	$to         = DateTime::createFromFormat('Y-m-d H:i:s', $last_item->to);
	$to         = $to->format('Y-m-d');
	reset($usage);
}

?>

<?php if( $no_publishers ) : ?>
<div class="well">
<p class="text-center">You don't have any publishers yet. Use <a class="btn btn-primary" href="<?php echo $directoryName?>/publisher/create"><i class="icon-white icon-plus"></i> New Publisher</a> to make one.</p>
</div>

<?php else : ?>

		<form action="<?php echo $directoryName?>/<?php echo $controllerName ?>/<?php echo $actionName ?>" class="form-inline" method="get" accept-charset="utf-8">
		<div class="form-actions" style="margin:0px;">
			<span>
			<label for="from" style="inline">Interval: </label>
				<div class="input-append datepicker">
					<input class="" type="text" name="from" value="<?php echo $from; ?>" data-format="yyyy-MM-dd" >
					<span class="add-on"><i class="icon-calendar"></i></span>
				</div>
				&nbsp; &#151; &nbsp;
				<label for="to"  style="inline"></label>
				<div class="input-append datepicker">
					<input class="" type="text" name="to" value="<?php echo $to; ?>" data-format="yyyy-MM-dd" >
					<span class="add-on"><i class="icon-calendar"></i></span>
				</div>
			</span>
			<span class="pull-right">
				<button type="submit" class="btn btn-primary" data-loading-text="Filtering ..."><i class="icon-ok icon-white"></i> Filter</button>
			</span>
		</div>
		</form>

<table class="table table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Publisher</th>
			<th>Traffic</th>
			<th>Storage</th>
			<th>Peak Storage</th>
			<th>Transcoding</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($results as $result) : ?>
		<?php if($result->type=='publisher') : ?>
		<tr class="">
			<td><?php echo $result->minoto_id; ?></td>
			<td><?php echo $result->name; ?></td>
			<td><?php echo formatBytes($result->traffic); ?></td>
			<td><?php echo formatBytes($result->storage); ?></td>
			<td><?php echo formatBytes($result->peakStorage); ?></td>
			<td><?php echo formatBytes($result->transcoding); ?></td>
		</tr>
		<?php endif; ?>
	<?php endforeach; ?>
		<tr class="">
			<td></td>
			<td><strong>Total</strong></td>
			<td><?php echo formatBytes($totalTraffic); ?></td>
			<td><?php echo formatBytes($totalStorage); ?></td>
			<td><?php echo formatBytes($totalPeakStorage); ?></td>
			<td><?php echo formatBytes($totalTranscoding); ?></td>
		</tr>
	</tbody>
</table>
<?php endif; ?>