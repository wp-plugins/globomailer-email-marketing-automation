<?php
/**
 * This file contains the transactional emails endpoint for GloboMailerApi PHP-SDK.
 * 
 * @author Incite Minds Ltd <support@globosupport.com>
 * @link http://www.globomailer.com/
 * @copyright 2013-2014 http://www.globomailer.com/
 */
 
 
/**
 * GloboMailerApi_Endpoint_TransactionalEmails handles all the API calls for transactional emails.
 * 
 * @author Incite Minds Ltd <support@globosupport.com>
 * @package GloboMailerApi
 * @subpackage Endpoint
 * @since 1.0
 */
class GloboMailerApi_Endpoint_TransactionalEmails extends GloboMailerApi_Base
{
    /**
     * Get all transactional emails of the current customer
     * 
     * Note, the results returned by this endpoint can be cached.
     * 
     * @param integer $page
     * @param integer $perPage
     * @return GloboMailerApi_Http_Response
     */
    public function getEmails($page = 1, $perPage = 10)
    {
        $client = new GloboMailerApi_Http_Client(array(
            'method'        => GloboMailerApi_Http_Client::METHOD_GET,
            'url'           => $this->config->getApiUrl('transactional-emails'),
            'paramsGet'     => array(
                'page'      => (int)$page, 
                'per_page'  => (int)$perPage
            ),
            'enableCache'   => true,
        ));
        
        return $response = $client->request();
    }
    
    /**
     * Get one transactional email
     * 
     * Note, the results returned by this endpoint can be cached.
     * 
     * @param string $emailUid
     * @return GloboMailerApi_Http_Response
     */
    public function getEmail($emailUid)
    {
        $client = new GloboMailerApi_Http_Client(array(
            'method'        => GloboMailerApi_Http_Client::METHOD_GET,
            'url'           => $this->config->getApiUrl(sprintf('transactional-emails/%s', (string)$emailUid)),
            'paramsGet'     => array(),
            'enableCache'   => true,
        ));
        
        return $response = $client->request();
    }
    
    /**
     * Create a new transactional email
     * 
     * @param array $data
     * @return GloboMailerApi_Http_Response
     */
    public function create(array $data)
    {
        if (!empty($data['body'])) {
            $data['body'] = base64_encode($data['body']);
        }
        
        if (!empty($data['plain_text'])) {
            $data['plain_text'] = base64_encode($data['plain_text']);
        }
        
        $client = new GloboMailerApi_Http_Client(array(
            'method'        => GloboMailerApi_Http_Client::METHOD_POST,
            'url'           => $this->config->getApiUrl('transactional-emails'),
            'paramsPost'    => array(
                'email'  => $data
            ),
        ));
        
        return $response = $client->request();
    }
    
    /**
     * Delete existing transactional email
     * 
     * @param string $emailUid
     * @return GloboMailerApi_Http_Response
     */
    public function delete($emailUid)
    {
        $client = new GloboMailerApi_Http_Client(array(
            'method'    => GloboMailerApi_Http_Client::METHOD_DELETE,
            'url'       => $this->config->getApiUrl(sprintf('transactional-emails/%s', $emailUid)),
        ));
        
        return $response = $client->request();
    }
}