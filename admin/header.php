<?php defined('ROOT') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="<?php show::siteLang(); ?>">
  <head>
	<?php eval($core->callHook('adminHead')); ?>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>99ko - <?php echo $core->lang('Backend'); ?></title>	
	<link rel="icon" href="favicon.gif">
	<?php show::linkTags(); ?>
	<link rel="stylesheet" href="styles.css" media="all">
	<?php show::scriptTags(); ?>
	<script type="text/javascript" src="scripts.js"></script>
	<?php eval($core->callHook('endAdminHead')); ?>	
  </head>
  <body>
	<div id="alert"><?php show::msg($msg, $msgType); ?></div>
	<div id="container">
		<div id="header">
			<div id="header_content">	
			  <ul>
				<li><h1><a class="active" href="./"><img src="home.png" alt="<?php echo $core->lang('Administration'); ?>" /></a></h1></li>
				<li><a target="_blank" href="../"><?php echo $core->lang('Show website'); ?></a></li>
				<li><a href="index.php?action=logout&token=<?php echo administrator::getToken(); ?>"><?php echo $core->lang('Logout'); ?></a></li>
				<?php eval($core->callHook('adminSecondaryMenu')); ?>
			  </ul>
			</div>
		</div>
		<div id="body">
		  <div id="sidebar">
			<ul id="navigation">
			  <?php foreach($pluginsManager->getPlugins() as $k=>$v) if($v->getConfigVal('activate') && $v->getAdminFile()){ ?>
			  <li><a class="" href="index.php?p=<?php echo $v->getName(); ?>"><?php echo $core->lang($v->getInfoVal('name')); ?></a></li>
			  <?php } ?>
			</ul>
			<div id="notifs">
			  <?php foreach($core->check() as $k=>$v){ ?>
			  <?php echo show::msg($v['msg'], $v['type']); ?>
			  <?php } ?>
			  <?php eval($core->callHook('adminNotifications')); ?>
			</div>
			<p class="just_using">
			  <a title="<?php echo $core->lang("NoDB CMS"); ?>" target="_blank" href="http://99ko.org"><?php echo $core->lang("Just using 99ko"); ?> <?php echo VERSION; ?></a>
			</p>
		  </div>
		  <div id="content_mask">
			<div id="content" class="<?php echo $runPlugin->getName(); ?>-admin">	
			  <h2><?php echo $core->lang($pageTitle); ?></h2>        
			  <?php if($core->detectAdminMode() == 'plugin' && $runPlugin->useAdminTabs()){ ?>
			  <ul class="tabs">
				<?php foreach($runPlugin->getAdminTabs() as $k=>$v){ ?>
				<li><a class="button" href="#tab-<?php echo $k; ?>"><?php echo $core->lang($v); ?></a></li>
				<?php } ?>
			  </ul>
			  <div class="tabs-content">
			  <?php } ?>