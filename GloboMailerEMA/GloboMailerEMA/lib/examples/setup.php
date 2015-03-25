<?php
/**
 * This file contains an example of base setup for the GloboMailerApi PHP-SDK.
 *
 * @author Incite Minds Ltd <support@globosupport.com>
 * @link http://www.globomailer.com/
 * @copyright 2013-2014 http://www.globomailer.com/
 */
 
// exit('COMMENT ME TO TEST THE EXAMPLES!');
 
// require the autoloader class
require_once dirname(__FILE__) . '/../GloboMailerApi/Autoloader.php';

// register the autoloader.
GloboMailerApi_Autoloader::register();

// if using a framework that already uses an autoloading mechanism, like Yii for example, 
// you can register the autoloader like:
// Yii::registerAutoloader(array('GloboMailerApi_Autoloader', 'autoloader'), true);

/**
 * Notes: 
 * If SSL present on the webhost, the api can be accessed via SSL as well (https://...).
 * A self signed SSL certificate will work just fine.
 * If the GloboMailer powered website doesn't use clean urls,
 * make sure your apiUrl has the index.php part of url included, i.e: 
 * http://apps.globomailer.com/api
 * 
 * Configuration components:
 * The api for the GloboMailer EMA is designed to return proper etags when GET requests are made.
 * We can use this to cache the request response in order to decrease loading time therefore improving performance.
 * In this case, we will need to use a cache component that will store the responses and a file cache will do it just fine.
 * Please see GloboMailerApi/Cache for a list of available cache components and their usage.
 */

// configuration object
function setConfig($publickey, $privatekey){
	$config = new GloboMailerApi_Config(array(
	    'apiUrl'        => 'http://apps.globomailer.com/api',
	    'publicKey'     => $publickey,// this and the below keys you'll take them from your customers area, in API section.
	    'privateKey'    => $privatekey,
	    
	    // components
	    'components' => array(
	        'cache' => array(
	            'class'     => 'GloboMailerApi_Cache_File',
	            'filesPath' => dirname(__FILE__) . '/../GloboMailerApi/Cache/data/cache', // make sure it is writable by webserver
	        )
	    ),
	));

	// now inject the configuration and we are ready to make api calls
	GloboMailerApi_Base::setConfig($config);	
}

// start UTC
date_default_timezone_set('UTC');