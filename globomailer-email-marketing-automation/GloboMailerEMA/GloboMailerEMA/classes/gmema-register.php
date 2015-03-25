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
		
		$data = gmema_cls_settings::gmema_setting_select(1);
		$post = get_post($post_id);
		$endpoint_lists = new GloboMailerApi_Endpoint_Lists();
		$response_lists = $endpoint_lists->getList($value_list);
		$myData_body = $response_lists->body;
		
		if ($myData_body->itemAt('status') == 'success') {
			$myData_data = $myData_body->itemAt('data');
			$form_data = $myData_data['record'];
			
			if($value_list && $send_mail == 'SEND' && $value_trans == 'yes'){
				$endpoint_cmp = new GloboMailerApi_Endpoint_Campaigns();
				
				$response_cmp = $endpoint_cmp->create(array(
				    'name'          => $post->title,
				    'from_name'     => $form_data['defaults']['from_name'],
				    'from_email'    => $form_data['defaults']['reply_to'],
				    'subject'       => $post->title,
				    'reply_to'      => $form_data['defaults']['reply_to'],
				    'send_at'       => date('Y-m-d H:i:s', strtotime('+10 hours')),
				    'list_uid'      => $value_list,
				    'segment_uid'   => '',
				    'options' => array(
				        'url_tracking'      => 'no',
				        'json_feed'         => 'no',
				        'xml_feed'          => 'no',
				        'plain_text_email'  => 'yes',
				        'email_stats'       => null,
				    ),
				    'template' => array(
				        'content'           => str_replace("\r\n", "<br />", $post->post_content),
				        'inline_css'        => 'no',
				        'plain_text'        => null,
				        'auto_plain_text'   => 'yes',
				    ),
				));
			}else if($value_list && $send_mail == 'SEND' && $value_trans == 'no'){
				$endpoint_subs = new GloboMailerApi_Endpoint_ListSubscribers();
				$response_subs = $endpoint_subs->getSubscribers($value_list, $pageNumber = 1, $perPage = 100);
				$subs_body = $response_subs->body;
				$subs_data = $subs_body->itemAt('data');
				$subscribers = $subs_data['records'];
				
				foreach($subscribers as $subscriber){
					$subscriber_list[] = $subscriber['EMAIL'];
				}
				$headers .= "From: ".$form_data['defaults']['from_name']." <".$form_data['defaults']['reply_to']."> \r\n";
				$headers .= 'Reply-To: '.$form_data['defaults']['reply_to']."\r\n";
				$headers .= "MIME-Version: 1.0\n";
				$headers .= "Content-Type: " . get_bloginfo('html_type') . "; charset=\"". get_bloginfo('charset') . "\"\n";
				$headers .= "Content-type: text/html\r\n";
				
				$reciepients = implode(",", $subscriber_list);
				$to = $reciepients;
				$subject = $post->title;
				$content = str_replace("\r\n", "<br />", $post->post_content);
				wp_mail($to, $subject, $content, $headers);
			}	
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
		_e( 'Create campaign: ', GMEMA_TDOMAIN );
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
			__( 'GloboMailerEMA', GMEMA_TDOMAIN ), 'admin_dashboard', 'GloboMailerEMA', 'gmema_admin_option', GMEMA_URL.'img/ico.png', 3 );
		
		add_submenu_page('GloboMailerEMA', __( 'Home', GMEMA_TDOMAIN ), 
			__( 'Home', GMEMA_TDOMAIN ), 'read', 'gmema-home', array( 'gmema_cls_intermediate', 'gmema_home' ));
		
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

