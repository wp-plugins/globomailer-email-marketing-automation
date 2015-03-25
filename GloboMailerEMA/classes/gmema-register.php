<?php

/**
 * Calls the class on the post edit screen.
 */
function call_gmema_cls_registerhook() {
    new gmema_cls_registerhook();
}

if ( is_admin() ) {
    add_action( 'load-post.php', 'call_gmema_cls_registerhook' );
    add_action( 'load-post-new.php', 'call_gmema_cls_registerhook' );
}


class gmema_cls_registerhook
{
	########### Meta box start #################
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		
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
	}
	
	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
            $post_types = array('post', 'page');     //limit meta box to certain post types
            if ( in_array( $post_type, $post_types )) {
		add_meta_box(
			'gmema_meta_box_name'
			,__( 'GloboMailer Email Marketing Automation', 'gmema_textdomain' )
			,array( $this, 'render_meta_box_content' )
			,$post_type
			,'advanced'
			,'high'
		);
            }
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['gmema_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['gmema_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'gmema_inner_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
		
		
		
		/* OK, its safe for us to save the data now. */
		
		// Sanitize the user input.
		$gmema_data_trnas = sanitize_text_field( $_POST['gmema_transactional_value_key'] );
		$gmema_data_list = sanitize_text_field( $_POST['gmema_lists_value_key'] );
		$send_mail = sanitize_text_field( $_POST['send_mail'] );
		
		// Update the meta field.
		update_post_meta( $post_id, '_gmema_transactional_value_key', $gmema_data_trnas );
		update_post_meta( $post_id, '_gmema_lists_value_key', $gmema_data_list );
		
		// Sending email to subscriber
		
		$value_trans = get_post_meta( $post_id, '_gmema_transactional_value_key', true );
		$value_list = get_post_meta( $post_id, '_gmema_lists_value_key', true );
		
		if($value_list && $send_mail == 'SEND'){
			$endpoint_lists = new GloboMailerApi_Endpoint_Lists();
			$endpoint_subs = new GloboMailerApi_Endpoint_ListSubscribers();
		
			$response = $endpoint_lists->getList($value_list);
			$myData_body = $response->body;
			$myData_data = $myData_body->itemAt('data');
			$form_data = $myData_data['record'];
			
			$response_subs = $endpoint_subs->getSubscribers($value_list, $pageNumber = 1, $perPage = 100);
			$subs_body = $response_subs->body;
			$subs_data = $subs_body->itemAt('data');
			$subscribers = $subs_data['records'];
			
			foreach($subscribers as $subscriber){
				$subscriber_list[] = $subscriber['EMAIL'];
			}
			
			$post = get_post($post_id);
			if($value_trans == 'no'){
				global $current_user;
				get_currentuserinfo();
				$headers .= "From: ".$current_user->display_name." <".$current_user->user_email."> \r\n";
				$headers .= 'Reply-To: '.$current_user->user_email."\r\n";
			}else{
				$headers .= "From: ".$form_data['defaults']['from_name']." <".$form_data['defaults']['reply_to']."> \r\n";
				$headers .= 'Reply-To: '.$form_data['defaults']['reply_to']."\r\n";
			}
			
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-Type: " . get_bloginfo('html_type') . "; charset=\"". get_bloginfo('charset') . "\"\n";
			$headers .= "Content-type: text/html\r\n";
			
			$reciepients = implode(",", $subscriber_list);
			$to = $reciepients;
			$subject = $form_data['defaults']['subject'];
			$content = str_replace("\r\n", "<br />", $post->post_content);
			
			wp_mail($to, $subject, $content, $headers);
		}
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'gmema_inner_custom_box', 'gmema_inner_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$value_trans = get_post_meta( $post->ID, '_gmema_transactional_value_key', true );
		
		$selected = '';
		if(esc_attr( $value_trans ) == 'yes'){
			$selected = '<option value="yes" selected>yes</option><option value="no" >No</option>';
		}else{
			$selected = '<option value="yes" >yes</option><option value="no" selected >No</option>';
		}
		// Display Transactional email option.
		echo '<label for="gmema_transactional_field">';
		_e( 'Enable Transactional Emails: ', GMEMA_TDOMAIN );
		echo '</label> ';
		echo '<select name="gmema_transactional_value_key" id="gmema_transactional_value_key" style="margin-right:50px;">';
		echo $selected;
		echo '</select>';
		
		// Display Lists option
		$myData = array();
		$endpoint_lists = new GloboMailerApi_Endpoint_Lists();
		$response = $endpoint_lists->getLists($pageNumber = 1, $perPage = 100);
		$myData = $response->body->toArray();
		if(count($myData) > 0)
		{
			echo '<label for="gmema_list_lable_field">';
			_e( 'Select List to send Emails: ', GMEMA_TDOMAIN );
			echo '</label> ';
			echo '<select name="gmema_lists_value_key" id="gmema_lists_value_key" style="margin-right:50px;">';
			foreach ($myData['data']['records'] as $data)
			{
				$lists_id = $data['general']['list_uid'];
				$lists_name = $data['general']['name'];
		
				$value_list = get_post_meta( $post->ID, '_gmema_lists_value_key', true );
				if($value_list == $lists_id){
					echo '<option value="'.$lists_id.'" selected>'.$lists_name.'</option>';	
				}else{
					echo '<option value="'.$lists_id.'">'.$lists_name.'</option>';
				}
				
			}
			echo '</select>';
		}else{
			echo 'No lists found..';
		}
		echo '<input type="checkbox" value="SEND" name="send_mail">';
		_e("Send email on Publish", GMEMA_TDOMAIN);
	}
	
	########### Meta box end #################
		
		
	public static function gmema_activation()
	{
		global $wpdb;
		
		add_option('GloboMailerEMA', "1.0.0");

		// Plugin tables
		$array_tables_to_plugin = array('gmema_pluginconfig');
		$errors = array();
		
		// loading the sql file, load it and separate the queries
		$sql_file = GMEMA_DIR.'sql'.DS.'gmema-createdb.sql';
		$prefix = $wpdb->prefix;
        $handle = fopen($sql_file, 'r');
        $query = fread($handle, filesize($sql_file));
        fclose($handle);
        $query=str_replace('CREATE TABLE IF NOT EXISTS `','CREATE TABLE IF NOT EXISTS `'.$prefix, $query);
        $queries = $query;

        // run the queries one by one
        $has_errors = false;
        $wpdb->query($queries);
		
		// list the tables that haven't been created
        $missingtables=array();
        foreach($array_tables_to_plugin as $table_name)
		{
			if(strtoupper($wpdb->get_var("SHOW TABLES like  '". $prefix.$table_name . "'")) != strtoupper($prefix.$table_name))  
			{
                $missingtables[]=$prefix.$table_name;
            }
        }
		
		// add error in to array variable
        if($missingtables) 
		{
			$errors[] = __('These tables could not be created on installation ' . implode(', ',$missingtables), GMEMA_TDOMAIN);
            $has_errors=true;
        }
		
		// if error call wp_die()
        if($has_errors) 
		{
			wp_die( __( $errors[0] , GMEMA_TDOMAIN ) );
			return false;
		}
		else
		{
			gmema_cls_default::gmema_pluginconfig_default();
		}
        return true;
	}
	
	public static function gmema_deactivation()
	{
		// do not generate any output here
	}
	
	public static function gmema_admin_option()
	{
		// do not generate any output here
	}
	
	public static function gmema_adminmenu()
	{	
		add_menu_page( __( 'GloboMailerEMA', GMEMA_TDOMAIN ), 
			__( 'GloboMailerEMA', GMEMA_TDOMAIN ), 'admin_dashboard', 'GloboMailerEMA', 'gmema_admin_option', 'dashicons-email-alt', 3 );
		add_submenu_page('GloboMailerEMA', __( 'Settings', GMEMA_TDOMAIN ), 
			__( 'Settings', GMEMA_TDOMAIN ), 'read', 'gmema-settings', array( 'gmema_cls_intermediate', 'gmema_settings' ));	
			
		add_submenu_page('GloboMailerEMA', __( 'Subscribers', GMEMA_TDOMAIN ), 
			__( 'List', GMEMA_TDOMAIN ), 'read', 'gmema-view-list', array( 'gmema_cls_intermediate', 'gmema_list' ));
	}
	
	public static function gmema_widget_loading()
	{
		register_widget( 'gmema_widget_register' );
	}
}

