<?php
/**
 * 99ko cms
 *
 * This source file is part of the 99ko cms. More information,
 * documentation and support can be found at http://99ko.org
 *
 * @package     99ko
 *
 * @author      Jonathan Coulet (contact@99ko.org)
 * @copyright   2016 Jonathan Coulet (contact@99ko.org)
 * @copyright   2015 Jonathan Coulet (contact@99ko.org) / Frédéric Kaplon (frederic.kaplon@me.com)
 * @copyright   2013-2014 Florent Fortat (florent.fortat@maxgun.fr) / Jonathan Coulet (contact@99ko.org) / Frédéric Kaplon (frederic.kaplon@me.com)
 * @copyright   2010-2012 Florent Fortat (florent.fortat@maxgun.fr) / Jonathan Coulet (contact@99ko.org)
 * @copyright   2010 Jonathan Coulet (contact@99ko.org)  
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

session_start();
define('ROOT', './');
include_once(ROOT.'common/config.php');
include_once(COMMON.'util.class.php');
include_once(COMMON.'core.class.php');
include_once(COMMON.'pluginsManager.class.php');
include_once(COMMON.'plugin.class.php');
include_once(COMMON.'show.class.php');
include_once(COMMON.'administrator.class.php');
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if($lang != 'fr') $lang = 'en';
$core = new core($lang);
if(file_exists(DATA. 'config.json')) die($core->lang('Config file already exist !'));
$administrator = new administrator();
$pluginsManager = pluginsManager::getInstance();
$msg = "";
$msgType = "";
if($core->install()){
	$plugins = $pluginsManager->getPlugins();
	if($plugins != false){
		foreach($plugins as $plugin){
		  if($plugin->getLibFile()){
			include_once($plugin->getLibFile());
			if(!$plugin->isInstalled()) $pluginsManager->installPlugin($plugin->getName(), true);
			$plugin->setConfigVal('activate', '1');
			$pluginsManager->savePluginConfig($plugin);
		  }
		}
	}
}
if(count($_POST) > 0 && $administrator->isAuthorized()){
	$adminPwd = $administrator->encrypt($_POST['adminPwd']); 
    $adminEmail = $_POST['adminEmail'];
	$config = array(
		'siteName' => "Nom du site",
		'siteDescription' => "Description du site",
		'adminPwd' => $administrator->encrypt($_POST['adminPwd']),
		'adminEmail' => $_POST['adminEmail'],
		'siteUrl' => $core->makeSiteUrl(),      
		'urlRewriting' => '0',
		'theme' => 'default',
		'siteLang' => $lang,
		'hideTitles' => '0',
		'defaultPlugin' => 'page',
		'checkUrl' => base64_decode('aHR0cDovLzk5a28ub3JnL3ZlcnNpb24='),
		'debug' => '0',
		'defaultAdminPlugin' => 'page',
		'urlSeparator' => ',',
	);
	if(!@file_put_contents(DATA. 'config.json', json_encode($config)) ||	!@chmod(DATA. 'config.json', 0666)){
		$msg = $core->lang('An error has occurred');
	    $$msgType = "error";
	}
	else{
		$_SESSION['installOk'] = true;
		header('location:admin/');
		die();
	}
}
?>
 
 <!doctype html>
<html lang="<?php echo $lang; ?>">
  <head>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>99ko - <?php echo $core->lang('Installation'); ?></title>	
	<link rel="icon" href="admin/favicon.gif">
	<link rel="stylesheet" href="admin/styles.css" media="all">
  </head>
  
  <body class="login">
	<div id="login">
	<?php show::msg($msg, $msgType); ?>
           <h1 class="text-center"><?php echo $core->lang('Installation'); ?></h1>
		   <p class="alert-box"><?php echo $core->lang('After installation, you will be redirected to the login page to set your site.'); ?></p>
           
           <form method="post" action="">   
           <?php show::adminTokenField(); ?>          
              <p>
                   <label for="adminEmail"><?php echo $core->lang('Admin email'); ?></label><br>
                   <input type="email" name="adminEmail" required="required">
                </p><p>
                   <label for="adminPwd"><?php echo $core->lang('Admin password'); ?></label><br>
                   <input type="password" name="adminPwd" id="adminPwd" required="required">
               </p><p>
					<a id="showPassword" href="javascript:showPassword()" class="button success"><?php echo $core->lang('Show password'); ?></a>
					<button type="submit" class="button success"><?php echo $core->lang('Validate'); ?></button>
			  </p>
			  <p class="just_using"><a title="<?php echo $core->lang("NoDB CMS"); ?>" target="_blank" href="http://99ko.org"><?php echo $core->lang("Just using 99ko"); ?></a>
	  </p>
            </form>
	</div>
    <script type="text/javascript">
	function showPassword(){
		document.getElementById("adminPwd").setAttribute("type", "text");
		document.getElementById("showPassword").style.display = 'none';
	}
	</script>
</body>
</html>
