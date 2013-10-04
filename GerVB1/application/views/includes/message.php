<?php if(isset($message) && !empty($message)) : ?>
				<!-- alert -->
				<div class="alert alert-<?php echo $message['type'] ?>">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong><?php echo $message['title'] ?></strong> <?php echo $message['text'] ?>
				</div>
				<!-- /alert -->
<?php endif; ?>