class gmema_widget_register extends WP_Widget 
{
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'gmema', // Base ID
            __('GloboMailerEMA Subscription Widget', 'gmema'),
            array( 'description' => '', ) // Args
        );
    }
    
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) 
    {
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }    
		
		$nonce      = wp_create_nonce(basename(__FILE__));
		$nonceField = '<input type="hidden" name="gmema_form_nonce" value="'.$nonce.'" />';
		$form       = $instance['generated_form'];
		$form       = str_replace('</form>', "\n" . $nonceField . "\n</form>", $form);
        ?>
        <div class="gmema-widget" data-ajaxurl="<?php echo admin_url('admin-ajax.php'); ?>">
            <div class="message"></div>
            <?php echo $form;?>
        </div>
        <?php
        echo $args['after_widget'];
    }
    
    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) 
    {
        $title              = isset($instance['title'])                 ? $instance['title']                : null;
        $apiUrl             = isset($instance['api_url'])               ? $instance['api_url']              : null;
        $publicKey          = isset($instance['public_key'])            ? $instance['public_key']           : null;
        $privateKey         = isset($instance['private_key'])           ? $instance['private_key']          : null;
        $listUid            = isset($instance['list_uid'])              ? $instance['list_uid']             : null;
        $listSelectedFields = isset($instance['selected_fields'])       ? $instance['selected_fields']      : array();
        $generatedForm      = isset($instance['generated_form'])        ? $instance['generated_form']       : '';
        
        $freshLists = array(
            array('list_uid' => null, 'name' => __('Please select', 'gmema'))
        );
        $freshFields = array();
        
        if (!empty($apiUrl) && !empty($publicKey) && !empty($privateKey)) {
            
            $endpoint = new GloboMailerApi_Endpoint_Lists();
            $response = $endpoint->getLists(1, 50);
            $response = $response->body->toArray();

            if (isset($response['status']) && $response['status'] == 'success' && !empty($response['data']['records'])) {
                foreach ($response['data']['records'] as $list) {
                    $freshLists[] = array(
                        'list_uid'  => $list['general']['list_uid'],
                        'name'      => $list['general']['name']
                    );
                }
            }
            
            if (!empty($listUid)) {
                $endpoint = new GloboMailerApi_Endpoint_ListFields();
                $response = $endpoint->getFields($listUid);
                $response = $response->body->toArray();
                
                if (isset($response['status']) && $response['status'] == 'success' && !empty($response['data']['records'])) {
                    foreach ($response['data']['records'] as $field) {
                        $freshFields[] = $field;
                    }
                }
            }
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('Title:'); ?></strong></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('api_url'); ?>"><strong><?php _e('Api url:'); ?></strong></label> 
            <input class="widefat gmema-api-url" id="<?php echo $this->get_field_id('api_url'); ?>" name="<?php echo $this->get_field_name('api_url'); ?>" type="text" value="<?php echo esc_attr($apiUrl); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('public_key'); ?>"><strong><?php _e('Public api key:'); ?></strong></label> 
            <input class="widefat gmema-public-key" id="<?php echo $this->get_field_id('public_key'); ?>" name="<?php echo $this->get_field_name('public_key'); ?>" type="text" value="<?php echo esc_attr($publicKey); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('private_key'); ?>"><strong><?php _e('Private api key:'); ?></strong></label> 
            <input class="widefat gmema-private-key" id="<?php echo $this->get_field_id('private_key'); ?>" name="<?php echo $this->get_field_name('private_key'); ?>" type="text" value="<?php echo esc_attr($privateKey); ?>" />
        </p>
        
        <div class="widget-control-actions">
            <div class="alignleft"></div>
            <div class="alignright">
                 <input type="submit" class="button button-primary right gmema-fetch-available-lists" value="Fetch available lists">            
               <span class="spinner gmema-spinner" style="display: none;"></span>
            </div>
            <br class="clear">
        </div>
        
        <div class="lists-container" style="<?php echo !empty($freshFields) ? 'display:block':'display:none';?>; margin:0; float:left; width:100%">
            <label for="<?php echo $this->get_field_id('list_uid'); ?>"><strong><?php _e('Select a list:'); ?></strong></label> 
            <select data-listuid="<?php echo esc_attr($listUid); ?>" data-fieldname="<?php echo $this->get_field_name('selected_fields');?>" class="widefat gmema-mail-lists-dropdown" id="<?php echo $this->get_field_id('list_uid'); ?>" name="<?php echo $this->get_field_name('list_uid'); ?>">
            <?php foreach ($freshLists as $list) { ?>
            <option value="<?php echo $list['list_uid'];?>"<?php if ($listUid == $list['list_uid']) { echo ' selected="selected"';}?>><?php echo $list['name'];?></option>
            <?php } ?>
            </select>
            <br class="clear"/>
            <br class="clear"/>
        </div>
        
        <div class="fields-container" style="<?php echo !empty($listUid) ? 'display:block':'display:none';?>; margin:0; float:left; width:100%">
            <label for="<?php echo $this->get_field_id('selected_fields'); ?>"><strong><?php _e('Fields:'); ?></strong></label> 
            <div class="table-container" style="width:100%;max-height:200px; overflow-y: scroll">
                <?php gmema_generate_fields_table($freshFields, $this->get_field_name('selected_fields'), $listSelectedFields);?>
            </div>
            <br class="clear">
            <div style="float: right;">
                Generate form again: <input name="<?php echo $this->get_field_name('generate_new_form'); ?>" value="1" type="checkbox" checked="checked"/>
            </div>
            <br class="clear">
        </div>
        
        <div class="generated-form-container" style="<?php echo !empty($listUid) ? 'display:block':'display:none';?>; margin:0; float:left; width:100%">
            <label for="<?php echo $this->get_field_id('generated_form'); ?>"><strong><?php _e('Generated form:'); ?></strong></label> 
            <textarea name="<?php echo $this->get_field_name('generated_form'); ?>" id="<?php echo $this->get_field_id('generated_form'); ?>" style="width: 100%; height: 200px; resize:none; outline:none"><?php echo $generatedForm;?></textarea>
        </div>
        
        <hr />
        <?php 
    }
    
    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) 
    {
        $instance = array();
        
        $instance['title']          = !empty($new_instance['title'])        ? sanitize_text_field($new_instance['title'])       : '';
        $instance['api_url']        = !empty($new_instance['api_url'])      ? sanitize_text_field($new_instance['api_url'])     : '';
        $instance['public_key']     = !empty($new_instance['public_key'])   ? sanitize_text_field($new_instance['public_key'])  : '';
        $instance['private_key']    = !empty($new_instance['private_key'])  ? sanitize_text_field($new_instance['private_key']) : '';
        $instance['list_uid']       = !empty($new_instance['list_uid'])     ? sanitize_text_field($new_instance['list_uid'])    : '';
        $instance['uid']            = !isset($old_instance['uid'])          ? uniqid()                                          : $old_instance['uid'];
        
        $instance['selected_fields'] = !empty($new_instance['selected_fields']) && is_array($new_instance['selected_fields']) ? array_map('sanitize_text_field', $new_instance['selected_fields']) : array();
 
        update_option('gmema_widget_instance_' . $instance['uid'], array(
            'api_url'       => $instance['api_url'],
            'public_key'    => $instance['public_key'],
            'private_key'   => $instance['private_key'],
            'list_uid'      => $instance['list_uid']
        ));
        
        if (!empty($new_instance['generate_new_form'])) {
            $instance['generated_form'] = $this->generateForm($instance);    
        } else {
            $instance['generated_form'] = !empty($new_instance['generated_form']) ? $new_instance['generated_form'] : '';
        }
        
        return $instance;
    }
    
    /**
     * Helper method to generate the html form that will be pushed in the widgets area in frontend.
     * It exists so that we don't have to generate the html at each page load.
     */
    protected function generateForm(array $instance)
    {
        if (empty($instance['list_uid']) || empty($instance['public_key']) || empty($instance['private_key'])) {
            return;
        }
        
        $endpoint = new GloboMailerApi_Endpoint_ListFields();
        $response = $endpoint->getFields($instance['list_uid']);
        $response = $response->body->toArray();
        
        if (!isset($response['status']) || $response['status'] != 'success' || empty($response['data']['records'])) {
            return;
        }
        
        $freshFields    = $response['data']['records'];
        $selectedFields = !empty($instance['selected_fields']) ? $instance['selected_fields'] : array();
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
        
        $out = '<form method="post" data-uid="'.$instance['uid'].'">' . "\n\n";
        $out .= implode("\n\n", $output);
        $out .= "\n\n";
        $out .= '<div class="clearfix"><!-- --></div><div class="actions pull-right"><button type="submit" class="btn btn-default btn-submit">Subscribe</button></div><div class="clearfix"><!-- --></div>';
        $out .= "\n\n" . '</form>';
        
        return $out;
    }
}

