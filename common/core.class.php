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

class core{
    private static $instance = null;
    private $config;
    private $hooks;
    private $urlParams;
    private $themes;
    private $langs;
    private $lang;
    private $pluginToCall;
    
    ## Constructeur
    public function __construct($forceLang = ''){
        // Timezone
        date_default_timezone_set(date_default_timezone_get());
        // Macgic quotes OFF
        util::setMagicQuotesOff();
        // Configuration
        $this->config = util::readJsonFile(DATA.'config.json', true);
        // Fix Bug update 2.2 => 2.3
        if($this->config['checkUrl'] == 'http://99ko.hellojo.fr/version'){
            $this->config['checkUrl'] = 'http://99ko.org/version';
            $this->saveConfig($this->config);
        }
        // Error reporting
        if($this->config['debug']) error_reporting(E_ALL);
        else error_reporting(E_ERROR | E_PARSE);
        // Tableau des paramètres d'URL
        if($this->getConfigVal('urlRewriting') == 1){
            if(isset($_GET['param'])) $this->urlParams = explode($this->getConfigVal('urlSeparator'), $_GET['param']);
        }
        else{
            foreach($_GET as $k=>$v) if($k != 'p'){
                $this->urlParams[] = $v;
            }
        }
        // Liste des thèmes
        $temp = util::scanDir(THEMES);
        foreach($temp['dir'] as $k=>$v){
            $this->themes[$v] = util::readJsonFile(THEMES.$v.'/infos.json', true);
        }
        // Liste des langues
        $this->langs = array('en');
        $temp = util::scanDir(LANG);
        foreach($temp['file'] as $k=>$v){
            $this->langs[] = substr($v, 0, 2);
        }
        // Tableau langue courante
        if($forceLang == '') $this->lang = util::readJsonFile(LANG.$this->getConfigVal('siteLang').'.json', true);
        else $this->lang = util::readJsonFile(LANG.$forceLang.'.json', true);
        if(file_exists(THEMES.$this->getConfigVal('theme').'/lang/'.$this->getConfigVal('siteLang').'.json')){
            $this->lang = array_merge($this->lang, util::readJsonFile(THEMES.$this->getConfigVal('theme').'/lang/'.$this->getConfigVal('siteLang').'.json', true));
        }
        if(!is_array($this->lang)) $this->lang = array();
        // Quel est le plugin solicité ?
        $this->pluginToCall = isset($_GET['p']) ? $_GET['p'] : $this->getConfigVal('defaultPlugin');
    }
    
