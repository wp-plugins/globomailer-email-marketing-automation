<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
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
		    $endpoint_sub   = new GloboMailerApi_Endpoint_ListSubscribers();
		    $response_sub   = $endpoint_sub->create($listUid, array(
		        'EMAIL' => isset($gmema_email) ? $gmema_email : null,
		        'FNAME' => isset($gmema_name_first) ? $gmema_name_first : null,
		        'LNAME' => isset($gmema_name_last) ? $gmema_name_last : null,
		    ));
		    $response_sub   = $response_sub->body;
		    
		    // if the returned status is success, we are done.
		    if ($response_sub->itemAt('status') == 'success') {
		    	$data = gmema_cls_settings::gmema_setting_select(1);		        
				$endpoint_lists = new GloboMailerApi_Endpoint_Lists();
		        $response_list = $endpoint_lists->getList($listUid);
				$myData_body = $response_list->body;
				if ($myData_body->itemAt('status') == 'success') {
					$myData_data = $myData_body->itemAt('data');
					$form_data = $myData_data['record'];
			        
			        if($data['transact'] == 'SEND'){
				        $fromnm = $gmema_name_first.' '.$gmema_name_last;
				        if(!$_POST['FNAME']){
							$fromnm = 'Not provided';
						}
				        $endpoint_trans   = new GloboMailerApi_Endpoint_TransactionalEmails();
					    $response_trans = $endpoint_trans->create(array(
						    'to_name'           => $fromnm,
						    'to_email'          => $gmema_email,
						    'from_name'         => $form_data['defaults']['from_name'],
						    'from_email'        => $form_data['defaults']['reply_to'],
						    'reply_to_name'     => $form_data['defaults']['from_name'],
						    'reply_to_email'    => $form_data['defaults']['reply_to'],
						    'subject'           => 'Subscription',
						    'body'              => $data['transact_msg'],
						    'send_at'           => date('Y-m-d H:i:s'),
						));
					}else{
						$headers .= "From: ".$form_data['defaults']['from_name']." <".$form_data['defaults']['reply_to']."> \r\n";
						$headers .= 'Reply-To: '.$form_data['defaults']['reply_to']."\r\n";
						$headers .= "MIME-Version: 1.0\n";
						$headers .= "Content-Type: " . get_bloginfo('html_type') . "; charset=\"". get_bloginfo('charset') . "\"\n";
						$headers .= "Content-type: text/html\r\n";
						$to = $gmema_email;
						$subject = 'Subscription';
						$content = $data['transact_msg'];
						wp_mail($to, $subject, $content, $headers);
					}
				}
		        
		        $response_sub = GloboMailerApi_Json::encode(array(
		            'status'    => 'success',
		            'message'   => 'Thank you for joining our email list. Please confirm your email address now!'
		        ));
		        echo $response_sub; exit;
		    }
		    
		    // otherwise, the status is error
		    $response_sub = GloboMailerApi_Json::encode(array(
		        'status'    => 'error',
		        'message'   => $response_sub->itemAt('error')
		    ));
		    echo $response_sub;
		}
	}
}
die();
?>