// register admin assets
add_action('admin_enqueue_scripts', 'gmema_load_admin_assets');
function gmema_load_admin_assets() {
    wp_register_script('gmema-admin', GMEMA_URL.'js/admin.js', array('jquery'), '1.0', true);
    wp_enqueue_script('gmema-admin');
}

// register frontend assets
add_action('wp_enqueue_scripts', 'gmema_load_frontend_assets');
function gmema_load_frontend_assets() {
    wp_register_style('gmema-front', GMEMA_URL.'css/front.css', array(), '1.0');
    wp_register_script('gmema-front', GMEMA_URL.'js/front.js', array('jquery'), '1.0', true);
    wp_enqueue_style('gmema-front');
    wp_enqueue_script('gmema-front');
}

// register ajax actions
// fetch the lists available for given api data
add_action('wp_ajax_gmema_fetch_lists', 'gmema_fetch_lists_callback');
function gmema_fetch_lists_callback() {
    $apiUrl     = isset($_POST['api_url'])      ? sanitize_text_field($_POST['api_url'])        : null;
    $publicKey  = isset($_POST['public_key'])   ? sanitize_text_field($_POST['public_key'])     : null;
    $privateKey = isset($_POST['private_key'])  ? sanitize_text_field($_POST['private_key'])    : null;
    
    $errors = array();
    if (empty($apiUrl) || !filter_var($apiUrl, FILTER_VALIDATE_URL)) {
        $errors['api_url'] = __('Please type a valid API url!', 'gmema');
    }
    if (empty($publicKey) || strlen($publicKey) != 40) {
        $errors['public_key'] = __('Please type a public API key!', 'gmema');
    }
    if (empty($privateKey) || strlen($privateKey) != 40) {
        $errors['private_key'] = __('Please type a private API key!', 'gmema');
    }
    if (!empty($errors)) {
        exit(GloboMailerApi_Json::encode(array(
            'result' => 'error',
            'errors' => $errors,
        )));
    }

    $endpoint = new GloboMailerApi_Endpoint_Lists();
    $response = $endpoint->getLists(1, 50);
    $response = $response->body->toArray();
    
    if (!isset($response['status']) || $response['status'] != 'success') {
        exit(GloboMailerApi_Json::encode(array(
            'result' => 'error',
            'errors' => array(
                'general'   => isset($response['error']) ? $response['error'] : __('Invalid request!', 'gmema'),
            ),
        )));
    }
    
    if (empty($response['data']['records']) || count($response['data']['records']) == 0) {
        exit(GloboMailerApi_Json::encode(array(
            'result' => 'error',
            'errors' => array(
                'general'   => __('We couldn\'t find any mail list, are you sure you have created one?', 'gmema'),
            ),
        )));
    }
    
    $lists = array(
        array(
            'list_uid'  => null, 
            'name'      => __('Please select', 'gmema')
        )
    );
    
    foreach ($response['data']['records'] as $list) {
        $lists[] = array(
            'list_uid'  => $list['general']['list_uid'],
            'name'      => $list['general']['name']
        );
    }
    
    exit(GloboMailerApi_Json::encode(array(
        'result' => 'success',
        'lists' => $lists,
    )));
}