function call_gmema_cls_registerhook_widget() {
    new gmema_widget_register();
}

if ( is_admin() ) {
    add_action( 'load-post.php', 'call_gmema_cls_registerhook_widget' );
    add_action( 'load-post-new.php', 'call_gmema_cls_registerhook_widget' );
}


class gmema_widget_register extends WP_Widget 
{
	function __construct() 
	{
		$widget_ops = array('classname' => 'widget_text elp-widget', 'description' => __(GMEMA_PLUGIN_DISPLAY, GMEMA_TDOMAIN), GMEMA_PLUGIN_NAME);
		parent::__construct(GMEMA_PLUGIN_NAME, __(GMEMA_PLUGIN_DISPLAY, GMEMA_TDOMAIN), $widget_ops);
		
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
	}
	
	function widget( $args, $instance ) 
	{
		extract( $args, EXTR_SKIP );
		
		$gmema_title 	= apply_filters( 'widget_title', empty( $instance['gmema_title'] ) ? '' : $instance['gmema_title'], $instance, $this->id_base );
		$gmema_desc	= $instance['gmema_desc'];
		$gmema_name	= $instance['gmema_name'];
		$setting_data = gmema_cls_settings::gmema_setting_select(1);
		
		if(isset($instance['gmema_group']) && $instance['gmema_group'] != ''){
			$gmema_group	= $instance['gmema_group'];
		}else{
			$gmema_group	= $setting_data['gmema_c_list'];
		}

		echo $args['before_widget'];
		if ( ! empty( $es_title ) )
		{
			echo $args['before_title'] . $gmema_title . $args['after_title'];
		}
		// display widget method
		$url = home_url();
		
		global $gmema_includes;
		if (!isset($gmema_includes) || $gmema_includes !== true) 
		{ 
			$gmema_includes = true;
			?>
			<link rel="stylesheet" media="screen" type="text/css" href="<?php echo GMEMA_URL; ?>widget/gmema-widget.css" />
			<?php 
		} 
		?>
		<script language="javascript" type="text/javascript" src="<?php echo GMEMA_URL; ?>widget/gmema-widget.js"></script>
		<div>
			<?php if( $gmema_desc <> "" ) { ?>
			<div class="gmema_caption"><?php echo $gmema_desc; ?></div>
			<?php } ?>
			<div class="gmema_msg"><span id="gmema_msg"></span></div>
			<?php if( $gmema_name == "YES" ) { ?>
			<div class="gmema_lablebox"><?php _e('First Name', GMEMA_TDOMAIN); ?></div>
			<div class="gmema_textbox">
				<input class="gmema_textbox_class" name="gmema_txt_first_name" id="gmema_txt_first_name" value="" maxlength="225" type="text">
			</div>
			<div class="gmema_lablebox"><?php _e('Last Name', GMEMA_TDOMAIN); ?></div>
			<div class="gmema_textbox">
				<input class="gmema_textbox_class" name="gmema_txt_last_name" id="gmema_txt_last_name" value="" maxlength="225" type="text">
			</div>
			<?php } ?>
			<div class="gmema_lablebox"><?php _e('Email *', GMEMA_TDOMAIN); ?></div>
			<div class="gmema_textbox">
				<input class="gmema_textbox_class" name="gmema_txt_email" id="gmema_txt_email" onkeypress="if(event.keyCode==13) gmema_submit_page('<?php echo $url; ?>')" value="" maxlength="225" type="text">
			</div>
			<div class="gmema_button">
				<input class="gmema_textbox_button" name="gmema_txt_button" id="gmema_txt_button" onClick="return gmema_submit_page('<?php echo $url; ?>')" value="<?php _e('Subscribe', GMEMA_TDOMAIN); ?>" type="button">
			</div>
			<?php if( $gmema_name != "YES" ) { ?>
				<input name="gmema_txt_name" id="gmema_txt_name" value="" type="hidden">
			<?php } ?>
			<input name="gmema_txt_group" id="gmema_txt_group" value="<?php echo $gmema_group; ?>" type="hidden">
		</div>
		<?php
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) 
	{
		$instance 				= $old_instance;
		$instance['gmema_title'] 	= ( ! empty( $new_instance['gmema_title'] ) ) ? strip_tags( $new_instance['gmema_title'] ) : '';
		$instance['gmema_desc'] 	= ( ! empty( $new_instance['gmema_desc'] ) ) ? strip_tags( $new_instance['gmema_desc'] ) : '';
		$instance['gmema_name'] 	= ( ! empty( $new_instance['gmema_name'] ) ) ? strip_tags( $new_instance['gmema_name'] ) : '';
		$instance['gmema_group'] 	= ( ! empty( $new_instance['gmema_group'] ) ) ? strip_tags( $new_instance['gmema_group'] ) : '';
		return $instance;
	}
	
