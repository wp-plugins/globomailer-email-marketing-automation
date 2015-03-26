<?php
/**
 * This file contains the configuration class for the GloboMailerApi PHP-SDK.
 *
 * @author Incite Minds Ltd <support@globosupport.com>
 * @link http://www.globomailer.com/
 * @copyright 2013-2014 http://www.globomailer.com/
 */
 
 
/**
 * GloboMailerApi_Config contains the configuration class that is injected at runtime into the main application.
 * 
 * It's only purpose is to set the needed data so that the API calls will run without problems.
 * 
 * @author Incite Minds Ltd <support@globosupport.com>
 * @package GloboMailerApi
 * @since 1.0
 */
class GloboMailerApi_Config extends GloboMailerApi_Base
{
    /**
     * @var string the api public key
     */
    public $publicKey;
    
    /**
     * @var string the api private key.
     */
    public $privateKey;

    /**
     * @var string the preffered charset.
     */
    public $charset = 'utf-8';
    
    /**
     * @var string the API url.
     */
    private $_apiUrl;

    /**
     * Constructor
     * @param array the config array that will populate the class properties.
     */
    public function __construct(array $config = array())
    {
        $this->populateFromArray($config);
    }

    /**
     * Setter for the API url.
     * 
     * Please note, this url should NOT contain any endpoint, 
     * just the base url to the API.
     * 
     * Also, a basic url check is done, but you need to make sure the url is valid.
     * 
     * @param mixed $url
     * @return GloboMailerApi_Config
     */
    public function setApiUrl($url)
    {
        if (!parse_url($url, PHP_URL_HOST)) {
            throw new Exception('Please set a valid api base url.');
        }

        $this->_apiUrl = trim($url, '/') . '/';
        return $this;
    }
    
    /**
     * Getter for the API url.
     * 
     * Also, you can use the $endpoint param to point the request to a certain endpoint.
     * 
     * @param string $endpoint
     * @return string
     */
    public function getApiUrl($endpoint = null)
    {
        if ($this->_apiUrl === null) {
            throw new Exception('Please set the api base url.');
        }
        
        return $this->_apiUrl . $endpoint;
    }
}