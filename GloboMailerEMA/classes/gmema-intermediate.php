<?php
class gmema_cls_intermediate
{
	public static function gmema_list()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(GMEMA_DIR.'list'.DIRECTORY_SEPARATOR.'view-list-add.php');
				break;
			case 'edit':
				require_once(GMEMA_DIR.'list'.DIRECTORY_SEPARATOR.'view-list-edit.php');
				break;
			default:
				require_once(GMEMA_DIR.'list'.DIRECTORY_SEPARATOR.'view-list-show.php');
				break;
		}
	}
	
	public static function gmema_settings()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(GMEMA_DIR.'settings'.DIRECTORY_SEPARATOR.'settings-add.php');
				break;
			default:
				require_once(GMEMA_DIR.'settings'.DIRECTORY_SEPARATOR.'settings-edit.php');
				break;
		}
	}
}
?>