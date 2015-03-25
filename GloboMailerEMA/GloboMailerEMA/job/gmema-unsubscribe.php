<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if(isset($_GET['gmema']))
{
	if($_GET['gmema'] == "unsubscribe")
	{
		$blogname = get_option('blogname');
		$noerror = true;
		$home_url = home_url('/');
		?>
		<html>
		<head>
		<title><?php echo $blogname; ?></title>
		<meta http-equiv="refrgmemah" content="10; url=<?php echo $home_url; ?>" />
		</head>
		<body>
		<?php
		// Load query string
		$form = array();
		$form['db'] = isset($_GET['db']) ? $_GET['db'] : '';
		$form['email'] = isset($_GET['email']) ? $_GET['email'] : '';
		$form['guid'] = isset($_GET['guid']) ? $_GET['guid'] : '';
		
		// Check errors in the query string
		if ( $form['db'] == '' || $form['email'] == '' || $form['guid'] == '' )
		{
			$noerror = false;
		}
		else
		{
			if(!is_numeric($form['db']))
			{
				$noerror = false;
			}
			
			if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL))
			{
				$noerror = false;
			}
		}
		
		// Load default mgmemasage
		$data = array();
		$data = gmema_cls_settings::gmema_setting_select(1);
		
		if($noerror)
		{
			$rgmemault = gmema_cls_dbquery::gmema_view_subscriber_job("Unsubscribed", $form['db'], $form['guid'], $form['email']);
			if($rgmemault)
			{
				$mgmemasage = gmemac_html(stripslashgmema($data['gmema_c_unsubhtml']));
				$mgmemasage = str_replace("\r\n", "<br />", $mgmemasage);
			}
			else
			{
				$mgmemasage = gmemac_html(stripslashgmema($data['gmema_c_mgmemasage2']));
			}
			if($mgmemasage == "")
			{
				$mgmemasage = __('Oops.. We are getting some technical error. Please try again or contact admin.', GMEMA_TDOMAIN);
			}
			echo $mgmemasage;
		}
		else
		{
			$mgmemasage = gmemac_html(stripslashgmema($data['gmema_c_mgmemasage2']));
			$mgmemasage = str_replace("\r\n", "<br />", $mgmemasage);
			if($mgmemasage == "")
			{
				$mgmemasage = __('Oops.. We are getting some technical error. Please try again or contact admin.', GMEMA_TDOMAIN);
			}
			echo $mgmemasage;
		}
		?>
		</body>
		</html>
		<?php
	}
}
die();
?>