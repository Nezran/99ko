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

## Préchauffage...
define('ROOT', '../');
include_once(ROOT.'common/common.php');
include_once(COMMON.'administrator.class.php');
$administrator = $core->createAdministrator();
## Variables
$msg = (isset($_GET['msg'])) ? $_GET['msg'] : '';
$msgType = (isset($_GET['msgType'])) ? $_GET['msgType'] : '';
$pageTitle = $runPlugin->getInfoVal('name');
## Mode login
if($administrator->isAuthorized() && $core->detectAdminMode() == 'login'){
	// hook
	eval($core->callHook('startAdminLogin'));
	// on bloque l'authentification si le fichier install est présent
	$temp = $core->check();
	if($core->getConfigVal('debug') == 0 && isset($temp[2])){
		$msg = $core->lang('Please delete the install.php file before logging');
		include_once('login.php');
	}
	// authentification
	elseif($administrator->login($_POST['adminEmail'], $_POST['adminPwd'])){
		header('location:index.php');
		die();
	}
	else{
		$msg = $core->lang('Incorrect password');
		include_once('login.php');
	}
	// hook
	eval($core->callHook('endAdminLogin'));
}
## Mode logout
elseif($administrator->isAuthorized() && $core->detectAdminMode() == 'logout'){
	$administrator->logout();
	header('location:index.php');
	die();
}
## Mode session KO
if(!$administrator->isLogged()){
	include_once('login.php');
}
## Mode plugin
elseif($core->detectAdminMode() == 'plugin'){
	// hook
	eval($core->callHook('startAdminIncludePluginFile'));
	// On inclut le fichier des traitements admin
	include($runPlugin->getAdminFile());
	// Plugin standard (un seul template)
	if(!is_array($runPlugin->getAdminTemplate())) include($runPlugin->getAdminTemplate());
	// Plugin avec onglets
	if($runPlugin->useAdminTabs()){
		include_once(ROOT.'admin/header.php');
		foreach($runPlugin->getAdminTemplate() as $k=>$v){
			echo '<div class="tab" id="tab-'.$k.'">';
			include_once($v);
			echo '</div>';
		}
		include_once(ROOT.'admin/footer.php');
	}
	// hook
	eval($core->callHook('endAdminIncludePluginFile'));
}
?>