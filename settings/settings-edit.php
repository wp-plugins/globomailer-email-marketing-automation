<?php error_reporting(0); if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<div class="wrap">
<?php
$gmema_errors = array();
$gmema_success = '';
$gmema_error_found = FALSE;
	
$result = gmema_cls_settings::gmema_setting_count(1);
if ($result != '1')
{ 
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist.', GMEMA_TDOMAIN); ?></strong></p></div>
	<?php
	$form = array(
		'gmema_c_id' => '',
		'gmema_c_list' => '',
		'selected_fields' => '',
		'gmema_c_subs_form' => '',
		'transact' => '',
		'transact_msg' => '',
	);
}
else
{
	$gmema_errors = array();
	$gmema_success = '';
	$gmema_error_found = FALSE;
	
	$data = array();
	$data = gmema_cls_settings::gmema_setting_select(1);
	$sub_from = gmema_cls_settings::generate_widget_Form($data);
	
	// Preset the form fields
	$form = array(
		'gmema_c_id' => $data['gmema_c_id'],
		'gmema_c_list' => $data['gmema_c_list'],
		'selected_fields' => unserialize($data['selected_fields']),
		'gmema_c_subs_form' => $sub_from,
		'transact' => $data['transact'],
		'transact_msg' => $data['transact_msg'],
	);
}

	
// Form submitted, check the data
if (isset($_POST['gmema_form_submit']) && $_POST['gmema_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('gmema_form_edit');
	
	$form['gmema_c_list'] = isset($_POST['gmema_c_list']) ? $_POST['gmema_c_list'] : '';
	$form['transact'] = isset($_POST['transact']) ? $_POST['transact'] : '';
	$form['transact_msg'] = isset($_POST['transact_msg']) ? $_POST['transact_msg'] : '';
	
	$selected_fields_temp  = isset($_POST['selected_fields']) ? $_POST['selected_fields'] : array();
	
	$form['selected_fields'] = serialize($selected_fields_temp);
	
	//	No errors found, we can add this Group to the table
	if ($gmema_error_found == FALSE)
	{	
		$action = "";
		$action = gmema_cls_settings::gmema_setting_update($form);
		if($action == "sus")
		{
			$gmema_success = __('Details was successfully updated.', GMEMA_TDOMAIN);
		}
		else
		{
			$gmema_error_found == TRUE;
			$gmema_errors[] = __('Oops, details not update.', GMEMA_TDOMAIN);
		}
	}
	$_POST['selected_fields'] = $form['selected_fields'];
	$form['selected_fields'] = $selected_fields_temp;
	$form['gmema_c_subs_form'] = gmema_cls_settings::generate_widget_Form($_POST);
}

if ($gmema_error_found == TRUE && isset($gmema_errors[0]) == TRUE)
{
	?>
		<div class="error">
			<p><strong><?php echo $gmema_errors[0]; ?></strong></p>
		</div>
	<?php
}
if ($gmema_error_found == FALSE && strlen($gmema_success) > 0)
{
	?>
	<div class="updated">
		<p>
			<strong>
				<?php echo $gmema_success; ?> 
				<a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=gmema-settings"><?php _e('Click here', GMEMA_TDOMAIN); ?></a>
				<?php _e(' to view the details', GMEMA_TDOMAIN); ?>
			</strong>
		</p>
	</div>
	<?php
}



?>
<style>
.form-table th {
    width: 350px;
}
</style>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><img src="<?php echo GMEMA_URL ?>img/logo-300x76.png" id="logo-img" width="154px"></h2>
	<h3><?php _e('Settings', GMEMA_TDOMAIN); ?></h3>
	<form name="gmema_form" method="post" action="#" >
	<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="elp"><?php _e('Transactional Email', GMEMA_TDOMAIN); ?>
				<p class="description"><?php _e('Check this field if you want to send the trnsactional email from the API.', GMEMA_TDOMAIN); ?></p></label>
			</th>
			<td align="left">
				<input type="checkbox"<?php echo (isset($form['transact']) && $form['transact'] == 'SEND') ? ' checked="checked"' : ''; ?> value="SEND" name="transact">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="elp"><?php _e('Transactional Email Message', GMEMA_TDOMAIN); ?>
				<p class="description"><?php _e('Write the transactional email message, it will send when the user subscribe the in this site.', GMEMA_TDOMAIN); ?></p></label>
			</th>
			<td align="left">
				<textarea name="transact_msg" id="transact_msg" style="width: 534px; height: 54px;"><?php echo esc_html(stripslashes($form['transact_msg'])); ?></textarea>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="elp"><?php _e('Select List', GMEMA_TDOMAIN); ?>
				<p class="description"><?php _e('Please select list <br> Fist enter key and save setting and after select the list and save.', GMEMA_TDOMAIN); ?></p></label>
			</th>
			<td>
			
				<?php
				$myData = array();
				$endpoint_lists = new GloboMailerApi_Endpoint_Lists();
				$response = $endpoint_lists->getLists($pageNumber = 1, $perPage = 100);
				$status = $response->body;
				$myData = $response->body->toArray();
				if($status->itemAt('status') == 'success')
				{	
					echo '<select name="gmema_c_list" id="gmema_c_list">';
					echo '<option value="">-- Select list --</option>';
					foreach ($myData['data']['records'] as $data)
					{
						$lists_id = $data['general']['list_uid'];
						$lists_name = $data['general']['name'];
				
						$value_list = $form['gmema_c_list'];
						if($value_list == $lists_id){
							echo '<option value="'.$lists_id.'" selected>'.$lists_name.'</option>';	
						}else{
							echo '<option value="'.$lists_id.'">'.$lists_name.'</option>';
						}
						
					}
					echo '</select>';
				}?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="elp"><?php _e('Select Fields Require', GMEMA_TDOMAIN); ?>
				<p class="description"><?php _e('Please select filds that you want to include in subscription form.', GMEMA_TDOMAIN); ?></p></label>
			</th>
			<td align="left">
				<input type="checkbox"<?php echo (isset($form['selected_fields']) && in_array('EMAIL',$form['selected_fields'])) ? ' checked="checked"' : ''; ?> value="EMAIL" name="selected_fields[]">
				<?php _e('Email', GMEMA_TDOMAIN); ?><br />
				<input type="checkbox"<?php echo (isset($form['selected_fields']) &&in_array('FNAME',$form['selected_fields'])) ? ' checked="checked"' : ''; ?> value="FNAME" name="selected_fields[]">
				<?php _e('First name', GMEMA_TDOMAIN); ?><br />
            	<input type="checkbox"<?php echo (isset($form['selected_fields']) &&in_array('LNAME',$form['selected_fields'])) ? ' checked="checked"' : ''; ?> value="LNAME" name="selected_fields[]">
            	<?php _e('Last name', GMEMA_TDOMAIN); ?>
            </td>
		</tr>
		<tr>
			<th scope="row">
				<label for="elp"><?php _e('Enter Subscriber Form HTML', GMEMA_TDOMAIN); ?>
				<p class="description"><?php _e('Please put the subscriber form HTML. Click on save setting to generate the Subscription form', GMEMA_TDOMAIN); ?></p></label>
			</th>
			<td>
				<textarea name="gmema_c_subs_form" id="gmema_c_subs_form" style="width: 534px; height: 177px;"><?php echo esc_html(stripslashes($form['gmema_c_subs_form'])); ?></textarea>
			</td>
		</tr>
	</tbody>
	</table>
	<div style="padding-top:10px;"></div>
	<input type="hidden" name="gmema_form_submit" value="yes"/>
	<input type="hidden" name="gmema_c_id" id="gmema_c_id" value="<?php echo $form['gmema_c_id']; ?>"/>
	<p class="submit">
		<input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Save Settings', GMEMA_TDOMAIN); ?>" type="submit" />
	</p>
	<?php wp_nonce_field('gmema_form_edit'); ?>
    </form>
</div>
</div>