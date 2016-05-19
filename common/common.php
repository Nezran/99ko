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
session_start();
defined('ROOT') OR exit('No direct script access allowed');
include_once(ROOT.'common/config.php');
include_once(COMMON.'util.class.php');
include_once(COMMON.'core.class.php');
include_once(COMMON.'pluginsManager.class.php');
include_once(COMMON.'plugin.class.php');
include_once(COMMON.'show.class.php');
## Création de l'instance core
$core = core::getInstance();
## Plugin par défaut du mode public
define('DEFAULT_PLUGIN', $core->getConfigVal('defaultPlugin'));
## Plugin par défaut du mode admin
define('DEFAULT_ADMIN_PLUGIN', $core->getConfigVal('defaultAdminPlugin'));
## Si le core n'est pas installé on redirige vers le script d'installation
if(!$core->isInstalled()){
	header('location:' .ROOT. 'install.php');
	die();
}
## Création de l'istance pluginsManager
$pluginsManager = pluginsManager::getInstance();
## On boucle sur les plugins
foreach($pluginsManager->getPlugins() as $plugin){
	// On inclut le fichier PHP principal
	include_once($plugin->getLibFile());
	// Le core charge le fichier langue du plugin
	$core->loadPluginLang($plugin->getName());
	// Le core alimente le tableau des hooks
	if($plugin->getConfigVal('activate')){
		foreach($plugin->getHooks() as $name=>$function){
			$core->addHook($name, $function);
		}
	}
}
## Hook
eval($core->callHook('startCreatePlugin'));
## Création de l'instance runPlugin, objet qui représente le plugin en cours d'execution
$runPlugin = $pluginsManager->getPlugin($core->getPluginToCall());
## Hook
eval($core->callHook('endCreatePlugin'));
?>