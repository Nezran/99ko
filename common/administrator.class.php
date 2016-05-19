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

class administrator{
    private static $instance = null;
    private $email;
    private $pwd;
    private $token;
    
    ## Constructeur
    public function __construct($email = '', $pwd = ''){
        $this->email = ($email != '') ? $email : @$_SESSION['adminEmail'];
        $this->pwd = ($pwd != '') ? $pwd : @$_SESSION['adminPwd'];
        $this->token = (isset($_SESSION['adminToken'])) ? $_SESSION['adminToken'] : sha1(uniqid(mt_rand(), true));
        $_SESSION['adminToken'] = $this->token;
    }
    
    ## Retourne l'instance administrator
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new administrator();
        }
        return self::$instance;
    }
    
    ## Retourne le jeton
    public static function getToken(){
        $instance = self::getInstance();
        return $instance->token;
    }
    
    ## Démmare la session admin si OK
    public function login($email, $pwd){
        if($this->encrypt($pwd) == $this->pwd && $email == $this->email){
            $_SESSION['admin'] = $this->pwd;
            $_SESSION['adminEmail'] = $email;
            $_SESSION['adminPwd'] = $this->pwd;
            return true;
        }
        else return false;
    }
    
    ## Détruit la session
    public function logout(){
        session_destroy();
    }
    
    ## Teste la session
    public function isLogged(){
        if(!isset($_SESSION['admin']) || $_SESSION['admin'] != $this->pwd) return false;
        else return true;
    }
    
    ## Teste le token et autres choses...
    public function isAuthorized(){
        if(!isset($_REQUEST['token'])) return false;
        if($_REQUEST['token'] != $this->token) return false;
        return true;
    }
    
    ## Fonction de cryptage
    public function encrypt($data){
        return hash_hmac('sha1', $data, KEY);
    }

}
?>