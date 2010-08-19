<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <?php include_stylesheets() ?>
  <?php include_javascripts() ?>
</head>

<body>

<h1>
	 <?php echo link_to('Minesweeper', 'user/index') ?>
	 for
	 <a href="http://freshplanet.com/">FreshPlanet</a></h1>

<?php if ($sf_user->hasFlash('notice')): ?>
	 <div class="flash_notice"><?php echo $sf_user->getFlash('notice') ?></div>
<?php endif ?>
 
<?php if ($sf_user->hasFlash('error')): ?>
	 <div class="flash_error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>

<?php if ($sf_user->isAuthenticated()): ?>
<p class="menu">
	 Logged in as <?php echo link_to($sf_user->getAttribute('name'), 'user/index') ?> |
	 <?php //echo link_to('Game board', 'game') ?> |
	 <?php //echo link_to('Hiscores', 'hiscore') ?> |
	 <?php echo link_to('Logout', 'user/logout') ?>
</div>
<?php endif ?>

<?php echo $sf_content ?>

<p class="bottom">
	 A first <a href="http://www.symfony-project.org/">Symfony</a> try
	 by <a href="http://www.glop.org/">Laurent Raufaste</a>
</p>

</body>
</html>
