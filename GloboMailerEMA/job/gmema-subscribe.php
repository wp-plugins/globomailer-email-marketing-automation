<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

// Connect GloboMailer Api

if($_SERVER['HTTP_HOST'] == 'localhost'){
	require_once GMEMA_DIR_AUTOLODER_LOCAL;
	require_once GMEMA_DIR_SETUP_LOCAL;
}else{
	require_once GMEMA_DIR_AUTOLODER_LIVE;
	require_once GMEMA_DIR_SETUP_LIVE;
}
GloboMailerApi_Autoloader::register();
$setting_data = gmema_cls_settings::gmema_setting_select(1);
setConfig($setting_data['gmema_c_publickey'],$setting_data['gmema_c_privatekey']);

?>
<?php
if(isset($_GET['gmema']))
{
	if($_GET['gmema'] == "subscribe")
	{
		$gmema_email = "";
		$gmema_name = "";
		$gmema_group = "";
		
		// get name and email value
		$gmema_email = isset($_POST['gmema_email']) ? $_POST['gmema_email'] : '';
		$gmema_name_first = isset($_POST['gmema_name_first']) ? $_POST['gmema_name_first'] : '';
		$gmema_name_last = isset($_POST['gmema_name_last']) ? $_POST['gmema_name_last'] : '';
		$gmema_group = isset($_POST['gmema_group']) ? $_POST['gmema_group'] : '';
		
		// trim querystring value
		$gmema_email = trim($gmema_email);
		$gmema_name_first = trim($gmema_name_first);
		$gmema_name_last = trim($gmema_name_last);
		$gmema_group = trim($gmema_group);
		
		// and if it is and we have post values, then we can proceed in sending the subscriber.
		if (!empty($_POST)) {

		    $listUid    = $gmema_group;// you'll take this from your customers area, in list overview from the address bar.
		    $endpoint   = new GloboMailerApi_Endpoint_ListSubscribers();
		    $response   = $endpoint->create($listUid, array(
		        'EMAIL' => isset($gmema_email) ? $gmema_email : null,
		        'FNAME' => isset($gmema_name_first) ? $gmema_name_first : null,
		        'LNAME' => isset($gmema_name_last) ? $gmema_name_last : null,
		    ));
		    $response   = $response->body;
		    
		    // if the returned status is success, we are done.
		    if ($response->itemAt('status') == 'success') {
		        $response = GloboMailerApi_Json::encode(array(
		            'status'    => 'success',
		            'message'   => 'Thank you for joining our email list. Please confirm your email address now!'
		        ));
		        echo $response;
		    }
		    
		    // otherwise, the status is error
		    $response = GloboMailerApi_Json::encode(array(
		        'status'    => 'error',
		        'message'   => $response->itemAt('error')
		    ));
		    echo $response;
		}
	}
}
die();
?>