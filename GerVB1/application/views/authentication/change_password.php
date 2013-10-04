<p>&nbsp;</p>
<div class="container">
	<div class="row">
		<div class="span4 offset4 well">
			<legend>Change Password</legend>
			{message}
			<?php echo form_open("authentication/change_password");?>
			<div class="input-block-level">
				<input type="password" id="old" class="input-block-level" name="old" placeholder="Old Password">
			</div>
			<div class="input-block-level">
				<input type="password" id="new" class="input-block-level" name="new" placeholder="New Password">
			</div>
			<div class="input-block-level">
				<input type="password" id="new_confirm" class="input-block-level" name="new_confirm" placeholder="Confirm New Password">
			</div>
			<button type="submit" name="submit" class="btn btn-primary btn-block"><?php echo lang('change_password_submit_btn'); ?></button>
			<?php echo form_close();?>
		</div>
	</div>
</div>