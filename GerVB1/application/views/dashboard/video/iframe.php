<html>
<head>
<style>
html,
body,
* {
	padding : 0;
	margin : 0;
	text-align : center;
}

div.mvp-auto-player {
	margin : 0 auto;
}
</style>
</head>
<body>
<script type="text/javascript" src="{playerUrl}<?php if(isset($_GET['beta']) && !empty($_GET['beta'])) : ?>?beta=1<?php endif; ?>"></script>
<div class="mvp-auto-player"  mvp-implementation="me" mvp-player="{playerId}" mvp-video="{videoId}" <?php if(!empty($signature)) : ?>mvp-client-ip="{ip}" mvp-signature="{signature}"<?php endif; ?>></div>
</body>
</html>