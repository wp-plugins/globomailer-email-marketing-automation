<?php
/**
 * This file contains the countries endpoint for GloboMailerApi PHP-SDK.
 * 
 * @author Incite Minds Ltd <support@globosupport.com>
 * @link http://www.globomailer.com/
 * @copyright 2013-2014 http://www.globomailer.com/
 */
 
 
/**
 * GloboMailerApi_Endpoint_Countries handles all the API calls for handling the countries and their zones.
 * 
 * @author Incite Minds Ltd <support@globosupport.com>
 * @package GloboMailerApi
 * @subpackage Endpoint
 * @since 1.0
 */
class GloboMailerApi_Endpoint_Countries extends GloboMailerApi_Base
{
    /**
     * Get all available countries
     * 
     * Note, the results returned by this endpoint can be cached.
     * 
     * @param integer $page
     * @param integer $perPage
     * @return GloboMailerApi_Http_Response
     */
    public function getCountries($page = 1, $perPage = 10)
    {
        $client = new GloboMailerApi_Http_Client(array(
            'method'        => GloboMailerApi_Http_Client::METHOD_GET,
            'url'           => $this->config->getApiUrl('countries'),
            'paramsGet'     => array(
                'page'      => (int)$page, 
                'per_page'  => (int)$perPage
            ),
            'enableCache'   => true,
        ));
        
        return $response = $client->request();
    }
    
    /**
     * Get all available country zones
     * 
     * Note, the results returned by this endpoint can be cached.
     * 
     * @param integer $countryId
     * @param integer $page
     * @param integer $perPage
     * @return GloboMailerApi_Http_Response
     */
    public function getZones($countryId, $page = 1, $perPage = 10)
    {
        $client = new GloboMailerApi_Http_Client(array(
            'method'        => GloboMailerApi_Http_Client::METHOD_GET,
            'url'           => $this->config->getApiUrl(sprintf('countries/%d/zones', $countryId)),
            'paramsGet'     => array(
                'page'      => (int)$page, 
                'per_page'  => (int)$perPage
            ),
            'enableCache'   => true,
        ));
        
        return $response = $client->request();
    }
}