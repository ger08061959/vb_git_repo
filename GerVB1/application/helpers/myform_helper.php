<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Source: http://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
if ( ! function_exists('formatBytes')){
function formatBytes($bytes, $precision = 2) { 
	$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

	$bytes = max($bytes, 0); 
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
	$pow = min($pow, count($units) - 1); 

	// Uncomment one of the following alternatives
	// $bytes /= pow(1024, $pow);
	$bytes /= (1 << (10 * $pow)); 

	return round($bytes, $precision) . ' ' . $units[$pow]; 
}
}

/**
 * makeCheckboxes
 * makeRadios
 * makeOptions
 * input( ... )
 */

if ( ! function_exists('makeCheckboxes')){
function makeCheckboxes($name, $values = array(), $value = array())
{
	/*
	echo '<pre>';
	print_r($values);
	print_r($value);
	echo '</pre>';
	//*/

	$checkboxes = '';
	$name = $name.'[]'; // post as array
	foreach($values as $key => $label)
	{
		$checked = in_array($key, $value) ? 'checked="checked"' : '';
		$checkboxes .= <<<checkbox
		
	<label class="checkbox"><input name="$name" type="checkbox" value="$key" $checked>$label</label>
checkbox;
	}
	return $checkboxes;
}}
if ( ! function_exists('makeRadios')){
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
}}
if ( ! function_exists('makeOptions')){
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
}}

/**
 * name  -- name and/or id
 * type  -- input type or specialized type
 * label -- translated
 * 
 * 
 */
if ( ! function_exists('input')){
function input($params = array())
{
	$type = isset($params['type']) ? $params['type'] : '';
	if(empty($type)) return;
	
	$name     = isset($params['name'])  ? $params['name']  : '';
	$id       = isset($params['id'])    ? $params['id']    : $name; // todo: might not be a correct thing to do
	$label    = isset($params['label']) ? $params['label'] : ucfirst($name);
	$value    = isset($params['value']) ? $params['value'] : '';
	$info     = isset($params['info'])  ? $params['info']  : '';
	$required = isset($params['rules']) && (strpos($params['rules'],'required') !== false) ? '<span style="color:red;">*</span>'  : ''; // if required exists in rules, only then is it truly required (server-side).
	
	if($info){
		$info = '<i class="icon-info-sign" data-toggle="tooltip" title="" data-original-title="'.$info.'"></i>';
	}
	
	
$output = <<<output

<!-- $label -->
<div class="control-group">
	<label class="control-label" for="$name">$label $required $info</label>
	<div class="controls">
output;
$control = '';

	switch($type)
	{
		/*-- hidden --*/
		case 'hidden':
			return <<<control
	<input class="input-block-level" type="$type" name="$name" id="$id" value="$value">
control;
		/*-- text, password --*/
		case 'text':
			$control = <<<control

		<input class="input-block-level" type="$type" name="$name" id="$id" value="$value" placeholder="$label">
control;
			break;
		case 'password':
			$control = <<<control

		<input class="input-block-level" type="$type" name="$name" id="$id" value="" placeholder="$label">
control;
			break;
		/*-- textarea --*/
		case 'textarea':
			$control = <<<control

		<textarea class="input-block-level" name="$name" id="$id" placeholder="$label" rows="5">$value</textarea>
control;
			break;
			
		/*-- tags --*/
		case 'tags':
			$control = <<<control

		<input class="input-block-level" type="text" data-type="tag" data-provide="tag-DISABLED" name="$name" id="$id" value="$value" placeholder="$label">
control;
			break;
		/*-- datetime --*/
		case 'datetime':
			$id_date   = $id.'_date';
			$id_time   = $id.'_time';
			$name_date = $name.'_date';
			$name_time = $name.'_time';
			
			// assumed is a string value from something like date(Y-m-d H:i:s)
			if(empty($value))
			{
				$value = date('Y-m-d H:i:s');
			}
			$date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
			// $timestamp = $date->getTimestamp();
			
			// $value_date = date("Y-m-d" , strtotime($value));
			// $value_time = date("H:i:s" , strtotime($value)); 
			
			$control = <<<control
		<div class="input-append datetimepicker">
			<input class="span2 input-block-level" type="text" id="$id" name="$name" value="$value" data-format="yyyy-MM-dd hh:mm:ss" />
			<span class="add-on"><i class="icon-calendar"></i></span>
		</div>
control;
/*
		<div class="input-append datepicker">
			<input class="span2" type="text" id="$id_date" name="$name_date" value="$value_date" data-format="yyyy-mm-dd" >
			<span class="add-on"><i class="icon-calendar"></i></span>
		</div>
		<div class="input-append timepicker">
			<input class="span1" type="text" id="$id_time" name="$name_time" value="$value_time" data-format="hh:mm" value="00:00">
			<span class="add-on"><i class="icon-time"></i></span>
		</div>
*/
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
			if(is_string($value)) $value = explode(',',$value);
			$checkboxes = makeCheckboxes($name, $values, $value);
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
		/*-- echo --*/
		case 'echo':
			// duration is a special case to echo, because we do not transform duration in Edit actions.
			// however, in View actions... most of the values are already transformed...
			if(isset($params['display']) && in_array($params['display'], array('duration') ))
			{
				// transform, useful for video->duration (converting from seconds to hh:mm:ss)
				// Note: this also escapes HTML (sometimes you do NOT want this at all)
				$value = display_value($name, null, $params);
			}
			$control = '<span class="help-inline" style="padding-top:5px;">'.$value.'</span>'; // // todo values --> key => value;
			break;
		/*-- default --*/
		default:
			try {
				require( dirname(__FILE__) . '/custom/'.$type.'.php' );
			} catch(Exception $e){
				$control = '<!-- No specific type set -->';
			}
	}

$output .= $control;
$output .= <<<output

	</div>
</div>
<!-- /$label -->

output;

return $output;
}}


