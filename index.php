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
define('ROOT', './');
include_once(ROOT.'common/common.php');
## Hook
eval($core->callHook('startFrontIncludePluginFile'));
## Gestion des erreurs 404
if(!$runPlugin || $runPlugin->getConfigVal('activate') < 1) $core->error404();
## On inclut le fichier public et la template du plugin en cours d'execution
elseif($runPlugin->getPublicFile()){
	include($runPlugin->getPublicFile());
	include($runPlugin->getPublicTemplate());
}
## Hook
eval($core->callHook('endFrontIncludePluginFile'));
?>