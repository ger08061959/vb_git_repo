<?php echo form_open("authentication/forgot_password");?>
	<div class="container">
		<div class="row">
			<div class="span4 offset4 well">
				<fieldset>
					<legend>Forgot Password<img class="pull-right" src="<?php echo THE_SITE_LOGO; ?>" WIDTH=55 HEIGHT=35 /></legend>
					{message}
					<p><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>
					<div class="input-prepend input-block-level">
						<span class="add-on"><i class="icon-envelope"></i></span>
						<input type="text" id="email" class="input-block-level" name="email" placeholder="Email">
					</div>
					<button type="submit" name="submit" class="btn btn-primary btn-block"><?php echo lang('forgot_password_submit_btn'); ?></button>
				</fieldset>
			</div>
		</div>
	</div>
<?php echo form_close();?>
		