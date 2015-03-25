<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
class gmema_cls_settings
{
	public static function gmema_setting_select($id = 1)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."gmema_pluginconfig` where 1=1";
		$sSql = $sSql . " and gmema_c_id=".$id;
		$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function gmema_setting_count($id = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($id > 0)
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."gmema_pluginconfig` WHERE `gmema_c_id` = %s", array($id));
		}
		else
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."gmema_pluginconfig`";
		}
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function gmema_setting_update($data = array())
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = $wpdb->prepare("UPDATE `".$prefix."gmema_pluginconfig` SET 
			`gmema_c_publickey` = %s,`gmema_c_privatekey` = %s,`gmema_c_list` = %s,`selected_fields` = %s 
			WHERE gmema_c_id = %d	LIMIT 1", 
			array($data["gmema_c_publickey"],$data["gmema_c_privatekey"],$data["gmema_c_list"],$data["selected_fields"],$data["gmema_c_id"]));
		$wpdb->query($sSql);
		return "sus";
	}
	
	public static function generate_widget_Form($instance = array())
	{	
		if (empty($instance['gmema_c_list']) || empty($instance['gmema_c_publickey']) || empty($instance['gmema_c_privatekey'])) {
	        return;
	    }
	    $selected_fields = unserialize($instance['selected_fields']);
	    $endpoint = new GloboMailerApi_Endpoint_ListFields();
	    $response = $endpoint->getFields($instance['gmema_c_list']);
	    $response = $response->body->toArray();
	    
	    if (!isset($response['status']) || $response['status'] != 'success' || empty($response['data']['records'])) {
	        return;
	    }
	    
	    $freshFields    = $response['data']['records'];
	    $selectedFields = !empty($selected_fields) ? $selected_fields : array();
	    $rowTemplate    = '<div class="form-group"><label>[LABEL] [REQUIRED_SPAN]</label><input type="text" class="form-control" name="[TAG]" placeholder="[HELP_TEXT]" value="" [REQUIRED]/></div>';
	    
	    $output = array();
	    foreach ($freshFields as $field) {
	        $searchReplace = array(
	            '[LABEL]'           => $field['label'],
	            '[REQUIRED]'        => $field['required'] != 'yes' ? '' : 'required',
	            '[REQUIRED_SPAN]'   => $field['required'] != 'yes' ? '' : '<span class="required">*</span>',
	            '[TAG]'             => $field['tag'],
	            '[HELP_TEXT]'       => $field['help_text'],
	            
	        );
	        if (in_array($field['tag'], $selectedFields) || $field['required'] == 'yes') {
	            $output[] = str_replace(array_keys($searchReplace), array_values($searchReplace), $rowTemplate);
	        }
	    }
	    $url = "'" . home_url() . "'";
	    //$out = '<form method="post" action="'.home_url().'/?gmema=subscribe">' . "\n\n";
	    $out .= '<div class="gmema_msg"><span id="gmema_msg_pg"></span></div>';
	    $out .= implode("\n\n", $output);
	    $out .= "\n\n";
	    $out .= '<div class="clearfix"><!-- --></div><div class="actions pull-right"><button type="button" class="btn btn-default btn-submit" onClick="return gmema_submit_pages_cust('.$url.')">Subscribe</button></div><div class="clearfix"><!-- --></div>';
	    $out .= "\n\n" . '<input id="gmema_group" type="hidden" value="'.$instance['gmema_c_list'].'" name="gmema_group">';
	    //$out .= "\n\n" . '</form>';
	    $out .= "\n\n" . '<script language="javascript" type="text/javascript" src="'.GMEMA_URL.'widget/gmema-widget-page_cust.js"></script>';
	    
	    return $out;
	}
}
?>