<?php
defined('ROOT') OR exit('No direct script access allowed');

$action = (isset($_GET['action'])) ? urldecode($_GET['action']) : '';
$msg = (isset($_GET['msg'])) ? urldecode($_GET['msg']) : '';
$msgType = (isset($_GET['msgType'])) ? $_GET['msgType'] : '';

switch($action){
	case '':
		$priority = array(
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4,
			5 => 5,
			6 => 6,
			7 => 7,
			8 => 8,
			9 => 9,
		);
		$nbPlugins = count($pluginsManager->getPlugins());
		break;
	case 'save':
		if($administrator->isAuthorized()){
			foreach($pluginsManager->getPlugins() as $k=>$v) {
				if(isset($_POST['activate'][$v->getName()])){
					if(!$v->isInstalled()) $pluginsManager->installPlugin($v->getName(), true);
					else $v->setConfigVal('activate', 1);
				}else $v->setConfigVal('activate', 0);
				if($v->isInstalled()){
					$v->setConfigVal('priority', intval($_POST['priority'][$v->getName()]));
					if(!$pluginsManager->savePluginConfig($v)){
						$msg = $core->lang('An error occured while saving your modifications.');
						$msgType = 'error';
					}
					else{
						$msg = $core->lang("The changes have been saved.");
						$msgType = 'success';
					}
				}
			}
		}
		header('location:index.php?p=pluginsmanager&msg='.urlencode($msg).'&msgType='.$msgType);
		die();
		break;
	case 'cache':
		if($administrator->isAuthorized()){
			$pluginsManager->intiPluginsCache(array(), true);
			$msg = $core->lang("Cache cleared.");
		}
		header('location:index.php?p=pluginsmanager&msg='.urlencode($msg).'&msgType='.$msgType);
		die();
		break;
}
?>