    ## Retourne l'instance core
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new core();
        }
        return self::$instance;
    }
    
    ## Retourne le paramètre d'URL ciblé
    public function getUrlParam($k){
        if(isset($this->urlParams[$k])) return $this->urlParams[$k];
        else return false;
    }
    
    ## Retourne la liste des langues
    public function getLangs(){
        return $this->langs;
    }
    
    ## Retourne la liste des thèmes
    public function getThemes(){
        return $this->themes;
    }
    
    ## Retourne une phrase dans la langue courante
    public function lang($k){
        if($this->getConfigVal('siteLang') == 'en') return $k;
        elseif(is_array($this->lang) && array_key_exists($k, $this->lang)) return $this->lang[$k];
        else return $k;
    }
    
    ## Retourne une valeur de configuration
    public function getConfigVal($k){
        if(isset($this->config[$k])) return $this->config[$k];
        elseif(!isset($this->config[$k])){
            // Auto update config : 2.0 => 2.1
            if($k == 'urlSeparator'){
                $this->saveConfig($this->config, array('urlSeparator' => ','));
                return ',';
            }
        }
        else return false;
    }
    
    ## Retourne l'information ciblée d'un thème
    public function getThemeInfo($k){
        if(isset($this->themes[$this->getConfigVal('theme')])) return $this->themes[$this->getConfigVal('theme')][$k];
        else return false;
    }
    
    ## Retourne l'identifiant du plugin solicité
    public function getPluginToCall(){
        return $this->pluginToCall;
    }
    
        ## Détermine si 99ko est installé
    public function isInstalled(){
        if(!file_exists(DATA.'config.json')) return false;
        else return true;
    }
    
    ## Génère l'URL du site
    public function makeSiteUrl(){
        $siteUrl = str_replace(array('install.php', '/admin/index.php'), array('', ''), $_SERVER['SCRIPT_NAME']);
        $isSecure = false;
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $isSecure = true;
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') $isSecure = true;
        $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
        $siteUrl = $REQUEST_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].$siteUrl;
        $pos = mb_strlen($siteUrl)-1;
        if($siteUrl[$pos] == '/') $siteUrl = substr($siteUrl, 0, -1);
        return $siteUrl;
    }
    
    ## Génère une URL réécrite ou retourne l'URL standard
    public function makeUrl($plugin, $params = array()){
        if($this->getConfigVal('urlRewriting') == 1){
            $url = $plugin.'/';
            if(count($params) > 0){
                foreach($params as $k=>$v) $url.= util::strToUrl($v).$this->getConfigVal('urlSeparator');
                $url = trim($url, $this->getConfigVal('urlSeparator'));
                $url.= '.html';
            }
        }
        else{
            $url = 'index.php?p='.$plugin;
            foreach($params as $k=>$v){
                $url.= '&amp;'.$k.'='.util::strToUrl($v);
            }
        }
        return $url;
    }
    
    ## Lance un check et retourne les alertes
    public function check(){
        $data = array();
        if(isset($_SESSION['installOk'])){
            $data[0]['msg'] = $this->lang("99ko is installed");
            $data[0]['type'] = 'success';
        }
        if(!file_exists(ROOT.'.htaccess')){
            $data[1]['msg'] = $this->lang('The .htaccess file is missing');
            $data[1]['type'] = 'warning';
        }
        if(file_exists(ROOT.'install.php')){
            $data[2]['msg'] = $this->lang('The install.php file must be deleted');
            $data[2]['type'] = 'warning';
        }
        if(!ini_get('allow_url_fopen')){
            $data[3]['msg'] = $this->lang("Unable to check for updates as 'allow_url_fopen' is disabled on this system.");
            $data[3]['type'] = 'error';
        }
        if($this->detectNewVersion()){
            $data[4]['msg'] = $this->lang("A new version of 99ko is available"). ' : '.$this->detectNewVersion();
            $data[4]['type'] = 'success';
        }
        if($this->getConfigVal('debug')){
            $data[5]['msg'] = $this->lang("The debug mode is activated");
            $data[5]['type'] = 'warning';
        }
        if(!file_exists(ROOT.'data/.htaccess') || file_get_contents(ROOT.'data/.htaccess') != 'deny from all'){
            $data[6]['msg'] = $this->lang('The data/.htaccess file is missing or does not contain : deny from all');
            $data[6]['type'] = 'warning';
        }
        return $data;
    }
    
    ## Détecte s'il existe une nouvelle version de 99ko
    public function detectNewVersion(){
        if($last = trim(@file_get_contents($this->getConfigVal('checkUrl')))) if($last != VERSION) return $last;
        return false;
    }
    
    ## Ajoute un hook à executter
    public function addHook($name, $function){
        $this->hooks[$name][] = $function;
    }
    
    ## Appel un hook
    public function callHook($name){
        $return = '';
        if(isset($this->hooks[$name])){
            foreach($this->hooks[$name] as $function){
                $return.= call_user_func($function);
            }
        }
        return $return;
    }
    
    ## Charge le fichier lang d'un plugin dans le tableau lang
    public function loadPluginLang($plugin){
        $pluginsManager = pluginsManager::getInstance();
        $this->lang = array_merge($this->lang, $pluginsManager->getPlugin($plugin)->getLang());
    }
    
    ## Detecte le mode de l'administration
    public function detectAdminMode(){
        $mode = '';
        if(isset($_GET['action']) && $_GET['action'] == 'login') return 'login';
        elseif(isset($_GET['action']) && $_GET['action'] == 'logout') return 'logout';
        elseif(!isset($_GET['p'])) return 'plugin';
        elseif(isset($_GET['p'])) return 'plugin';
    }
    
    ## Renvoi une erreur 404
    public function error404(){
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
            include_once(THEMES.$this->getConfigVal('theme').'/404.php');	
            die();
    }
    
    ## Sauvegarde le tableau de configuration
    public function saveConfig($val, $append = array()){
        $config = util::readJsonFile(DATA.'config.json', true);
        $config = array_merge($config, $append);
        foreach($config as $k=>$v) if(isset($val[$k])){
            $config[$k] = $val[$k];
        }
        if(util::writeJsonFile(DATA.'config.json', $config)){
            $this->config = util::readJsonFile(DATA.'config.json', true);
            return true;
        }
        else return false;
    }
    
    ## Retourne l'objet administrator
    public function createAdministrator(){
        return new administrator($this->getConfigVal('adminEmail'), $this->getConfigVal('adminPwd'));
    }
    
    ## Installation de 99ko
    public function install(){
        $install = true;
        @chmod(ROOT.'.htaccess', 0666);
        if(!file_exists(ROOT.'.htaccess')){
            if(!@file_put_contents(ROOT.'.htaccess', "Options -Indexes", 0666)) $install = false;
        }
        if(!is_dir(DATA) && (!@mkdir(DATA) || !@chmod(DATA, 0777))) $install = false;
        if($install){
            if(!file_exists(DATA. '.htaccess')){
                if(!@file_put_contents(DATA. '.htaccess', "deny from all", 0666)) $install = false;
            }
            if(!is_dir(DATA_PLUGIN) && (!@mkdir(DATA_PLUGIN) || !@chmod(DATA_PLUGIN, 0777))) $install = false;
            if(!is_dir(UPLOAD) && (!@mkdir(UPLOAD) || !@chmod(UPLOAD, 0777))) $install = false;
            if(!file_exists(UPLOAD. '.htaccess')){
                if(!@file_put_contents(UPLOAD. '.htaccess', "allow from all", 0666)) $install = false;
            }
            if(!file_exists(__FILE__) || !@chmod(__FILE__, 0666)) $install = false;
            $key = uniqid(true);
            if(!file_exists(DATA. 'key.php') && !@file_put_contents(DATA. 'key.php', "<?php define('KEY', '$key'); ?>", 0666)) $install = false;
        }
        return $install;
    }
}
?>