<?php
$pub          = $this->db->get_where('organisation', array('id' => $result['organisation_id'] ))->row();
$video        = $this->minoto->video->getVideo( $pub->minoto_id, $result['minoto_id'] );

$color_1 = $organisation ? $organisation['color_1'] : '#EA650D';
$color_2 = $organisation ? $organisation['color_2'] : '#E64415';

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
$statistics = $this->minoto->video->getStatistics($pub->minoto_id, $video->id, $params);
$attentions = $this->minoto->video->getAttentionStatistics($pub->minoto_id, $video->id, $params);

if(!$statistics){
	// error with $from and $to, get default values
	$statistics = $this->minoto->video->getStatistics($pub->minoto_id, $video->id);
	$attentions = $this->minoto->video->getAttentionStatistics($pub->minoto_id, $video->id);
}

// Let statistics from Minoto be leading.
$first_item = $statistics[0];
$last_item  = end($statistics);
$from       = DateTime::createFromFormat('Y-m-d H:i:s', $first_item->from);
$from       = $from->format('Y-m-d');
$to         = DateTime::createFromFormat('Y-m-d H:i:s', $last_item->to);
$to         = $to->format('Y-m-d');
reset($statistics);


$viewers    = array(); // darker
$views      = array(); // lighter
$labels     = array();

$flot_viewers = array();
$flot_views   = array();

// Convert statistics into something usable.
foreach($statistics as $key => $statistic)
{
	$viewers_value = $statistic->viewers;
	$views_value   = $statistic->views;
	$datetime  = DateTime::createFromFormat('Y-m-d H:i:s', $statistic->from);
	$label     = $datetime->format('M d'); // M d
	
	// for charts.js
	$viewers[] = $viewers_value;
	$views[]   = $views_value;
	$labels[]  = "'$label'";
	
	// for jquery.flot
	$flot_viewers[] = "[$key,$viewers_value]"; // '$label'
	$flot_views[]   = "[$key,$views_value]"; // '$label'
	$flot_labels[]  = "[$key, '$label']";
}

$attention_views = array();
$attention_labels = array();

foreach($attentions as $key => $attention)
{
	// $attention->seconds; // same as $key
	//$view_value = $attention->views;
	$view_value = round($attention->views_normalized * 100);
	
	$attention_views [] = "[$key,$view_value]";
	$attention_labels[] = "[$key,'$key']";
}

$views   = implode(',', $views);
$viewers = implode(',', $viewers);
$labels  = implode(',', $labels);

$flot_labels  = implode(',', $flot_labels);
$flot_viewers = implode(',', $flot_viewers);
$flot_views   = implode(',', $flot_views);

