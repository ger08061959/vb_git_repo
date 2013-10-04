<html>
<body>
	<p><?php echo sprintf(lang('email_activate_heading'), $identity);?></p>
	<p><?php echo sprintf(lang('email_activate_subheading'), anchor('authentication/activate/'. $id .'/'. $activation, lang('email_activate_link')));?></p>
</body>
</html>