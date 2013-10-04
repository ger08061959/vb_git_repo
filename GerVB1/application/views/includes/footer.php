		</div>
		<!-- /container -->
		<!-- footer -->
		<footer class="footer">
			<div class="container">
				<p>Designed by <a href="http://www.datiq.com">Datiq B.V.</a> 2013</p>
			</div>
		</footer>
		
<!-- Modal -->
<div id="confirmation-modal" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Confirmation required</h3>
	</div>
	<div class="modal-body">
		<p>Are you sure?</p>
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal">Cancel</a>
		<a href="" class="btn btn-primary">Yes</a>
	</div>
</div>
<!-- /Modal -->

<!-- Modal -->
<div id="profile-modal" class="modal hide fade" tabindex="-1">
	<form class="form-horizontal ajax-form-simple" style="margin:0;padding:0;" data-action="dashboard/profile/update" action="dashboard/profile/update" method="post" autocomplete="off">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Profile</h3>
	</div>
	<div class="modal-body">
		<div class="my-message"></div>
		<!-- Profile -->
		<?php
			$profileFields = $this->user_model->fields;
			$profileFields['email']['type'] = 'echo';
			$profileKeys = array('first_name', 'last_name', 'phone', 'email', 'business_unit');
			foreach($profileKeys as $key){
					$field = $profileFields[$key];
					$field['name']  = $key;
					$field['value'] = isset($user) && isset($user[$key]) ? $user[$key] : (isset($field['value']) ? $field['value'] : '');
					$field['value'] = html_escape($field['value']);
					echo input($field);
			}
		?>
		<!-- Password -->
		<p>
			<strong>Update password</strong>
			<i class="icon-info-sign" data-toggle="tooltip" title="Use the following fields to update your password. If you don't want to change your password, just leave the fields empty."></i>
		</p>
		<?php 
			$passwordKeys = array('password', 'password_confirm');
			foreach($passwordKeys as $key){
					$field = $profileFields[$key];
					$field['name']  = $key;
					$field['value'] = '';
					echo input($field);
			}
			?>
		
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal">Cancel</a>
		<button type="submit" class="btn btn-primary" data-loading-text="Updating ..." >Update</button>
	</div>
	</form>
</div>
<script>
	$('#profile-modal').modal({
		keyboard : true,
		show: false,
		backdrop: true // 'static' iff true modal
	});
	
	$('a.profile-modal').click(function(e){
		e.preventDefault();
		// $('#confirmation-modal a.btn-primary').attr('href', $(this).attr('href'));
		$('#profile-modal').modal('show');
	});
	
	$('#profile-modal a.btn-primary').click(function(e){
	
	});
</script>
<!-- /Modal -->