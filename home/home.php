<?php error_reporting(0); if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo GMEMA_URL; ?>css/admin.css" />
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo GMEMA_URL; ?>css/bootstrap.css" />
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo GMEMA_URL; ?>css/fontawesome/css/font-awesome.css" />
<div id="wpbody">
	<div id="wpbody-content">
		<input type="hidden" id="cur_refer_url" value="<?php echo GMEMA_URL; ?>?page=gmema-settings">
			<div class="metabox-prefs" id="screen-meta">
				<div class="hidden no-sidebar" id="contextual-help-wrap">
					<div id="contextual-help-back"></div>
					<div id="contextual-help-columns">
						<div class="contextual-help-tabs">
							<ul></ul>
						</div>
						<div class="contextual-help-tabs-wrap"></div>
					</div>
				</div>
					</div>
			            <div class="box-border-box container-fluid" id="wrap">
	                <h2><img src="<?php echo GMEMA_URL ?>img/logo-300x76.png" id="logo-img"></h2>
					<?php
						$gmema_errors = array();
						$gmema_success = '';
						$gmema_error_found = FALSE;
							
						$result = gmema_cls_settings::gmema_setting_count(1);
						if ($result != '1')
						{
							?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist.', GMEMA_TDOMAIN); ?></strong></p></div><?php
							$form = array(
								'gmema_c_id' => '',
								'gmema_c_publickey' => '',
								'gmema_c_privatekey' => '',
							);
						}
						else
						{
							$gmema_errors = array();
							$gmema_success = '';
							$gmema_error_found = FALSE;
							
							$data = array();
							$data = gmema_cls_settings::gmema_setting_select(1);
							
							// Preset the form fields
							$form = array(
								'gmema_c_id' => $data['gmema_c_id'],
								'gmema_c_publickey' => $data['gmema_c_publickey'],
								'gmema_c_privatekey' => $data['gmema_c_privatekey'],
							);
						}
						
						if($form['gmema_c_publickey'] && $form['gmema_c_privatekey']){
							$savetext = 'Update Keys';
						}else{
							$savetext = 'Save Keys';
						}
						
							
						// Form submitted, check the data
						if (isset($_POST['gmema_form_submit']) && $_POST['gmema_form_submit'] == 'yes')
						{
							//Just security thingy that wordpress offers us
							check_admin_referer('gmema_form_edit');
							
							$form['gmema_c_publickey'] = isset($_POST['gmema_c_publickey']) ? $_POST['gmema_c_publickey'] : '';
							if ($form['gmema_c_publickey'] == '')
							{
								$gmema_errors[] = __('Please enter public key.', GMEMA_TDOMAIN);
								$gmema_error_found = TRUE;
							}
							
							$form['gmema_c_privatekey'] = isset($_POST['gmema_c_privatekey']) ? $_POST['gmema_c_privatekey'] : '';
							if ($form['gmema_c_privatekey'] == '')
							{
								$gmema_errors[] = __('Please enter pricvate key.', GMEMA_TDOMAIN);
								$gmema_error_found = TRUE;
							}
							
							$form['gmema_c_id'] = isset($_POST['gmema_c_id']) ? $_POST['gmema_c_id'] : '';
							
							//	No errors found, we can add this Group to the table
							if ($gmema_error_found == FALSE)
							{	
								$action = "";
								$action = gmema_cls_settings::gmema_home_update($form);
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
						}
					?>
					
	                <div class="box-border-box col-md-9" id="wrap-left">
	                
	            <div id="main-content">
	                <input type="hidden" value="" id="cur_refer_url">
	                <div class="panel panel-default row small-content">
	                    <div class="page-header">
	                        <span style="color: #777777;">Step1&nbsp;|&nbsp;</span><strong>Create a GloboMailer Account</strong>
	                    </div>
	                    <div class="panel-body">
	                        <div class="col-md-9 row">
	                            <p>By Creating a free GloboMailer account, you will have access to send a confirmation message.</p>
	                            <ul class="sib-home-feature">
	                                <li><span style="font-size: 12px;" class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Collect your contacts and upload your lists</li>
	                                <li><span style="font-size: 12px;" class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Use GloboMailer API to send your transactional emails</li>
	                                <li class="home-read-more-content"><span style="font-size: 12px;" class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Email marketing builders</li>
	                                <li class="home-read-more-content"><span style="font-size: 12px;" class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Create and schedule your email marketing campaigns</li>
	                                <li class="home-read-more-content"><span style="font-size: 12px;" class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;See all of&nbsp;<a target="_blank" href=" http://www.globomailer.com/?pk_campaign=wordpress_plugin&pk_kwd=plugin-home">GloboMailer's features</a></li>
	                            </ul>
	                            <a style="margin-top: 10px;" target="_blank" class="btn btn-primary" href="http://apps.globomailer.com/customer/guest/register?pk_campaign=wordpress_plugin&pk_kwd=create-account">Create an account</a>
	                        </div>
	                    </div>
	                </div>
	                <div class="panel panel-default row small-content">
	                    <div class="page-header">
	                        <span style="color: #777777;">Step2&nbsp;|&nbsp;</span><strong>Activate your account with your API key</strong>
	                    </div>
	                    <div class="panel-body">
	                        <div class="col-md-9 row">
							<?php
	                            if ($gmema_error_found == TRUE && isset($gmema_errors[0]) == TRUE)
								{
									?>
										<div class="alert alert-danger" id="failure-alert"><?php echo $gmema_errors[0]; ?></div>
								<?php
								}
								if ($gmema_error_found == FALSE && strlen($gmema_success) > 0)
								{
									?>
									<div class="alert alert-success" id="success-alert"><?php echo $gmema_success; ?></div>
									<?php
								}
								?>
								
	                            <p>
	                                Once your have created your GloboMailer account, activate this plugin to send all your transactional emails by using GloboMailer API to make sure all of your emails get to your contacts inbox.<br>
	                                To activate your plugin, enter your API Access key.<br>
	                            </p>
	                            <p>
	                                <a target="_blank" href="http://apps.globomailer.com/customer/api-keys/index?pk_campaign=wordpress_plugin&pk_kwd=create-account"><i class="fa fa-angle-right"></i>&nbsp;Get your API key from your account</a>
	                            </p>
	                            <p>
	                                </p><div class="col-md-7 row">
	                                    <form name="gmema_form" method="post" action="#" onsubmit="return _gmema_submit()"  >
	                                    <input type="hidden" name="gmema_form_submit" value="yes"/>
										<input type="hidden" name="gmema_c_id" id="gmema_c_id" value="<?php echo $form['gmema_c_id']; ?>"/>
	                                    <p class="col-md-12 row"><input type="text" placeholder="Public Key" style="margin-top: 10px;" name="gmema_c_publickey" class="col-md-10" id="sib_access_key" value="<?php echo $form['gmema_c_publickey']; ?>"></p>
	                                    <p class="col-md-12 row"><input name="gmema_c_privatekey" type="text" placeholder="Private Key" style="margin-top: 10px;" class="col-md-10" id="sib_access_key" value="<?php echo $form['gmema_c_privatekey']; ?>"></p>
	                                    <p class="col-md-12 row"><button class="col-md-4 btn btn-primary" id="sib_validate_btn" type="submit" name="submit"><span class="sib-spin" style="display: none;"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i>&nbsp;&nbsp;</span><?php echo $savetext; ?></button></p>
	                                    <?php wp_nonce_field('gmema_form_edit'); ?>
	                                    </form>
	                                </div>
	                            <p></p>
	                        </div>
	                    </div>
	                </div>
	            </div>
	                        </div>
	                <div class="box-border-box  col-md-3" id="wrap-right-side">
	                    
	            <div class="panel panel-default text-left box-border-box  small-content">
	                <div class="panel-heading"><strong>About GloboMailer</strong></div>
	                <div class="panel-body">
	                    <p>Easy to use, Low Cost, Email Marketing. GloboMailer is aowerful email marketing web service that is extremely easy to use. Manage your subscribers, send your campaigns and track results.</p>
	                    <ul class="sib-widget-menu">
	                        <li>
	                            <a target="_blank" href="http://www.globomailer.com/about-us/"><i class="fa fa-angle-right"></i> &nbsp;Who we are</a>
	                        </li>
	                        <li>
	                            <a target="_blank" href=" http://www.globomailer.com/plans-pricing/?pk_campaign=wordpress_plugin&pk_kwd=home-page"><i class="fa fa-angle-right"></i> &nbsp;Pricing</a>
	                        </li>
	                        <li>
	                            <a target="_blank" href="http://www.globomailer.com/?pk_campaign=wordpress_plugin&pk_kwd=plugin-home"><i class="fa fa-angle-right"></i> &nbsp;Features</a>
	                        </li>
	                    </ul>
	                </div>
	            </div>
	            <div class="panel panel-default text-left box-border-box  small-content">
	                <div class="panel-heading"><strong>Need Help ?</strong></div>
	                <div class="panel-body">
	                    <p>You have a question or need more information ?</p>
	                    <ul class="sib-widget-menu">
	                        <li><a target="_blank" href="http://apps.globomailer.com/articles/how-guides?pk_campaign=wordpress_plugin&pk_kwd=home-page"><i class="fa fa-angle-right"></i> &nbsp;Tutorials</a></li>
	                        <li><a target="_blank" href="http://www.globosupport.com/822430-Frequently-Asked-Questions-FAQ?pk_campaign=wordpress_plugin&pk_kwd=home-page"><i class="fa fa-angle-right"></i> &nbsp;FAQ</a></li>
	                    </ul>
	                </div>
	            </div>
	            <div class="panel panel-default text-left box-border-box  small-content">
	                <div class="panel-heading"><strong>Recommended this plugin</strong></div>
	                <div class="panel-body">
	                    <p>You like this plugin? Let everybody knows and review it</p>
	                    <ul class="sib-widget-menu">
	                        <li><a target="_blank" href="https://wordpress.org/support/plugin/globomailer-email-marketing-automation"></i> &nbsp;Review this plugin</a></li>
	                    </ul>
	                </div>
	            </div>
			</div>
		</div>  
	</div>
</div>