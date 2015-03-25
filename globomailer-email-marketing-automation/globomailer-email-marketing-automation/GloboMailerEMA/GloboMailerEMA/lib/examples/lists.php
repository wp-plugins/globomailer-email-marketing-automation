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

// create the lists endpoint:
$endpoint = new GloboMailerApi_Endpoint_Lists();

/*===================================================================================*/

// GET ALL ITEMS
$response = $endpoint->getLists($pageNumber = 1, $perPage = 100);

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// get a single list
$response = $endpoint->getList('LIST-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';


/*===================================================================================*/

// delete a list
$response = $endpoint->delete('LIST-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';