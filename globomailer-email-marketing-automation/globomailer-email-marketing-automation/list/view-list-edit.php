<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
$endpoint = new GloboMailerApi_Endpoint_Lists();
?>
<div class="wrap">
<?php
$gmema_errors = array();
$gmema_success = '';
$gmema_error_found = FALSE;

// Preset the form fields
$form = array(
	'gmema_g_name' => '', 
    'gmema_g_desc' => '', 
    'gmema_d_fname' => '', 
    'gmema_d_femail' => '', 
    'gmema_d_replyto' => '', 
    'gmema_d_subject' => '', 
    'gmema_n_subs' => '',
    'gmema_n_unsubs' => '',
    'gmema_n_substo' => '', 
    'gmema_n_unsubsto' => '', 
    'gmema_c_name' => '', 
    'gmema_c_country' => '', 
    'gmema_c_zone' => '', 
    'gmema_c_add1' => '', 
    'gmema_c_add2' => '', 
    'gmema_c_zonenm' => '', 
    'gmema_c_city' => '', 
    'gmema_c_zip' => '',
);

// Form submitted, check the data
if (isset($_POST['gmema_form_submit']) && $_POST['gmema_form_submit'] == 'yes' && $_GET['lid'])
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('gmema_form_add');
	
	$form['gmema_g_name'] = isset($_POST['gmema_g_name']) ? $_POST['gmema_g_name'] : '';
	if ($form['gmema_g_name'] == '')
	{
		$gmema_errors[] = __('Please enter list name.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	$form['gmema_g_desc'] = isset($_POST['gmema_g_desc']) ? $_POST['gmema_g_desc'] : '';
	if ($form['gmema_g_desc'] == '')
	{
		$gmema_errors[] = __('Please enter list Description.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	$form['gmema_d_fname'] = isset($_POST['gmema_d_fname']) ? $_POST['gmema_d_fname'] : '';
	if ($form['gmema_d_fname'] == '')
	{
		$gmema_errors[] = __('Please enter from name.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	$form['gmema_d_femail'] = isset($_POST['gmema_d_femail']) ? $_POST['gmema_d_femail'] : '';
	if ($form['gmema_d_femail'] == '')
	{
		$gmema_errors[] = __('Please enter from email.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	$form['gmema_d_replyto'] = isset($_POST['gmema_d_replyto']) ? $_POST['gmema_d_replyto'] : '';
	if ($form['gmema_d_replyto'] == '')
	{
		$gmema_errors[] = __('Please enter reply to email.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	$form['gmema_d_subject'] = isset($_POST['gmema_d_subject']) ? $_POST['gmema_d_subject'] : 'Hello!';
	
	$form['gmema_n_subs'] = $_POST['gmema_n_subs'];
	$form['gmema_n_unsubs'] = $_POST['gmema_n_unsubs'];
		
	$form['gmema_n_substo'] = isset($_POST['gmema_n_substo']) ? $_POST['gmema_n_substo'] : $_POST['gmema_d_femail'];
	$form['gmema_n_unsubsto'] = isset($_POST['gmema_n_unsubsto']) ? $_POST['gmema_n_unsubsto'] : $_POST['gmema_d_replyto'];
	
	$form['gmema_c_name'] = isset($_POST['gmema_c_name']) ? $_POST['gmema_c_name'] : '';
	if ($form['gmema_c_name'] == '')
	{
		$gmema_errors[] = __('Please enter company name.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	$form['gmema_c_country'] = isset($_POST['gmema_c_country']) ? $_POST['gmema_c_country'] : '';
	if ($form['gmema_c_country'] == '')
	{
		$gmema_errors[] = __('Please enter country.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	
	$form['gmema_c_zone'] = isset($_POST['gmema_c_zone']) ? $_POST['gmema_c_zone'] : '';
	
	$form['gmema_c_add1'] = isset($_POST['gmema_c_add1']) ? $_POST['gmema_c_add1'] : '';
	if ($form['gmema_c_add1'] == '')
	{
		$gmema_errors[] = __('Please enter Address 1.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	$form['gmema_c_add2'] = isset($_POST['gmema_c_add2']) ? $_POST['gmema_c_add2'] : '';
	
	$form['gmema_c_zonenm'] = isset($_POST['gmema_c_zonenm']) ? $_POST['gmema_c_zonenm'] : '';
	
	$form['gmema_c_city'] = isset($_POST['gmema_c_city']) ? $_POST['gmema_c_city'] : '';
	if ($form['gmema_c_city'] == '')
	{
		$gmema_errors[] = __('Please enter City name.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	
	$form['gmema_c_zip'] = isset($_POST['gmema_c_zip']) ? $_POST['gmema_c_zip'] : '';
	if ($form['gmema_c_zip'] == '')
	{
		$gmema_errors[] = __('Please enter zip code.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
	
	
	
	
	
	//	No errors found, we can add this Group to the table
	if ($gmema_error_found == FALSE)
	{
		$action = "";
		
		$action = $endpoint->update($_GET['lid'],array(
		    'general' => array(
		        'name'          => $form['gmema_g_name'],
		        'description'   => $form['gmema_g_desc'],
		    ),
		    'defaults' => array(
		        'from_name' => $form['gmema_d_fname'],
		        'from_email'=> $form['gmema_d_femail'],
		        'reply_to'  => $form['gmema_d_replyto'],
		        'subject'   => $form['gmema_d_subject'],
		    ),
		    'notifications' => array(
		        'subscribe'         => $form['gmema_n_subs'],
		        'unsubscribe'       => $form['gmema_n_unsubs'],
		        'subscribe_to'      => $form['gmema_n_substo'],
		        'unsubscribe_to'    => $form['gmema_n_unsubsto'],
		    ),
		    'company' => array(
		        'name'      => $form['gmema_c_name'],
		        'country'   => $form['gmema_c_country'],
		        'zone'      => $form['gmema_c_zone'],
		        'address_1' => $form['gmema_c_add1'],
		        'address_2' => $form['gmema_c_add2'],
		        'zone_name' => $form['gmema_c_zonenm'],
		        'city'      => $form['gmema_c_city'],
		        'zip_code'  => $form['gmema_c_zip'],
		    ),
		));
		
		$response = $action->body;
		if ($response->itemAt('status') == 'success') {
		    $gmema_success = __('List was successfully updated.', GMEMA_TDOMAIN);
		}else
		{
			$gmema_errors[] = $response->itemAt('error');
			$gmema_error_found = TRUE;
		}
			
		// Reset the form fields
		$form = array(
			'gmema_g_name' => '', 
		    'gmema_g_desc' => '', 
		    'gmema_d_fname' => '', 
		    'gmema_d_femail' => '', 
		    'gmema_d_replyto' => '', 
		    'gmema_d_subject' => '', 
		    'gmema_n_subs' => 'yes',
		    'gmema_n_unsubs' => 'yes',
		    'gmema_n_substo' => '', 
		    'gmema_n_unsubsto' => '', 
		    'gmema_c_name' => '', 
		    'gmema_c_country' => '', 
		    'gmema_c_zone' => '', 
		    'gmema_c_add1' => '',
		    'gmema_c_add2' => '',
		    'gmema_c_zonenm' => '', 
		    'gmema_c_city' => '', 
		    'gmema_c_zip' => '',
		);
	}
}

if ($gmema_error_found == TRUE && isset($gmema_errors[0]) == TRUE)
{
	?><div class="error fade"><p><strong><?php echo $gmema_errors[0]; ?></strong></p></div><?php
}
if ($gmema_error_found == FALSE && isset($gmema_success[0]) == TRUE)
{
	?>
	<div class="updated fade">
	<p><strong>
	<?php echo $gmema_success; ?>
	<a href="<?php echo GMEMA_ADMINURL; ?>?page=gmema-view-list"><?php _e('Click here', GMEMA_TDOMAIN); ?></a><?php _e(' to view the details', GMEMA_TDOMAIN); ?>
	</strong></p>
	</div>
	<?php
}

$response = $endpoint->getList($_GET['lid']);
$myData_body = $response->body;
$myData_data = $myData_body->itemAt('data');
$form_data = $myData_data['record'];

$form = array(
			'gmema_g_name' => $form_data['general']['name'], 
		    'gmema_g_desc' => $form_data['general']['description'], 
		    'gmema_d_fname' => $form_data['defaults']['from_name'], 
		    'gmema_d_femail' => $form_data['defaults']['from_email'], 
		    'gmema_d_replyto' => $form_data['defaults']['reply_to'], 
		    'gmema_d_subject' => $form_data['defaults']['subject'], 
		    'gmema_n_subs' => $form_data['notifications']['subscribe'],
		    'gmema_n_unsubs' => $form_data['notifications']['unsubscribe'],
		    'gmema_n_substo' => $form_data['notifications']['subscribe_to'], 
		    'gmema_n_unsubsto' => $form_data['notifications']['unsubscribe_to'], 
		    'gmema_c_name' => $form_data['company']['name'], 
		    'gmema_c_country' => $form_data['company']['country']['name'], 
		    'gmema_c_zone' => $form_data['company']['zone'], 
		    'gmema_c_add1' => $form_data['company']['address_1'],
		    'gmema_c_add2' => $form_data['company']['address_2'],
		    'gmema_c_zonenm' => $form_data['company']['zone_name'], 
		    'gmema_c_city' => $form_data['company']['city'], 
		    'gmema_c_zip' => $form_data['company']['zip_code'],
		);
?>
<script language="javaScript" src="<?php echo GMEMA_URL; ?>list/view-list.js"></script>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(GMEMA_PLUGIN_DISPLAY, GMEMA_TDOMAIN); ?></h2>
	<form name="form_addemail" method="post" action="#" onsubmit="return _gmema_addemail()"  >
      <h3 class="title"><?php _e('Edit List', GMEMA_TDOMAIN); ?></h3>
      <table class="form-table">
      	<tbody>
      		<tr>
				<td colspan="2">
					<hr />
	      			<h3 class="title"><?php _e('General', GMEMA_TDOMAIN); ?></h3>
				</td>
			</tr>
      		<tr>
				<th scope="row">
					<label for="elp">Name<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_g_name']; ?>" id="gmema_g_name" name="gmema_g_name">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Description<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_g_desc']; ?>" id="gmema_g_desc" name="gmema_g_desc">				
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<hr />
	      			<h3 class="title"><?php _e('Defaults', GMEMA_TDOMAIN); ?></h3>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">From Name<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_d_fname']; ?>" id="gmema_d_fname" name="gmema_d_fname">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">From Email<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_d_femail']; ?>" id="gmema_d_femail" name="gmema_d_femail">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Reply To<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_d_replyto']; ?>" id="gmema_d_replyto" name="gmema_d_replyto">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Subject</label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_d_subject']; ?>" id="gmema_d_subject" name="gmema_d_subject">				
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<hr />
	      			<h3 class="title"><?php _e('Notifications', GMEMA_TDOMAIN); ?></h3>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Subscribe<span style="color: red;"> *</span></label>
				</th>
				<td>
					<select id="gmema_n_subs" name="gmema_n_subs">
						<option <?php echo $form['gmema_n_subs'] == 'yes' ? 'selected' : ''; ?> value="yes">Yes</option>
						<option <?php echo $form['gmema_n_subs'] == 'no' ? 'selected' : ''; ?> value="no">No</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Unsubscribe<span style="color: red;"> *</span></label>
				</th>
				<td>
					<select id="gmema_n_unsubs" name="gmema_n_unsubs">
						<option <?php echo $form['gmema_n_unsubs'] == 'yes' ? 'selected' : ''; ?> value="yes">Yes</option>
						<option <?php echo $form['gmema_n_unsubs'] == 'no' ? 'selected' : ''; ?> value="no">No</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Subscribe To</label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_n_substo']; ?>" id="gmema_n_substo" name="gmema_n_substo">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Unsubscribe To</label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_n_unsubsto']; ?>" id="gmema_n_unsubsto" name="gmema_n_unsubsto">				
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<hr />
	      			<h3 class="title"><?php _e('Company', GMEMA_TDOMAIN); ?></h3>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Name<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_c_name']; ?>" id="gmema_c_name" name="gmema_c_name">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Country<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_c_country']; ?>" id="gmema_c_country" name="gmema_c_country">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Zone</label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_c_zone']; ?>" id="gmema_c_zone" name="gmema_c_zone">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Address 1<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_c_add1']; ?>" id="gmema_c_add1" name="gmema_c_add1">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Address 2</label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_c_add2']; ?>" id="gmema_c_add2" name="gmema_c_add2">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Zone Name</label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_c_zonenm']; ?>" id="gmema_c_zonenm" name="gmema_c_zonenm">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">City<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_c_city']; ?>" id="gmema_c_city" name="gmema_c_city">				
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="elp">Zip Code<span style="color: red;"> *</span></label>
				</th>
				<td>
					<input type="text" maxlength="225" size="60" value="<?php echo $form['gmema_c_zip']; ?>" id="gmema_c_zip" name="gmema_c_zip">				
				</td>
			</tr>
      	</tbody>
      </table>
      
      <input type="hidden" name="gmema_form_submit" value="yes"/>
	  <div style="padding-top:5px;"></div>
      <p>
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Submit', GMEMA_TDOMAIN); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="_gmema_redirect()" value="<?php _e('Cancel', GMEMA_TDOMAIN); ?>" type="button" />
      </p>
		<?php wp_nonce_field('gmema_form_add'); ?>
    </form>
</div>
</div>