	function form( $instance ) 
	{
		$defaults = array(
			'gmema_title' => '',
            'gmema_desc' 	=> '',
            'gmema_name' 	=> '',
			'gmema_group' 	=> ''
        );
		$instance 		= wp_parse_args( (array) $instance, $defaults);
		$gmema_title 		= $instance['gmema_title'];
        $gmema_desc 		= $instance['gmema_desc'];
        $gmema_name 		= $instance['gmema_name'];
		$gmema_group 		= $instance['gmema_group'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('gmema_title'); ?>"><?php _e('Widget Title', GMEMA_TDOMAIN); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('gmema_title'); ?>" name="<?php echo $this->get_field_name('gmema_title'); ?>" type="text" value="<?php echo $gmema_title; ?>" />
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('gmema_name'); ?>"><?php _e('Display Name Field', GMEMA_TDOMAIN); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('gmema_name'); ?>" name="<?php echo $this->get_field_name('gmema_name'); ?>">
				<option value="YES" <?php $this->gmema_selected($gmema_name == 'YES'); ?>>YES</option>
				<option value="NO" <?php $this->gmema_selected($gmema_name == 'NO'); ?>>NO</option>
			</select>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('gmema_desc'); ?>"><?php _e('Short Description', GMEMA_TDOMAIN); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('gmema_desc'); ?>" name="<?php echo $this->get_field_name('gmema_desc'); ?>" type="text" value="<?php echo $gmema_desc; ?>" />
			<?php _e('Short description about your subscription form.', GMEMA_TDOMAIN); ?>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('gmema_group'); ?>"><?php _e('Subscriber List', GMEMA_TDOMAIN); ?></label>
			
			<?php
				$myData = array();
				$endpoint_lists = new GloboMailerApi_Endpoint_Lists();
				$response = $endpoint_lists->getLists($pageNumber = 1, $perPage = 100);
				
				$myData = $response->body->toArray();
				if(count($myData) > 0)
				{
					
					echo '<select name="'.$this->get_field_name('gmema_group').'" id="'.$this->get_field_id('gmema_group').'">';
					echo '<option value="">-- Select list --</option>';
					foreach ($myData['data']['records'] as $data)
					{
						$lists_id = $data['general']['list_uid'];
						$lists_name = $data['general']['name'];
				
						$value_list = $gmema_group;
						if($value_list == $lists_id){
							echo '<option value="'.$lists_id.'" selected>'.$lists_name.'</option>';	
						}else{
							echo '<option value="'.$lists_id.'">'.$lists_name.'</option>';
						}
						
					}
					echo '</select>';
				}
			?>
        </p>
		<?php
	}
	
	function gmema_selected($var) 
	{
		if ($var==1 || $var==true) 
		{
			echo 'selected="selected"';
		}
	}
}
?>