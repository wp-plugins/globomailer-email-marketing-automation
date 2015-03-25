<?php
class gmema_cls_widget
{
	public static function load_subscription($arr)
	{
		$gmema_name = trim($arr['gmema_name']);
		$gmema_desc = trim($arr['gmema_desc']);
		$gmema_group = trim($arr['gmema_group']);
		$url = "'" . home_url() . "'";
		$gmema = "";
		
		global $gmema_includes;
		if (!isset($gmema_includes) || $gmema_includes !== true) 
		{ 
			$gmema_includes = true;
			$gmema = $gmema . '<link rel="stylesheet" media="screen" type="text/css" href="'.GMEMA_URL.'widget/gmema-widget.css" />';
		} 
		$gmema = $gmema . '<script language="javascript" type="text/javascript" src="'.GMEMA_URL.'widget/gmema-widget-page.js"></script>';
		$gmema = $gmema . "<div>";
		
		if( $gmema_desc <> "" ) 
		{ 
			$gmema = $gmema . '<div class="gmema_caption">'.$gmema_desc.'</div>';
		} 
		$gmema = $gmema . '<div class="gmema_msg"><span id="gmema_msg_pg"></span></div>';
		if( $gmema_name == "YES" ) 
		{
			$gmema = $gmema . '<div class="gmema_lablebox">'.__('First Name', GMEMA_TDOMAIN).'</div>';
			$gmema = $gmema . '<div class="gmema_textbox">';
				$gmema = $gmema . '<input class="gmema_textbox_class" name="gmema_txt_first_name_pg" id="gmema_txt_first_name_pg" value="" maxlength="225" type="text">';
			$gmema = $gmema . '</div>';
			
			$gmema = $gmema . '<div class="gmema_lablebox">'.__('Last Name', GMEMA_TDOMAIN).'</div>';
			$gmema = $gmema . '<div class="gmema_textbox">';
				$gmema = $gmema . '<input class="gmema_textbox_class" name="gmema_txt_last_name_pg" id="gmema_txt_last_name_pg" value="" maxlength="225" type="text">';
			$gmema = $gmema . '</div>';
		}
		$gmema = $gmema . '<div class="gmema_lablebox">'.__('Email *', GMEMA_TDOMAIN).'</div>';
		$gmema = $gmema . '<div class="gmema_textbox">';
			$gmema = $gmema . '<input class="gmema_textbox_class" name="gmema_txt_email_pg" id="gmema_txt_email_pg" onkeypress="if(event.keyCode==13) gmema_submit_pages('.$url.')" value="" maxlength="225" type="text">';
		$gmema = $gmema . '</div>';
		$gmema = $gmema . '<div class="gmema_button">';
			$gmema = $gmema . '<input class="gmema_textbox_button" name="gmema_txt_button_pg" id="gmema_txt_button_pg" onClick="return gmema_submit_pages('.$url.')" value="'.__('Subscribe', GMEMA_TDOMAIN).'" type="button">';
		$gmema = $gmema . '</div>';
		if( $gmema_name != "YES" ) 
		{
			$gmema = $gmema . '<input name="gmema_txt_name_pg" id="gmema_txt_name_pg" value="" type="hidden">';
		}
		$gmema = $gmema . '<input name="gmema_txt_group_pg" id="gmema_txt_group_pg" value="'.$gmema_group.'" type="hidden">';
		$gmema = $gmema . '</div>';
		return $gmema;
	}
}

function gmema_shortcode( $atts ) 
{	
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	
	//[GloboMailerEMA namefield="YES" desc="" list="Public"] Code short code
	//[GloboMailerEMA namefield = NO desc = "" list = Public] Post/Page shortcode
	$setting_data = gmema_cls_settings::gmema_setting_select(1);
	$gmema_name = isset($atts['namefield']) ? $atts['namefield'] : 'YES';
	$gmema_desc = isset($atts['desc']) ? $atts['desc'] : '';
	
	if(isset($atts['list']) && $atts['list'] != ''){
		$gmema_group	= $atts['list'];
	}else{
		$gmema_group	= $setting_data['gmema_c_list'];
	}
	
	$arr = array();
	$arr["gmema_title"] 	= "";
	$arr["gmema_desc"] 	= $gmema_desc;
	$arr["gmema_name"] 	= $gmema_name;
	$arr["gmema_group"] 	= $gmema_group;
	return gmema_cls_widget::load_subscription($arr);
}

function gmema_subbox( $namefield = "YES", $desc = "", $group = "" )
{
	$arr = array();
	$arr["gmema_title"] 	= "";
	$arr["gmema_desc"] 	= $desc;
	$arr["gmema_name"] 	= $namefield;
	$arr["gmema_group"] 	= $group;
	echo gmema_cls_widget::load_subscription($arr);
}
?>