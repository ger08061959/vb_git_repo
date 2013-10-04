<p>&nbsp;</p>
<div class="container">
	<div class="row">
		<div class="span4 offset4 well">
			<legend>Reset Password</legend>
			{message}
			<?php echo form_open('authentication/reset_password/' . $code);?>
			<div class="input-block-level">
				<input type="password" id="new" class="input-block-level" name="new" placeholder="New password">
			</div>
			<div class="input-block-level">
				<input type="password" id="new_confirm" class="input-block-level" name="new_confirm" placeholder="Confirm New password">
			</div>
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
			<?php echo form_hidden($csrf); ?>
			<button type="submit" name="submit" class="btn btn-primary btn-block"><?php echo lang('reset_password_submit_btn'); ?></button>
			<?php echo form_close();?>
		</div>
	</div>
</div>