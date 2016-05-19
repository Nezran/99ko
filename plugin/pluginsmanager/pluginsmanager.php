<?php
defined('ROOT') OR exit('No direct script access allowed');

/*
** Exécute du code lors de l'installation
** Le code présent dans cette fonction sera exécuté lors de l'installation
** Le contenu de cette fonction est facultatif
*/
function pluginsmanagerInstall(){
}

/********************************************************************************************************************
** Code relatif au plugin
** La partie ci-dessous est réservé au code du plugin 
** Elle peut contenir des classes, des fonctions, hooks... ou encore du code à exécutter lors du chargement du plugin
********************************************************************************************************************/

function pluginsmanagerAdminNotifications(){
    $core = core::getInstance();
    $pluginsManager = pluginsManager::getInstance();
    $list = '';
    foreach($pluginsManager->getPlugins() as $plugin){
        if(!$pluginsManager->isActivePlugin($plugin->getName())) $list.= $plugin->getInfoVal('name').', ';
    }
    if($list != '') show::msg($core->lang("Plugins are inactive").' ('.trim($list, ', ').')', "info");
}

?>