<?php if ($uc->isLoggedIn()) { ?>
<nav class="dark-bg">
	<div class="container">
		<ul class="float-right margin-0 padding-0">
			<li><a href="./?route=logout" class="">Logout</a></li>
		</ul><ul class="margin-0 padding-0">
			<li><a href="./" class="nav-user-name"><?php echo $_SESSION['username']; ?></a></li>
			<li><a href="./" class="<?php if($route=='/'){echo' active';} ?>">Home</a></li>
			<li><a href="./?route=favorites"  class="<?php if($route=='favorites'){echo' active';} ?>">Favorites</a></li>
			<li><a href="./?route=statistics"  class="<?php if($route=='statistics'){echo' active';} ?>">Anime Statistics</a></li>
		</ul>
		
	</div>
</nav>
<?php } ?>
<main>