if( ! function_exists('display_value')){
	// $key        - the property to print
	// $model      - an array with values, instance from a database
	// $properties - an element from the a $model->fields
	function display_value($key, $model, $properties)
	{
		// $model has a true value, however $properties has a DEFAULT value.
		$value = '';
		$value = isset($properties['value']) ? $properties['value'] : $value;
		$value = !empty($model) && is_array($model) && isset($model[$key]) ? $model[$key] : $value;
		$value = isset($model->$key) ? $model->$key : $value;
		
		if(isset($properties['many']) && $properties['many']) // has-many
		{
			// $value  = isset($model[$key]) ? $model[$key] : (isset($properties['value']) ? $properties['value'] : '');
			$values = $properties['values']; // required

			$new_values = array();
			foreach($value as $i)
				$new_values[] = $values[$i];
			
			$value = implode(', ', $new_values);
			
		}
		elseif(isset($properties['display']) && $properties['display'] == 'url')
		{
			return '<a href="'.$value.'" />'.$value.'</a>';
		}
		elseif(isset($properties['display']) && $properties['display'] == 'image')
		{
			return '<img src="'.$value.'" style="height:20px;" />';
		}
		elseif(isset($properties['display']) && $properties['display'] == 'duration')
		{
			if(is_numeric($value))
			{
				// convert seconds to hh:mm:ss
				$t = round($value);
				return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
			}
			return $value;
		}
		else
		{
			// $properties has the value => label mappings, e.g. 0 - Inactive, 1 - Active, or even for foreign objects
			if(isset($properties['values']))
			{
				$values = $properties['values'];
				$value = isset( $values[$value] ) ? $properties['values'][$value] : $value;
			}
		}
		
		if(isset( $properties['html_escape']) && !$properties['html_escape'])
			return $value; // don't do anything
		else
			$value = html_escape($value);
		return $value;
		/*
		$field['value'] = isset($result) && isset($result[$key]) ? $result[$key] : (isset($field['value']) ? $field['value'] : '');
		$field['value'] = html_escape($field['value']);
		*/
	}
}
/* End of file form_helper.php */
/* Location: ./application/helpers/form_helper.php */