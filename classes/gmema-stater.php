<?php
require_once(GMEMA_DIR.'classes'.DIRECTORY_SEPARATOR.'gmema-register.php');
require_once(GMEMA_DIR.'classes'.DIRECTORY_SEPARATOR.'gmema-intermediate.php');
require_once(GMEMA_DIR.'classes'.DIRECTORY_SEPARATOR.'gmema-loadwidget.php');
require_once(GMEMA_DIR.'query'.DIRECTORY_SEPARATOR.'db_settings.php');
require_once(GMEMA_DIR.'query'.DIRECTORY_SEPARATOR.'db_default.php');

if($_SERVER['HTTP_HOST'] == 'localhost'){
	require_once GMEMA_DIR_AUTOLODER_LOCAL;
	require_once GMEMA_DIR_SETUP_LOCAL;
}else{
	require_once GMEMA_DIR_AUTOLODER_LIVE;
	require_once GMEMA_DIR_SETUP_LIVE;
}
if(!function_exists('register')){
	GloboMailerApi_Autoloader::register();
	$setting_data = gmema_cls_settings::gmema_setting_select(1);
	if(isset($setting_data['gmema_c_publickey'])){
		setConfig($setting_data['gmema_c_publickey'],$setting_data['gmema_c_privatekey']);	
	}	
}
?>