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

define('VERSION', '3.0.3');
define('COMMON',  ROOT.'common/');
define('LANG', COMMON.'lang/');
define('DATA', ROOT.'data/');
define('UPLOAD', ROOT.'data/upload/');
define('DATA_PLUGIN', ROOT.'data/plugin/');
define('THEMES', ROOT.'theme/');
define('PLUGINS', ROOT.'plugin/');
define('ADMIN_PATH', ROOT.'admin/');
if(file_exists(DATA.'key.php')) include(DATA.'key.php');
?>