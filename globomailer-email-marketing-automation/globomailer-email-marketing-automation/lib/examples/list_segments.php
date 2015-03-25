<?php
/**
 * This file contains examples for using the GloboMailerApi PHP-SDK.
 *
 * @author Incite Minds Ltd <support@globosupport.com>
 * @link http://www.globomailer.com/
 * @copyright 2013-2014 http://www.globomailer.com/
 */
 
// require the setup which has registered the autoloader
require_once dirname(__FILE__) . '/setup.php';

// CREATE THE ENDPOINT
$endpoint = new GloboMailerApi_Endpoint_ListSegments();

/*===================================================================================*/

// GET ALL ITEMS
$response = $endpoint->getSegments('LIST-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';