<?php defined('ROOT') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="<?php show::siteLang(); ?>">
<head>
	<?php eval($core->callHook('frontHead')); ?>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php show::titleTag(); ?></title>
	<base href="<?php show::siteUrl(); ?>/" />
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
	<meta name="description" content="<?php show::metaDescriptionTag(); ?>" />
	<link rel="icon" href="theme/<?php show::theme("[id]"); ?>/favicon.gif" />
	<?php show::linkTags(); ?>
	<?php show::scriptTags(); ?>
	<?php eval($core->callHook('endFrontHead')); ?>
</head>
<body>
<div id="container">
	<div id="header">
		<div id="header_content">
			<p id="siteName"><a href="<?php show::siteUrl(); ?>"><?php show::siteName(); ?></a></p>
		</div>
		<div id="banner"></div>
	</div>
	<div id="body">
		<div id="sidebar">
			<?php eval($core->callHook('sidebar')); ?>
			<ul id="navigation">
				<?php show::mainNavigation(); ?>
			</ul>
			<?php eval($core->callHook('endSidebar')); ?>
		</div>
		<div id="content" class="<?php show::pluginId(); ?>">
			<?php show::mainTitle(); ?>