// fetch list fields
add_action('wp_ajax_gmema_fetch_list_fields', 'gmema_fetch_list_fields_callback');
function gmema_fetch_list_fields_callback() {
    $apiUrl     = isset($_POST['api_url'])      ? sanitize_text_field($_POST['api_url'])        : null;
    $publicKey  = isset($_POST['public_key'])   ? sanitize_text_field($_POST['public_key'])     : null;
    $privateKey = isset($_POST['private_key'])  ? sanitize_text_field($_POST['private_key'])    : null;
    $listUid    = isset($_POST['list_uid'])     ? sanitize_text_field($_POST['list_uid'])       : null;
    $fieldName  = isset($_POST['field_name'])   ? sanitize_text_field($_POST['field_name'])     : null;

    if (
        empty($apiUrl)      || !filter_var($apiUrl, FILTER_VALIDATE_URL) || 
        empty($publicKey)   || strlen($publicKey)   != 40 || 
        empty($privateKey)  || strlen($privateKey)  != 40 || 
        empty($listUid)     || empty($fieldName)
    ) {
        die();
    }
    
    $endpoint = new GloboMailerApi_Endpoint_ListFields();
    $response = $endpoint->getFields($listUid);
    $response = $response->body->toArray();
    
    if (!isset($response['status']) || $response['status'] != 'success' || empty($response['data']['records']) || count($response['data']['records']) == 0) {
        die();
    }
    gmema_generate_fields_table($response['data']['records'], $fieldName, array());
    die();
}