$attention_views  = implode(',', $attention_views);
$attention_labels = implode(',', $attention_labels);
?>
<style>
/* #statistics-graph */ .flot-x-axis div.flot-tick-label { 
	font-size: 9px;
}
</style>
<script>
function hex2rgb(hex, opacity) {
	var rgb = hex.replace('#', '').match(/(.{2})/g);
	var i = 3;
	while (i--) {
		rgb[i] = parseInt(rgb[i], 16);
	}
	if (typeof opacity == 'undefined') {
		return 'rgb(' + rgb.join(', ') + ')';
	}
	return 'rgba(' + rgb.join(', ') + ', ' + opacity + ')';
};
</script>
<div class="container">
	{breadcrumbs}
	<h3>Statistics</h3>
	<div class="row">
		<div class="span12">

		<form action="<?php echo $directoryName?>/<?php echo $controllerName ?>/statistics/<?php echo $result['minoto_id'] ?>" class="form-inline" method="get" accept-charset="utf-8">
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
		
		<h4>Viewers</h4>
		<div id="statistics-graph" style="height: 400px; overflow:hidden;"></div>
		<h4>Attention Span</h4>
		<div id="attention-graph" style="height: 400px; overflow:hidden;"></div>
		
		<script>
		var divstatistics = "#statistics-graph";
		var plot = $.plot(divstatistics, [
			{ data: [<?php echo $flot_views ?>], label: "Views", color: hex2rgb("#000000", 0.5) },
			{ data: [<?php echo $flot_viewers ?>], label: "Viewers", color: hex2rgb("<?php echo $color_1; ?>", 1.0)}
		], {
			series: {
				lines  : { show: true },
				points : { show: true }
			},
			grid: {
				 hoverable: true
				//,clickable: true
			},
			yaxis: {
				min: 0,
				tickDecimals : 0,
				minTickSize: 1 // higher?
			},
			xaxis: {
				tickDecimals : 0,
				//labelWidth: 30,
				ticks: [<?php echo $flot_labels ?>]
			}
		});

		function showTooltip(x, y, contents) {
			$("<div id='tooltip'>" + contents + "</div>").css({
				position: "absolute",
				display: "none",
				top: y + 5,
				left: x + 5,
				border: "1px solid #fdd",
				padding: "2px",
				"background-color": "#fee",
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}
		
		var previousPoint = null;
		$(divstatistics).bind("plothover", function (event, pos, item) {
			if (item) {
				if (previousPoint != item.dataIndex) {
					previousPoint = item.dataIndex;
					$("#tooltip").remove();
					label = item.series.xaxis.ticks[item.dataIndex].label
					var x = item.datapoint[0].toFixed(2),
					y = item.datapoint[1].toFixed(2);
					showTooltip(item.pageX, item.pageY,
						item.series.label + " on " + label + " = " + Math.round( y ));
				}
			} else {
				$("#tooltip").remove();
				previousPoint = null;            
			}
		});
		// $(divstatistics).height( $(divstatistics).height() );
		</script>
		
		<script>
		var divattention = "#attention-graph";
		$.plot(divattention,
			[
				{ data: [<?php echo $attention_views ?>], label: "Attention", color: hex2rgb("#98cc63", 1.0)}
			],
			{
				series: {
					lines  : { show: true },
					points : { show: true }
				},
				grid: { hoverable: true },
				yaxis: {
					min: 0,
					max: 100,
					tickDecimals : 0,
					minTickSize: 1
				},
				xaxis: {
					tickDecimals : 0,
					ticks: [<?php echo $attention_labels ?>]
				}
			}
		);
		$(divattention).bind("plothover", function (event, pos, item) {
			if (item) {
				if (previousPoint != item.dataIndex) {
					previousPoint = item.dataIndex;
					$("#tooltip").remove();
					label = item.series.xaxis.ticks[item.dataIndex].label
					var x = item.datapoint[0].toFixed(2),
					y = item.datapoint[1].toFixed(2);
					showTooltip(item.pageX, item.pageY,
						Math.round(y) + "% viewers watching at " + label + " seconds.");
				}
			} else {
				$("#tooltip").remove();
				previousPoint = null;            
			}
		});
		// Canvas gives a width of 0, if the canvas is not visible.
		// So we must render it, then move it to the correct tab.
		// $(divattention).appendTo("#tab-attention");
		</script>
		</div>
	</div>
</div>
<script>
	function fixAxis(id)
	{
		// normalize x axis for statistics
		var maxOnX = 30; // maximum number of items on the x-axis
		var numOnX = $(id + ' .flot-x-axis div.flot-tick-label').length; // number of items on X
		if( numOnX > maxOnX ){
			var showEveryN = Math.round(numOnX / maxOnX) - 1; // if 60 and 30 --> 2 (then we alternate 1 0 1 0 1 0 1 0 1 etc.
			var index      = 0;
			$(id + ' .flot-x-axis div.flot-tick-label').each(function(){

				if(index == 0)
					$(this).show(); //.css('visibility', 'visible');
				else 
					$(this).hide(); // css('visibility', 'hidden');
					
				index++;
				
				if(index > showEveryN) index = 0;
			});
		}
	}
	
	fixAxis('#statistics-graph');
	fixAxis('#attention-graph');
</script>
