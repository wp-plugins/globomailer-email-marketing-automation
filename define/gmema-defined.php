<?php
$gmema_plugin_name='GloboMailerEMA';
$gmema_plugin_folder_name = dirname(dirname(plugin_basename(__FILE__)));
$gmema_current_folder=dirname(dirname(__FILE__));

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('GMEMA_TDOMAIN')) define('GMEMA_TDOMAIN', $gmema_plugin_name);
if(!defined('GMEMA_PLUGIN_NAME')) define('GMEMA_PLUGIN_NAME', $gmema_plugin_name);
if(!defined('GMEMA_PLUGIN_DISPLAY')) define('GMEMA_PLUGIN_DISPLAY', "GloboMailerEMA");
if(!defined('GMEMA_PLG_DIR')) define('GMEMA_PLG_DIR', dirname($gmema_current_folder).DS);
if(!defined('GMEMA_DIR')) define('GMEMA_DIR', $gmema_current_folder.DS);
if(!defined('GMEMA_DIR_AUTOLODER_LOCAL')) define('GMEMA_DIR_AUTOLODER_LOCAL', GMEMA_DIR . 'lib\GloboMailerApi\Autoloader.php');
if(!defined('GMEMA_DIR_AUTOLODER_LIVE')) define('GMEMA_DIR_AUTOLODER_LIVE', GMEMA_DIR . 'lib/GloboMailerApi/Autoloader.php');
if(!defined('GMEMA_DIR_SETUP_LOCAL')) define('GMEMA_DIR_SETUP_LOCAL', GMEMA_DIR . 'lib\examples\setup.php');
if(!defined('GMEMA_DIR_SETUP_LIVE')) define('GMEMA_DIR_SETUP_LIVE', GMEMA_DIR . 'lib/examples/setup.php');
if(!defined('GMEMA_URL')) define('GMEMA_URL',plugins_url().'/'.'GloboMailerEMA'.'/');
define('GMEMA_FILE',GMEMA_DIR.'GloboMailerEMA.php');
if(!defined('GMEMA_FAV')) define('GMEMA_FAV', 'http://www.archirayan.com');
if(!defined('GMEMA_ADMINURL')) define('GMEMA_ADMINURL', get_option('siteurl') . '/wp-admin/admin.php');
define('GMEMA_OFFICIAL', 'Check official website for more information <a target="_blank" href="'.GMEMA_FAV.'">click here</a>');
global $gmema_includes;
?>