// subscribe a user in given list
add_action('wp_ajax_gmema_subscribe', 'gmema_subscribe_callback');
add_action('wp_ajax_nopriv_gmema_subscribe', 'gmema_subscribe_callback');
function gmema_subscribe_callback() {
	if (!isset($_POST['gmema_form_nonce']) || !wp_verify_nonce($_POST['gmema_form_nonce'], basename(__FILE__))) {
		exit(GloboMailerApi_Json::encode(array(
            'result'    => 'error', 
            'message'   => __('Invalid nonce!', 'gmema')
        )));
	}

    $uid = isset($_POST['uid']) ? sanitize_text_field($_POST['uid']) : null;
    if ($uid) {
        unset($_POST['uid']);
    }
    unset($_POST['action'], $_POST['gmema_form_nonce']);
    
    if (empty($uid) || !($uidData = get_option('gmema_widget_instance_' . $uid))) {
        exit(GloboMailerApi_Json::encode(array(
            'result'    => 'error', 
            'message'   => __('Please try again later!', 'gmema')
        )));
    }
    
    $keys = array('api_url', 'public_key', 'private_key', 'list_uid');
    foreach ($keys as $key) {
        if (!isset($uidData[$key])) {
            exit(GloboMailerApi_Json::encode(array(
                'result'    => 'error', 
                'message'   => __('Please try again later!', 'gmema')
            )));
        }
    }
    
    $endpoint = new GloboMailerApi_Endpoint_ListSubscribers();
    $response = $endpoint->create($uidData['list_uid'], $_POST);
    $response = $response->body->toArray();
    
    if (isset($response['status']) && $response['status'] == 'error' && isset($response['error'])) {
        $errorMessage = $response['error'];
        if (is_array($errorMessage)) {
            $errorMessage = implode("\n", array_values($errorMessage));
        }
        exit(GloboMailerApi_Json::encode(array(
            'result'    => 'error', 
            'message'   => $errorMessage
        )));
    }
    
    if (isset($response['status']) && $response['status'] == 'success') {
        $data = gmema_cls_settings::gmema_setting_select(1);		        
		
		$endpoint_lists = new GloboMailerApi_Endpoint_Lists();
        $response_lists = $endpoint_lists->getList($uidData['list_uid']);
		$myData_body = $response_lists->body;
		
		if ($myData_body->itemAt('status') == 'success') {
			$myData_data = $myData_body->itemAt('data');
			$form_data = $myData_data['record'];
	        
	        if($data['transact'] == 'SEND'){
		        $fromnm = $_POST['FNAME'].' '.$_POST['LNAME'];
		        if(!$_POST['FNAME']){
					$fromnm = 'Not provided';
				}
		        
		        $endpoint_trans   = new GloboMailerApi_Endpoint_TransactionalEmails();
			    $response_trans = $endpoint_trans->create(array(
				    'to_name'           => $fromnm,
				    'to_email'          => $_POST['EMAIL'],
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
        
        exit(GloboMailerApi_Json::encode(array(
            'result'    => 'success', 
            'message'   => __('Please check your email to confirm the subscription!', 'gmema')
        )));
    }
    
    exit(GloboMailerApi_Json::encode(array(
        'result'    => 'success', 
        'message'   => $response->itemAt('error')
    )));
}

// admin notice if cache folder not writable.
function gmema_admin_notice() {
    global $pagenow;
    if ($pagenow != 'widgets.php') {
        return;
    }
    if (is_writable($cacheDir = GMEMA_DIR . '/lib/GloboMailerApi/Cache/data/cache')) {
        return;
    }
    ?>
    <div class="error">
        <p><?php _e('Permissions error!', 'gmema'); ?></p>
        <p><?php _e('The directory "<strong>'.$cacheDir.'</strong>" must be writable by the web server (chmod -R 0777)!', 'gmema'); ?></p>
        <p><?php _e('Please fix this error now.', 'gmema'); ?></p>
    </div>
    <?php
}
add_action('admin_notices', 'gmema_admin_notice');

// small function to generate our fields table.
function gmema_generate_fields_table(array $freshFields = array(), $fieldName, array $listSelectedFields = array()) {
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <th width="40" align="left"><?php echo  __('Show', 'gmema');?></th>
            <th width="60" align="left"><?php echo  __('Required', 'gmema');?></th>
            <th align="left"><?php echo  __('Label', 'gmema');?></th>
        </thead>
        <tbody>
            <?php foreach ($freshFields as $field) { ?>
            <tr>
                <td width="40" align="left"><input name="<?php echo $fieldName; ?>[]" value="<?php echo $field['tag']?>" type="checkbox"<?php echo empty($listSelectedFields) || in_array($field['tag'], $listSelectedFields) ? ' checked="checked"':''?>/></td>
                <td width="60" align="left"><?php echo $field['required'];?></td>
                <td align="left"><?php echo $field['label'];?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
}

?>