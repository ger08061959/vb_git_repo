<!DOCTYPE html>
<?php
function makeCheckboxes($name, $values = array(), $value = array())
{
	$checkboxes = '';
	foreach($values as $key => $label)
	{
		$checked = in_array($key, $value) ? 'checked="checked"' : '';
		$checkboxes .= <<<checkbox
		
	<label class="checkbox"><input name="$name" type="checkbox" value="$key" $checked>$label</label>
checkbox;
	}
	return $checkboxes;
}

function makeRadios($name, $values = array(), $value = NULL)
{
	$radios = '';
	foreach($values as $key => $label)
	{
		$checked = $key == $value ? 'checked="checked"' : '';
		$radios .= <<<radio
		
	<label class="radio"><input type="radio" name="$name" id="$name-$key" value="$key" $checked>$label</label>
radio;
	}
	return $radios;
}

function makeOptions($values = array(), $value = NULL)
{
	$options = '';
	foreach($values as $key => $label)
	{
		$selected = $key == $value ? 'selected="selected"' : '';
		$options .= <<<option
		
	<option value="$key" $selected>$label</option>'
option;
	}
	return $options;
}

/**
 * name  -- name and/or id
 * type  -- input type or specialized type
 * label -- translated
 * 
 * 
 */
function input($params = array())
{
	$type = isset($params['type']) ? $params['type'] : '';
	if(empty($type)) return;
	
	$name  = isset($params['name'])  ? $params['name']  : '';
	$id    = isset($params['id'])    ? $params['id']    : $name; // todo: might not be a correct thing to do
	$label = isset($params['label']) ? $params['label'] : ucfirst($name);
	$value = isset($params['value']) ? $params['value'] : '';
	
	
$output = <<<output

<!-- $label -->
<div class="control-group">
	<label class="control-label" for="$name">$label</label>
	<div class="controls">
output;
$control = '';

	switch($type)
	{
		/*-- text, password --*/
		case 'text':
		case 'password':
			$control = <<<control

		<input class="input-block-level" type="$type" name="$name" id="$id" value="$value" placeholder="$label">
control;
			break;

		/*-- textarea --*/
		case 'textarea':
			$control = <<<control

		<textarea class="input-block-level" name="$name" id="$id" placeholder="$label" rows="10">$value</textarea>
control;
			break;
			
		/*-- tags --*/
		case 'tags':
			$control = <<<control

		<input class="input-block-level" type="text" data-provide="tag" name="$name" id="$id" value="$value" placeholder="$label">
control;
			break;
		/*-- datetime --*/
		case 'datetime':
			$id_date   = $id.'_date';
			$id_time   = $id.'_time';
			$name_date = $name.'_date';
			$name_time = $name.'_time';
			
			// assumed is a string value from something like date(Y-m-d H:i)
			$value_date = date("Y-m-d" , strtotime($value)); // $date = DateTime::createFromFormat('j-M-Y', '15-Feb-2009');
			$value_time = date("H:i"   , strtotime($value)); 
			
			$control = <<<control

		<div class="input-append datepicker">
			<input class="span2" type="text" id="$id_date" name="$name_date" value="$value_date" data-format="yyyy-mm-dd" >
			<span class="add-on"><i class="icon-calendar"></i></span>
		</div>
		<div class="input-append timepicker">
			<input class="span1" type="text" id="$id_time" name="$name_time" value="$value_time" data-format="hh:mm" value="00:00">
			<span class="add-on"><i class="icon-time"></i></span>
		</div>
control;
			break;
		/*-- file --*/
		case 'file':
			$control = <<<control

		<input id="$id" name="$name" type="file" class="filestyle">
control;
			break;
		/*-- select --*/
		case 'select':
			$values  = isset($params['values']) ? $params['values'] : array();
			$options = makeOptions($values, $value);
			$control = <<<control

<select id="$id" name="$name">$options
</select>
control;
			break;
		/*-- checkbox --*/
		case 'checkbox':
			$values     = isset($params['values']) ? $params['values'] : array();
			$checkboxes = makeCheckboxes($name, $values, explode(',',$value));
			$control    = <<<control

$checkboxes
control;
			break;
		/*-- radio --*/
		case 'radio':
			$values  = isset($params['values']) ? $params['values'] : array();
			$radios  = makeRadios($name, $values, $value);
			$control = <<<control

$radios
control;
			break;
		/*-- default --*/
		default:
			$control = '<!-- No specific type set -->';
	}

$output .= $control;
$output .= <<<output

	</div>
</div>
<!-- /$label -->

output;

return $output;
}
?>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Video Platform</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
		<!--<link rel="shortcut icon" type="image/x-icon" href="assets/favicon.ico">-->
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="assets/bootstrap-datetimepicker-0.0.11/css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="assets/bootstrap-tag-master/bootstrap-tag-custom.css">
		<link rel="stylesheet" href="assets/style.css">
    </head>
    <body>
		<!-- /navbar -->
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="brand" href="index.html">Novum</a>
					<div class="nav-collapse collapse">
						<ul class="nav">
						<!--
							<li class="active"><a href="index.html"><i class="icon-white icon-home"></i> Home</a></li>
							<li class=""><a href="archief.html"><i class="icon-white icon-folder-close"></i> Archief</a></li>
							<li class=""><a href="perslijsten.html"><i class="icon-white icon-list"></i> Perslijsten</a></li>
							<li class=""><a href="beheer.html"><i class="icon-white icon-cog"></i> Beheer</a></li>
						-->
						</ul>
						<ul class="nav pull-right">
						<!--
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class=" icon-white icon-user"></i> Achmea <b class="caret"></b>
								</a>
								<ul class="dropdown-menu">
									<li><a href="#"><i class="icon-user"></i> Profiel</a></li>
									<li><a href="#"><i class="icon-cog"></i> Instellingen</a></li>
									<li><a href="#"><i class="icon-off"></i> Uitloggen</a></li>
								</ul>
							</li>
						-->
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- /navbar -->
		
		<!-- header -->
		<header>
			<div class="container">
				<h2>Home</h2>
				<p>Welkom Achmea! Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec turpis nec nisi dignissim tempor nec vitae neque.</p>
				<!-- nav-tabs -->
				<ul class="nav nav-tabs">
					<li class="active"><a href="#">Gebruikers</a></li>
					<li><a href="#">Persberichten</a></li>
					<li><a href="#">Persvragen</a></li>
					<li><a href="#">Koppelingen</a></li>
					<li><a href="#">Organisaties</a></li>
					<li><a href="#">Admins</a></li>
					<li><a href="#">Pricing</a></li>
					<li><a href="#">Zoeken</a></li>
					<li><a href="#">Exports</a></li>
				</ul>
				<!-- /nav-tabs -->
			</div>
		</header>
		<!-- /header -->
		
		<!-- container -->
		<div class="container">
			<form class="form-horizontal">
				<h3>Aanmaken Video</h3>
				<div class="row">
					<div class="span6">
						<?php echo input(array(
							'type' => 'text',
							'label' => 'Email',
							'name' => 'email',
							'value' => 'Lorem ipsum.'
							)); ?>
						<?php echo input(array(
							'type' => 'password',
							'label' => 'Password',
							'name' => 'password'
							)); ?>
						<?php echo input(array(
							'type' => 'textarea',
							'label' => 'Message',
							'name' => 'message',
							'value' => 'Lorem ipsum dolor sit amet.'
							)); ?>
					</div>
					<div class="span6">
						<?php echo input(array(
							'type' => 'tags',
							'label' => 'Tags',
							'name' => 'tags',
							'value' => 'Lorem,ipsum,dolor,sit,amet'
							)); ?>
						<?php echo input(array(
							'type' => 'datetime',
							'label' => 'Date',
							'name' => 'date'
							));
						?>
						<?php echo input(array(
							'type' => 'file',
							'label' => 'Upload file',
							'name' => 'file',
							'date' => date('Y-m-d H:i')
						));
						?>
						<?php echo input(array(
							'type' => 'select',
							'label' => 'Combobox',
							'name' => 'select',
							'values' => array(
								'key1' => 'Key 1',
								'key2' => 'Key 2',
								'key3' => 'Key 3'
							),
							'value' => 'key2'
						));
						?>
						<?php echo input(array(
							'type' => 'checkbox',
							'label' => 'Checkboxes',
							'name' => 'checkbox',
							'values' => array(
								'val1' => 'Value 1',
								'val2' => 'Value 2',
								'val3' => 'Value 3'
							),
							'value' => 'val2,val3' // todo: comma-separated or array... :p
						));
						?>
						<?php echo input(array(
							'type' => 'radio',
							'label' => 'Le Radio Face',
							'name' => 'radio',
							'values' => array(
								'radio_1' => 'Le description 1',
								'radio_2' => 'Le description 2',
								'radio_3' => 'Le description 3'
							),
							'value' => 'radio_3'
						));
						?>
					</div>
				</div>
				
				<div class="form-actions">
					<div class="pull-right">
						<a class="btn"><i class="icon-eye-open"></i> Preview</a>
						<button type="submit" class="btn"><i class="icon-hdd"></i> Opslaan</button>
						<button type="submit" class="btn btn-primary"><i class="icon-check icon-white"></i> Volgende</button>
						<button type="submit" class="btn btn-primary"><i class="icon-share icon-white"></i> Publiceren</button>
					</div>
				</div>
			</form>
		</div>
		<!-- /container -->
		<!-- footer -->
		<footer class="footer">
			<div class="container">
				<p>Designed by <a href="http://www.datiq.com">Datiq B.V.</a> 2013</p>
			</div>
		</footer>
		<!-- /footer -->
        <!--
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="jquery/jquery-1.9.0.min.js"><\/script>')</script>
    	-->
    	<script src="assets/jquery/jquery-1.9.0.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
		<script src="assets/bootstrap-tag-master/js/bootstrap-tag.js"></script>
		<script src="assets/bootstrap-filestyle.js"></script>
		<script src="assets/bootstrap-datetimepicker-0.0.11/js/bootstrap-datetimepicker.min.js"></script>
	    <script>
			var now = new Date();
			$('a[data-toggle="tooltip"]').tooltip();
			$('.timepicker').datetimepicker({ pickDate: false, pickSeconds : false });
			$('.datepicker').datetimepicker({ pickTime: false, startDate :  now  });
	    </script>
    </body>
</html>