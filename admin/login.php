<?php defined('ROOT') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="<?php show::siteLang(); ?>">
  <head>
	<?php eval($core->callHook('adminHead')); ?>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>99ko - <?php echo $core->lang('Login'); ?></title>	
	<link rel="icon" href="favicon.gif">
	<?php show::linkTags(); ?>
	<link rel="stylesheet" href="styles.css" media="all">
	<?php show::scriptTags(); ?>
	<script type="text/javascript" src="scripts.js"></script>
	<?php eval($core->callHook('endAdminHead')); ?>	
  </head>
  <body class="login">
  <div id="login">
	<?php show::msg($msg, 'error'); ?>
	<h1><?php echo $core->lang('Login'); ?></h1>
	<form method="post" action="index.php?action=login">   
	  <?php show::adminTokenField(); ?>          
	  <p><label for="adminEmail"><?php echo $core->lang('Email'); ?></label><br>
	  <input type="email" id="adminEmail" name="adminEmail" placeholder="your@mail.com" required></p>
	  <p><label for="adminPwd"><?php echo $core->lang('Password'); ?></label>
	  <input type="password" id="adminPwd" name="adminPwd" placeholder="*******" required></p>
	  <p><a href="../" class="button alert"><?php echo $core->lang('Quit'); ?></a> 
	  <button type="submit" class="button success"><?php echo $core->lang('Validate'); ?></button></p>
	  <p class="just_using"><a title="<?php echo $core->lang("NoDB CMS"); ?>" target="_blank" href="http://99ko.org"><?php echo $core->lang("Just using 99ko"); ?></a>
	  </p>
	</form>
  </div>
  <?php eval($core->callHook('endAdminBody')); ?>
  </body>
</html>