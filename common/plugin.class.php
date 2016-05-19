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

class plugin{
	private $infos;
	private $config;
	private $name;
	private $hooks;
	private $isValid;
	private $isDefaultPlugin;
	private $titleTag;
	private $metaDescriptionTag;
	private $mainTitle;
	private $libFile;
	private $publicFile;
	private $adminFile;
	private $dataPath;
	private $publicTemplate;
	private $adminTemplate;
	private $initConfig;
	private $navigation;
	private $adminTabs;
	private $lang;
	private $publicCssFile;
	private $publicJsFile;
	private $adminCssFile;
	private $adminJsFile;
	private $isDefaultAdminPlugin;
	
	## Constructeur
	public function __construct($name, $config = array(), $infos = array(), $hooks = array(), $initConfig = array(), $lang = array()){
		$core = core::getInstance();
		// Identifiant
		$this->name = $name;
		// Tableau de configuration
		$this->config = $config;
		// Tableau d'informations
		$this->infos = $infos;
		// Liste des hooks
		$this->hooks = $hooks;
		// Validité du plugin (si false l'etat ou la configuration du plugin ne sera pas sauvegardé)
		$this->isValid = true;
		// Détermine si il s'agit du plugin par défaut en mode public
		$this->isDefaultPlugin = ($name == DEFAULT_PLUGIN) ? true : false;
		// Détermine si il s'agit du plugin par défaut en mode admin
		$this->isDefaultAdminPlugin = ($name == DEFAULT_ADMIN_PLUGIN) ? true : false;
		// Meta title
		$this->setTitleTag($infos['name']);
		// Titre de page
		$this->setMainTitle($infos['name']);
		// Fichier principal
		$this->libFile = (file_exists(PLUGINS .$this->name.'/'.$this->name.'.php')) ? PLUGINS .$this->name.'/'.$this->name.'.php' : false;
		// Tableau lang
		$this->lang = $lang;
		// Controlleur public
		$this->publicFile = (file_exists(PLUGINS .$this->name.'/public.php')) ? PLUGINS .$this->name.'/public.php' : false;
		// Controlleur admin
		$this->adminFile = (file_exists(PLUGINS .$this->name.'/admin.php')) ? PLUGINS .$this->name.'/admin.php' : false;
		// CSS
		$this->publicCssFile = (file_exists(PLUGINS .$this->name.'/other/public.css')) ? PLUGINS .$this->name.'/other/public.css' : false;
		$this->adminCssFile = (file_exists(PLUGINS .$this->name.'/other/admin.css')) ? PLUGINS .$this->name.'/other/admin.css' : false;
		// JS
		$this->publicJsFile = (file_exists(PLUGINS .$this->name.'/other/public.js')) ? PLUGINS .$this->name.'/other/public.js' : false;
		$this->adminJsFile = (file_exists(PLUGINS .$this->name.'/other/admin.js')) ? PLUGINS .$this->name.'/other/admin.js' : false;
		// Data
		$this->dataPath = (is_dir(DATA_PLUGIN .$this->name)) ? DATA_PLUGIN .$this->name.'/' : false;
		// Template public (peut etre le template par defaut ou un template présent dans le dossier du theme)
		if(file_exists('theme/'.$core->getConfigVal('theme').'/'.$this->name.'.php')) $this->publicTemplate = 'theme/'.$core->getConfigVal('theme').'/'.$this->name.'.php';
		elseif(file_exists(PLUGINS .$this->name.'/template/public.php')) $this->publicTemplate = PLUGINS .$this->name.'/template/public.php';
		else $this->publicTemplate = false;
		// Template admin
		$this->adminTemplate = (file_exists(PLUGINS.$this->name.'/template/admin.php')) ? PLUGINS.$this->name.'/template/admin.php' : false;
		// Configuration usine
		$this->initConfig = $initConfig;
		// Navigation
		$this->navigation = array();
		// Templates admin (mode onglets)
		$this->adminTabs = array();
		if(isset($this->config['adminTabs']) && $this->config['adminTabs'] != '') $this->adminTabs = explode(',', $this->config['adminTabs']);
		foreach($this->adminTabs as $k=>$v){
			if(file_exists(PLUGINS .$this->name.'/template/admin-tab-'.$k.'.php')){
				if(!is_array($this->adminTemplate)) $this->adminTemplate = array();
				$this->adminTemplate[] = PLUGINS .$this->name.'/template/admin-tab-'.$k.'.php';
			}
		}
	}
	
