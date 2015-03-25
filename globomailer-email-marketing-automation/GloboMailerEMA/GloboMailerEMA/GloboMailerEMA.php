<?php
/*
Plugin Name: GloboMailerEMA
Plugin URI: http://www.globomailer.com/plugins/wordpress?pk_campaign=wordpress_plugin&pk_kwd=plugins-page
Description: Connect your Wordpress Install with GloboMailer and create email marketing automation. Also use your GloboMailer account and send transactional emails instead of the default emailing system Wordpress uses. 

Add subscription widgets. Using the subscription widget, you can generate a subscription form based on your mail list definition. You also have full control over the generated form, so you can continue changing it until it fits your needs. Also you can use shortcodes within the WYSIWYG editor. For extreme customization of your webforms we have also included a php code generator so you can add your webforms on any location of your theme.
Version: 1.0
Author: Incite Minds Ltd <support@globomailer.com>
Author URI: http://www.globomailer.com/?pk_campaign=wordpress_plugin&pk_kwd=plugins-page
License: MIT http://opensource.org/licenses/MIT
*/


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'define'.DIRECTORY_SEPARATOR.'gmema-defined.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'gmema-stater.php');
add_action('admin_menu', array( 'gmema_cls_registerhook', 'gmema_adminmenu' ));
register_activation_hook(GMEMA_FILE, array( 'gmema_cls_registerhook', 'gmema_activation' ));
register_deactivation_hook(GMEMA_FILE, array( 'gmema_cls_registerhook', 'gmema_deactivation' ));
add_action( 'widgets_init', array( 'gmema_cls_registerhook', 'gmema_widget_loading' ));
add_shortcode( 'GloboMailerEMA', 'gmema_shortcode' );
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'gmema-directly.php');

function gmema_textdomain() 
{
	  load_plugin_textdomain( 'GloboMailerEMA' , false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'gmema_textdomain');
?>