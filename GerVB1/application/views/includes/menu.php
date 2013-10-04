		<!-- /navbar -->
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<?php if( isset( $organisation ) && !empty( $organisation['logo_url'] ) ) :?>
					<a class="brand" href="/" style="margin:0; padding:5px 10px;"><img src="<?php echo $organisation['logo_url'] ?>" style="height:30px;" title="{site_name}"/> <!--{site_name}--></a>
					<?php else : ?>
					<a class="brand" href="/" style="margin:0; padding:5px 10px;"><img src="<?php echo THE_SITE_LOGO ?>" style="height:30px;" title="{site_name}"/> <!--{site_name}--></a>
					<?php endif; ?>
					<div class="nav-collapse collapse">
						<ul class="nav">
							{navigation}
								<li {active}><a href="{url}">{icon}{name}</a></li>
							{/navigation}
						</ul>
						<ul class="nav pull-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class=" icon-white icon-user"></i> {username} <b class="caret"></b>
								</a>
								<ul class="dropdown-menu">
									<li><a href="" class="profile-modal"><i class="icon-user"></i> Profile</a></li>
<!--									<li><a href="#user/preferences"><i class="icon-cog"></i> Preferences</a></li>-->
									<li><a href="authentication/logout"><i class="icon-off"></i> Logout</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- /navbar -->