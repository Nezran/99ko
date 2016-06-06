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

defined('ROOT') OR exit('No direct script access allowed');

class pluginsManager{

	private $plugins;
	private static $instance = null;
	
	## Constructeur
	public function __construct(){
		$this->plugins = $this->listPlugins();
	}
	
	## Retourne la liste des plugins
	public function getPlugins(){
		return $this->plugins;
	}
	
	## Retourne un objet plugin
	public function getPlugin($name){
		foreach($this->plugins as $plugin){
			if($plugin->getName() == $name) {
				return $plugin;
			}
		}
		return false;
	}
	
	## Sauvegarde la configuration d'un plugin
	public function savePluginConfig($obj){
		// Effacement du cache plugins lors d'une sauvegarde
		$this->intiPluginsCache(array(), true);
		if($obj->getIsValid() && $path = $obj->getDataPath()){
		    return util::writeJsonFile($path.'config.json', $obj->getConfig());
		}
	}

	## Installe un plugin
	public function installPlugin($name, $activate = false){
		// Effacement du cache plugins lors d'une installation
		$this->intiPluginsCache(array(), true);
		// Création du dossier data
		@mkdir(DATA_PLUGIN .$name.'/', 0777);
		@chmod(DATA_PLUGIN .$name.'/', 0777);
		// Lecture du fichier config usine
		$config = util::readJsonFile(PLUGINS .$name.'/param/config.json');
		// Par défaut le plugin est inactif
		if($activate) $config['activate'] = "1";
		else $config['activate'] = "0";
		// Création du fichier config
		@util::writeJsonFile(DATA_PLUGIN .$name.'/config.json', $config);
		@chmod(DATA_PLUGIN .$name.'/config.json', 0666);
		// Appel de la fonction d'installation du plugin
		if(function_exists($name.'Install')) call_user_func($name.'Install');
		// Check du fichier config
		if(!file_exists(DATA_PLUGIN .$name.'/config.json')) return false;
		return true;
	}
	
	## Génère la liste des plugins
	private function listPlugins(){
		$data = array();
		$dataNotSorted = array();
		$dataFromCache = $this->loadPluginsFromCache();
		if(ROOT == './' && is_array($dataFromCache)) $data = $dataFromCache;
		// Si pas de cache plugins, on reconstruit le fichier cache
		if(!$dataFromCache){
			$items = util::scanDir(PLUGINS);
			foreach($items['dir'] as $dir){
				// Si le plugin est installé on récupère sa configuration
				if(file_exists(DATA_PLUGIN .$dir. '/config.json')) $dataNotSorted[$dir] = util::readJsonFile(DATA_PLUGIN .$dir. '/config.json', true);
				// Sinon on lui attribu une priorité faible
				else $dataNotSorted[$dir]['priority'] = '10';
			}
			// On tri les plugins par priorité
			$dataSorted = @util::sort2DimArray($dataNotSorted, 'priority', 'num');
			foreach($dataSorted as $plugin=>$config){
				$data[] = $this->createPlugin($plugin);
			}
			// On génère le cache
			if(ROOT == './') $this->intiPluginsCache($data);
		}
		return $data;
	}
	
	## Créée un objet plugin
	private function createPlugin($name){
		// Instance du core
		$core = core::getInstance();
		// Infos du plugin
		$infos = util::readJsonFile(PLUGINS .$name. '/param/infos.json');
		// Configuration du plugin
		$config = util::readJsonFile(DATA_PLUGIN .$name. '/config.json');
		// Hooks du plugin
		$hooks = util::readJsonFile(PLUGINS .$name. '/param/hooks.json');
		// Config usine
		$initConfig = util::readJsonFile(PLUGINS .$name. '/param/config.json');
		// lang
		$lang = util::readJsonFile(PLUGINS .$name. '/lang/'.$core->getConfigVal('siteLang').'.json');
		// Derniers checks
		if(!is_array($config)) $config = array();
		if(!is_array($hooks)) $hooks = array();
		// Création de l'objet
		$plugin = new plugin($name, $config, $infos, $hooks, $initConfig, $lang);
		return $plugin;
	}
	
	## Lecture des plugins depuis le cache
	public function loadPluginsFromCache(){
		if(ROOT == './' && file_exists(DATA.'plugins.cache')) return unserialize(file_get_contents(DATA.'plugins.cache'));
		else return false;
	}
	
	## Initialisation du cache plugins
	public function intiPluginsCache($data, $clear = false){
		if($clear){
			@unlink(DATA.'plugins.cache');
			return true;
		}
		file_put_contents(DATA.'plugins.cache', serialize($data));
	}
	
	## Singleton
	public static function getInstance(){
		if(is_null(self::$instance)) self::$instance = new pluginsManager();
		return self::$instance;
	}
	
	## Retourne une valeur de configuration
	public static function getPluginConfVal($pluginName, $kConf){
		$instance = self::getInstance();
		$plugin = $instance->getPlugin($pluginName);
		return $plugin->getConfigVal($kConf);
	}
	
	## Détermine si le plugin ciblé existe et s'il est actif
	public static function isActivePlugin($pluginName){
		$instance = self::getInstance();
		$plugin = $instance->getPlugin($pluginName);
		if($plugin && $plugin->isInstalled() && $plugin->getConfigval('activate')) return true;
		return false;
	}
}
?>