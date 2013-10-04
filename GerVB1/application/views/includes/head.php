<!DOCTYPE html>
<html class="no-js">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>{site_name} &raquo; {title} | {videobank_name}</title>
		<meta name="description" content="{description}">
		<meta name="viewport" content="width=device-width">
		<!--<link rel="shortcut icon" type="image/x-icon" href="assets/favicon.ico">-->
		<!-- Font Awesome
		<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
		-->
		<!-- Stylesheets -->
		<!--<link rel="stylesheet" href="{base_url}assets/bootstrap/css/bootstrap.min.css">-->
		<?php if(isset($organisation) && isset($organisation['theme']) ): ?>
			<?php if($organisation['theme']=='custom'): ?>
			<!-- organisation/theme : custom -->
			<link rel="stylesheet/less" href="{base_url}assets/bootstrap/less/bootstrap.less">
			<link rel="stylesheet/less" href="{base_url}assets/bootstrap/less/responsive.less">
			<?php elseif( !empty($organisation['theme']) ) : ?>
			<!-- organisation/theme : [<?php echo $organisation['theme']; ?>] -->
			<link rel="stylesheet" href="{base_url}assets/bootstrap-<?php echo $organisation['theme']; ?>/css/bootstrap.min.css">
			<?php else : ?>
			<!-- organisation/theme : default -->
			<link rel="stylesheet" href="{base_url}assets/bootstrap/css/bootstrap.min.css">
			<?php endif; ?>
		<?php else : ?>
		<!-- default bootstrap -->
		<link rel="stylesheet" href="{base_url}assets/bootstrap-<?php echo THE_SITE_THEME; ?>/css/bootstrap.min.css">
		<?php endif; ?>
		<link rel="stylesheet" href="{base_url}assets/bootstrap-datetimepicker-0.0.11/css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="{base_url}assets/bootstrap-tag-master/bootstrap-tag-custom.css">
		<link rel="stylesheet" href="{base_url}assets/jquery.tablesorter/style.css">
		<link rel="stylesheet" href="{base_url}assets/style.css">

		<base href="{base_url}"></base>
	</head>
	<body>
		<script src="{base_url}assets/jquery/jquery-1.9.0.min.js"></script>
		<!--<script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script><!-- debugging jquery -->
		<script src="{base_url}assets/bootstrap/js/bootstrap.min.js"></script>
		<script src="{base_url}assets/bootstrap-tag-master/js/bootstrap-tag.js"></script>
		<script src="{base_url}assets/bootstrap-datetimepicker-0.0.11/js/bootstrap-datetimepicker.min.js"></script>
		<script src="{base_url}assets/jquery.form.min.js"></script>
		<script src="{base_url}assets/Chart.js/Chart.min.js"></script>
		<script src="{base_url}assets/flot/jquery.flot.js"></script>
		<script src="{base_url}assets/jquery.tablesorter/jquery.tablesorter.min.js"></script>
		<script src="{base_url}assets/less-1.4.1.min.js"></script>
		<script src="{base_url}assets/application.controls.custom.js"></script>
