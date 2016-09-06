<?php defined('ROOT') OR exit('No direct script access allowed'); ?>

<form id="configForm" method="post" action="index.php?p=configmanager&action=save" autocomplete="off">
  <?php show::adminTokenField(); ?>
  <p>
      <label><?php echo $core->lang("Lang"); ?></label><br>
      <select name="lang">
	    <?php foreach($core->getLangs() as $k=>$v){ ?>
	    <option <?php if($v == $core->getConfigVal('siteLang')){ ?>selected<?php } ?> value="<?php echo $v; ?>"><?php echo $v; ?></option>
	    <?php } ?>
      </select>
  </p>
  <p>
      <label><?php echo $core->lang("Default plugin"); ?> (public)</label><br>
      <select name="defaultPlugin">
	    <?php foreach($pluginsManager->getPlugins() as $plugin) if($plugin->getAdminFile() && $plugin->getConfigVal('activate') && $plugin->getPublicFile()){ ?>
	    <option <?php if($plugin->getIsDefaultPlugin()){ ?>selected<?php } ?> value="<?php echo $plugin->getName(); ?>"><?php echo $plugin->getInfoVal('name'); ?></option>
	    <?php } ?>
      </select>
  </p>
  <p>
      <label><?php echo $core->lang("Default plugin"); ?> (admin)</label><br>
      <select name="defaultAdminPlugin">
	    <?php foreach($pluginsManager->getPlugins() as $k=>$v) if($v->getConfigVal('activate') && $v->getAdminFile()){ ?>
	    <option <?php if($v->getName() == DEFAULT_ADMIN_PLUGIN){ ?>selected<?php } ?> value="<?php echo $v->getName(); ?>"><?php echo $v->getInfoVal('name'); ?></option>
	    <?php } ?>
      </select>
  </p>
  <p>
      <label><?php echo $core->lang("Site name"); ?></label><br>
      <input type="text" name="siteName" value="<?php echo $core->getConfigVal('siteName'); ?>" required />
  </p>
  <p>
      <label><?php echo $core->lang("Site description"); ?></label><br>
      <input type="text" name="siteDescription" value="<?php echo $core->getConfigVal('siteDescription'); ?>" required />
  </p>
  <p>
      <input <?php if($core->getConfigVal('hideTitles')){ ?>checked<?php } ?> type="checkbox" name="hideTitles" /> <label for="hideTitles"><?php echo $core->lang("Hide pages titles"); ?>
  </p>
  <p>
      <label><?php echo $core->lang("Theme"); ?></label><br>
      <ul class="no_bullet">
      <?php foreach($core->getThemes() as $k=>$v){ ?>
	    <li><input type="radio" name="theme" <?php if($k == $core->getConfigVal('theme')){ ?>checked<?php } ?> value="<?php echo $k; ?>" /><a target="_blank" href="<?php echo $v['authorWebsite']; ?>"><?php echo $v['name']; ?> (<?php echo $v['version']; ?>)</a></li>
	    <?php } ?>
      </ul>	    
  </p>
  <p>
  <button type="submit" class="button"><?php echo $core->lang("Save"); ?></button>
  </p>