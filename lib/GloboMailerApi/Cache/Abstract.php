<?php
/**
 * This file contains the base class for implementing caching in the GloboMailerApi PHP-SDK.
 * 
 * Each class extending this one needs to implement the abstract methods.
 * 
 * @author Incite Minds Ltd <support@globosupport.com>
 * @link http://www.globomailer.com/
 * @copyright 2013-2014 http://www.globomailer.com/
 */
 
 
/**
 * GloboMailerApi_Cache_Abstract is the base class that all the caching classes should extend.
 * 
 * @author Incite Minds Ltd <support@globosupport.com>
 * @package GloboMailerApi
 * @subpackage Cache
 * @since 1.0
 */
abstract class GloboMailerApi_Cache_Abstract extends GloboMailerApi_Base
{
    /**
     * @var array keeps a history of loaded keys for easier and faster reference
     */
    protected $_loaded = array();
    
    /**
     * Set data into the cache
     * 
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    abstract public function set($key, $value);
    
    /**
     * Get data from the cache
     * 
     * @param string $key
     * @return mixed
     */
    abstract public function get($key);
    
    /**
     * Delete data from cache
     * 
     * @param string $key
     * @return bool
     */
    abstract public function delete($key);
    
    /**
     * Delete all data from cache
     * 
     * @return bool
     */
    abstract public function flush();
}