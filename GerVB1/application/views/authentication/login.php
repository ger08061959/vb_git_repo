<script>
function destroyLessCache(pathToCss) { // e.g. '/css/' or '/stylesheets/'
 
  if (!window.localStorage /*|| !less || less.env !== 'development'*/) {
    return;
  }
  var host = window.location.host;
  var protocol = window.location.protocol;
  var keyPrefix = protocol + '//' + host + pathToCss;
  
  for (var key in window.localStorage) {
    if (key.indexOf(keyPrefix) === 0) {
      delete window.localStorage[key];
    }
  }
}
destroyLessCache('/assets/bootstrap/less/');
</script>
<div class="container">
	<div class="row">
		<div class="span4 offset4 well">
			<legend>Please Sign In <img class="pull-right" src="<?php echo THE_SITE_LOGO; ?>" WIDTH=55 HEIGHT=35 /></legend>
			{message}
			<?php echo form_open("authentication/login"); ?>
				<div class="input-prepend input-block-level">
					<span class="add-on"><i class="icon-user"></i></span>
					<input type="text" id="identity" class="input-block-level" name="identity" placeholder="<?php echo lang('login_identity_label');?>">
				</div>
				<div class="input-prepend input-block-level">
					<span class="add-on"><i class="icon-lock"></i></span>
					<input type="password" id="password" class="input-block-level" name="password" placeholder="<?php echo lang('login_password_label');?>">
				</div>
				<label class="checkbox">
					<input type="checkbox" id="remember" name="remember" value="1"> <?php echo lang('login_remember_label');?>
				</label>
				<button type="submit" name="submit" class="btn btn-primary btn-block"><?php echo lang('login_submit_btn'); ?></button>
			<?php echo form_close();?>
			
			<p><a href="authentication/forgot_password"><?php echo lang('login_forgot_password');?></a></p>
		</div>
	</div>
</div>