<div class="container">
	<div class="row">
	<?php if(isset($_GET['flash']) && !empty($_GET['flash'])) : ?>
		<h4><?php echo $the_video['title']; ?></h4>
		<object width="800" height="450">
		<param name="movie" value="{url}"></param>
		<param name="allowFullScreen" value="true"></param>
		<param name="allowscriptaccess" value="always"></param>
		<embed src="{url}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="800" height="450"></embed>
		</object>
	<?php else : ?>
		<?php if(isset($_GET['beta']) && !empty($_GET['beta'])) : ?>
			<h4><?php echo $the_video['title']; ?> <span class="label label-important">beta</span></h4>
			<script type="text/javascript" src="{playerUrl}?beta=1"></script>
			<div class="mvp-auto-player" mvp-implementation="me" mvp-player="{playerId}" mvp-video="{videoId}" <?php if(!empty($signature)) : ?>mvp-client-ip="{ip}" mvp-signature="{signature}"<?php endif; ?>></div>
		<?php else : ?>
			<h4><?php echo $the_video['title']; ?></h4>
			<iframe type="text/html" width="800" height="450" src="{iframeUrl}" frameborder="0" scrolling="no" style="overflow:hidden;" allowfullscreen webkitAllowFullScreen mozallowfullscreen allowFullScreen>Video cannot be shown in this browser.</iframe>
		<?php endif; ?>
	<?php endif; ?>
	</div>
</div>