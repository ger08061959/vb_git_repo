<p>&nbsp;</p>
<div class="container">
	<div class="row">
		<div class="span4 offset4 well">
			<legend>Forgot Password</legend>
			{message}
			<p><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>
			<?php echo form_open("authentication/forgot_password");?>
			<div class="input-prepend input-block-level">
				<span class="add-on"><i class="icon-envelope"></i></span>
				<input type="text" id="email" class="input-block-level" name="email" placeholder="Email">
			</div>
			<button type="submit" name="submit" class="btn btn-primary btn-block"><?php echo lang('forgot_password_submit_btn'); ?></button>
			<?php echo form_close();?>
		</div>
	</div>
</div>