	## Getters
	public function getConfigVal($val){
		if(isset($this->config[$val])) return $this->config[$val];
	}
	
	public function getConfig(){
		return $this->config;
	}
	
	public function getInfoVal($val){
		return $this->infos[$val];
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getHooks(){
		return $this->hooks;
	}
	
	public function getIsDefaultPlugin(){
		return $this->isDefaultPlugin;
	}
	
	public function getTitleTag(){
		return $this->titleTag;
	}
	
	public function getMetaDescriptionTag(){
		return $this->metaDescriptionTag;
	}
	
	public function getMainTitle(){
		return $this->mainTitle;
	}
	
	public function getLibFile(){
		return $this->libFile;
	}
	
	public function getPublicFile(){
		return $this->publicFile;
	}
	
	public function getAdminFile(){
		return $this->adminFile;
	}
	
	public function getPublicCssFile(){
		return $this->publicCssFile;
	}
	
	public function getAdminCssFile(){
		return $this->adminCssFile;
	}
	
	public function getPublicJsFile(){
		return $this->publicJsFile;
	}
	
	public function getAdminJsFile(){
		return $this->adminJsFile;
	}
	
	public function getDataPath(){
		return $this->dataPath;
	}
	
	public function getPublicTemplate(){
		return $this->publicTemplate;
	}
	
	public function getAdminTemplate(){
		return $this->adminTemplate;
	}
	
	public function getConfigTemplate(){
		return $this->configTemplate;
	}
	
	public function getIsValid(){
		return $this->isValid;
	}
	
	public function getNavigation(){
		return $this->navigation;
	}
	
	public function getAdminTabs(){
		return $this->adminTabs;
	}
	
	public function getLang(){
		return $this->lang;
	}
	
	public function getIsDefaultAdminPlugin(){
		return $this->isDefaultAdminPlugin;
	}
	
	## Détermine si le plugin utilise des onglets admin
	public function useAdminTabs(){
		if(is_array($this->adminTemplate)) return true;
		else return false;
	}

	## Permet de modifier une valeur de configuration
	public function setConfigVal($k, $v){
		$this->config[$k] = $v;
		if($k == 'activate' && $v < 1 && $this->isRequired()) $this->isValid = false;
	}
	
	## Permet de définir la meta title
	public function setTitleTag($val){
		$core = core::getInstance();
		/*if($this->isDefaultPlugin) $val = $core->getConfigVal('siteName').' | '.trim($val);
		else */$val = $val.' | '.$core->getConfigVal('siteName');
		$this->titleTag = $val;
	}
	
	## Permet de définir la meta description
	public function setMetaDescriptionTag($val){
		$this->metaDescriptionTag = trim($val);
	}
	
	## Permet de définir le titre de page
	public function setMainTitle($val){
		$this->mainTitle = trim($val);
	}
	
	## Ajoute un item dans la navigation
	function addToNavigation($label, $target, $targetAttribut = '_self'){
		$this->navigation[] = array('label' => $label, 'target' => $target, 'targetAttribut' => $targetAttribut);
	}
	
	## Supprime un item de la navigation
	function removeToNavigation($k){
		unset($this->navigation[$k]);
	}

	## Initialise la navigation
	function initNavigation(){
		$this->navigation = array();
	}

	## Détermine si le plugin est installé
	public function isInstalled(){
		$temp = $this->config;
		unset($temp['activate']);
		$currentConfig = implode(',', array_keys($temp));
		$initConfig = @implode(',', array_keys($this->initConfig));
		if(count($this->config) < 1 || $currentConfig != $initConfig) return false;
		elseif(isset($currentConfig['adminTabs'])){
			if($currentConfig['adminTabs'] != $initConfig['adminTabs']) return false;
		}
		return true;
	}
	
	## Détermine si le plugin est protégé, ce qui empèche de le désactiver
	public function isRequired(){
		if(isset($this->config['protected']) && $this->config['protected'] == 1) return true;
		if($this->isDefaultPlugin) return true;
		return false;
	}
}
?>