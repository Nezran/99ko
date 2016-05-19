<?php
defined('ROOT') OR exit('No direct script access allowed');

$action = (isset($_GET['action'])) ? urldecode($_GET['action']) : '';
$msg = (isset($_GET['msg'])) ? urldecode($_GET['msg']) : '';
$msgType = (isset($_GET['msgType'])) ? $_GET['msgType'] : '';
$error = false;
$htaccess = @file_get_contents(ROOT.'.htaccess');
$htaccess = htmlspecialchars($htaccess, ENT_QUOTES, 'UTF-8');
$rewriteBase = str_replace(array('index.php', 'install.php', 'admin/'), '', $_SERVER['PHP_SELF']);
$passwordError = false;

switch($action){
	case 'save':
		if($administrator->isAuthorized()){
			$config = array(
				'siteName' => (trim($_POST['siteName']) != '') ? trim($_POST['siteName']) : 'Démo',
				'siteDescription' => (trim($_POST['siteDescription']) != '') ? trim($_POST['siteDescription']) : 'Un site propulsé par 99Ko',
				'adminEmail' => trim($_POST['adminEmail']),
				'siteUrl' => (trim($_POST['siteUrl']) != '') ? trim($_POST['siteUrl']) : $core->getConfigVal('siteUrl'),
				'theme' => $_POST['theme'],
				'defaultPlugin' => $_POST['defaultPlugin'],
				'urlRewriting' => (isset($_POST['urlRewriting'])) ? '1' : '0',
				'siteLang' => $_POST['lang'],
				'hideTitles' => (isset($_POST['hideTitles'])) ? '1' : '0',
				'gzip' => (isset($_POST['gzip'])) ? '1' : '0',
				'debug' => (isset($_POST['debug'])) ? '1' : '0',
				'defaultAdminPlugin' => $_POST['defaultAdminPlugin'],
				'urlSeparator' => $_POST['urlSeparator'],
			);
			if(trim($_POST['_adminPwd']) != ''){
				if(trim($_POST['_adminPwd']) == trim($_POST['_adminPwd2'])) {
					$config['adminPwd'] = $administrator->encrypt(trim($_POST['_adminPwd']));
					$_SESSION['admin'] = $config['adminPwd'];
				}
				else $passwordError = true;
			}
			if($passwordError){
				$msg = $core->lang("The password is different from his confirmation.");
				$msgType = 'error';
			}
			elseif(!util::isEmail(trim($_POST['adminEmail']))){
				$msg = $core->lang("Invalid email.");
				$msgType = 'error';
			}
			elseif(!$core->saveConfig($config)){
				$msg = $core->lang("An error occurred while saving the changes");
				$msgType = 'error';
			}
			else{
				$msg = $core->lang("The changes have been saved.");
				$msgType = 'success';
			}
			@file_put_contents(ROOT.'.htaccess', str_replace('¶m', '&param', $_POST['htaccess']));
			$pluginsManager->intiPluginsCache(array(), true);
			header('location:index.php?p=configmanager&msg='.urlencode($msg).'&msgType='.$msgType);
			die();
		}
		break;
}
?>