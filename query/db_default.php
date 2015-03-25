<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
class gmema_cls_default
{	
	public static function gmema_pluginconfig_default()
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		
		$result = gmema_cls_settings::gmema_setting_count(0);
		if ($result == 0)
		{
			$gmema_c_publickey = "";
			$gmema_c_privatekey = "";
			$gmema_c_list = "";
			$selected_fields = "";
			$transact = "";
			$transact_msg = "";
					
			$sSql = $wpdb->prepare("INSERT INTO `".$prefix."gmema_pluginconfig` 
					(`gmema_c_publickey`,`gmema_c_privatekey`,`gmema_c_list`,`selected_fields`,`transact`,`transact_msg`)
					VALUES(%s, %s, %s, %s, %s, %s)", 
					array($gmema_c_publickey,$gmema_c_privatekey,$gmema_c_list,$selected_fields,$transact,$transact_msg));
			$wpdb->query($sSql);
		}
		return true;
	}
}
?>