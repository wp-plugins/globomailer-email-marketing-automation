<?php
/**
 * This file contains the base class for the GloboMailerApi PHP-SDK.
 *
 * @author Incite Minds Ltd <support@globosupport.com>
 * @link http://www.globomailer.com/
 * @copyright 2013-2014 http://www.globomailer.com/
 */
 
 
/**
 * GloboMailerApi_Base is the base class for all the other classes used in the sdk.
 * 
 * @author Incite Minds Ltd <support@globosupport.com>
 * @package GloboMailerApi
 * @since 1.0
 */
class GloboMailerApi_Base
{
    /**
     * Marker for before send request event
     */
    const EVENT_BEFORE_SEND_REQUEST = 'beforeSendRequest';
    
    /**
     * Marker for after send request event
     */
    const EVENT_AFTER_SEND_REQUEST = 'afterSendRequest';
    
    /**
     * @var GloboMailerApi_Config the configuration object injected into the application at runtime
     */
    private static $_config;
    
    /**
     * @var GloboMailerApi_Params the package registry that will hold various components
     */
    private static $_registry = array();
    
    /**
     * @var GloboMailerApi_Params the registered event handlers
     */
    private static $_eventHandlers = array();
    
    /**
     * Inject the configuration into the sdk
     * 
     * @param GloboMailerApi_Config $config
     */
    public static function setConfig(GloboMailerApi_Config $config)
    {
        self::$_config = $config;
    }
    
    /**
     * Returns the configuration object
     * 
     * @return GloboMailerApi_Config
     */
    public static function getConfig()
    {
        return self::$_config;
    }
    
    /**
     * Add a new component to the registry
     * 
     * @param string $key
     * @param mixed $value
     * @return GloboMailerApi_Base
     */
    public function addToRegistry($key, $value)
    {
        $this->getRegistry()->add($key, $value);
        return $this;
    }
    
    /**
     * Get the current registry object
     * 
     * @return GloboMailerApi_Params
     */
    public function getRegistry()
    {
        if (!(self::$_registry instanceof GloboMailerApi_Params)) {
            self::$_registry = new GloboMailerApi_Params(self::$_registry);
        }
        return self::$_registry;
    }
    
    /**
     * Set the components used throughout the application lifecyle.
     * 
     * Each component config array needs to have a `class` key containing a class name that can be autoloaded.
     * For example, adding a cache component would be done like : 
     * 
     * <pre>
     * $components = array(
     *     'cache'=>array(
     *         'class'             => 'GloboMailerApi_Cache_Sqlite',
     *         'connectionString'  => 'sqlite:/absolute/path/to/your/sqlite.db',
     *     ),
     * );
     * $context->setComponents($components);
     * </pre>
     * 
     * Please note, if a named component exists, and you assign one with the same name,
     * it will get overriden by the second one.
     * 
     * @param array $components
     * @return GloboMailerApi_Base
     */
    public function setComponents(array $components)
    {
        foreach ($components as $componentName => $config) {
            $this->setComponent($componentName, $config);
        }
        return $this;
    }
    
    /**
     * Set a single component used throughout the application lifecyle.
     * 
     * The component config array needs to have a `class` key containing a class name that can be autoloaded.
     * For example, adding a cache component would be done like : 
     * 
     * <pre>
     * $context->setComponent('cache', array(
     *    'class'             => 'GloboMailerApi_Cache_Sqlite',
     *    'connectionString'  => 'sqlite:/absolute/path/to/your/sqlite.db',    
     * ));
     * </pre>
     * 
     * Please note, if a named component exists, and you assign one with the same name,
     * it will get overriden by the second one.
     * 
     * @param string $componentName the name of the component accessed later via $context->componentName
     * @param array $config the component configuration array
     * @return GloboMailerApi_Base
     */
    public function setComponent($componentName, array $config)
    {
        if (empty($config['class'])) {
            throw new Exception('Please set the class property for "'.htmlspecialchars($componentName, ENT_QUOTES, $this->getConfig()->getCharset()).'" component.');
        }
        $component = new $config['class'];
        if ($component instanceof GloboMailerApi_Base) {
            $component->populateFromArray($config);
        } else {
            unset($config['class']);
            foreach ($config as $property => $value) {
                if (property_exists($component, $property)) {
                    $reflection = new ReflectionProperty($component, $property);
                    if ($reflection->isPublic()) {
                        $component->$property = $value;
                    }
                }
            }
        }
        $this->addToRegistry($componentName, $component);
        return $this;
    }
    
    /**
     * Register one or more callbacks/event handlers for the given event(s)
     * 
     * A valid registration would be: 
     * 
     * <pre>
     * $eventHandlers = array(
     *     'eventName1' => array($object, 'method'),
     *     'eventName2' => array(
     *         array($object, 'method'),
     *         array($object, 'otherMethod'),
     *     )
     * );
     * </pre>
     * 
     * @param array $eventHandlers
     * @return GloboMailerApi_Base
     */
    public function setEventHandlers(array $eventHandlers)
    {
        foreach ($eventHandlers as $eventName => $callback) {
            if (empty($callback) || !is_array($callback)) {
                continue;
            }
            if (!is_array($callback[0]) && is_callable($callback)) {
                $this->getEventHandlers($eventName)->add(null, $callback);
                continue;
            }
            if (is_array($callback[0])) {
                foreach ($callback as $cb) {
                    if (is_callable($cb)) {
                        $this->getEventHandlers($eventName)->add(null, $cb);
                    }
                }    
            }
        }
        return $this;
    }
    
    /**
     * Return a list of callbacks/event handlers for the given event
     * 
     * @param string $eventName
     * @return GloboMailerApi_Params
     */
    public function getEventHandlers($eventName) 
    {
        if (!(self::$_eventHandlers instanceof GloboMailerApi_Params)) {
            self::$_eventHandlers = new GloboMailerApi_Params(self::$_eventHandlers);
        }
        
        if (!self::$_eventHandlers->contains($eventName) || !(self::$_eventHandlers->itemAt($eventName) instanceof GloboMailerApi_Params)) {
            self::$_eventHandlers->add($eventName, new GloboMailerApi_Params());
        }
        
        return self::$_eventHandlers->itemAt($eventName);
    }
    
    /**
     * Remove all the event handlers bound to the event name
     * 
     * @param string $eventName
     * @return GloboMailerApi_Base
     */
    public function removeEventHandlers($eventName)
    {
        self::$_eventHandlers->remove($eventName);
        return $this;
    }
    
    /**
     * Called from within a child class, will populate 
     * all the setters matching the array keys with the array values
     * 
     * @param mixed $params
     * @return GloboMailerApi_Base
     */
    protected function populateFromArray(array $params = array())
    {
        foreach ($params as $name => $value) {
            
            $found = false;

            if (property_exists($this, $name)) {
                $param = $name;
            } else {
                $asSetterName = str_replace('_', ' ', $name);
                $asSetterName = ucwords($asSetterName);
                $asSetterName = str_replace(' ', '', $asSetterName);
                $asSetterName{0} = strtolower($asSetterName{0});
                $param = property_exists($this, $asSetterName) ? $asSetterName : null;
            }

            if ($param) {
                $reflection = new ReflectionProperty($this, $param);
                if ($reflection->isPublic()) {
                    $this->$param = $value;
                    $found = true;
                }    
            }
            
            if (!$found) {
                $methodName = str_replace('_', ' ', $name);
                $methodName = ucwords($methodName);
                $methodName = str_replace(' ', '', $methodName);
                $methodName = 'set'.$methodName;

                if (method_exists($this, $methodName)) {
                    $reflection = new ReflectionMethod($this, $methodName);
                    if ($reflection->isPublic()) {
                        $this->$methodName($value);
                    }
                }     
            }
        }
        
        return $this;
    }
    
    /**
     * Magic setter
     * 
     * This method should never be called directly from outside of the class.
     * 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $methodName = 'set'.ucfirst($name);
        if (!method_exists($this, $methodName)) {
            $this->addToRegistry($name, $value);
        } else {
            $method = new ReflectionMethod($this, $methodName);
            if ($method->isPublic()) {
                $this->$methodName($value);
            }
        }
    }
    
    /**
     * Magic getter
     * 
     * This method should never be called directly from outside of the class.
     * 
     * @param string $name
     */
    public function __get($name)
    {
        $methodName = 'get'.ucfirst($name);
        if (!method_exists($this, $methodName) && $this->getRegistry()->contains($name)) {
            return $this->getRegistry()->itemAt($name);
        } elseif (method_exists($this, $methodName)) {
            $method = new ReflectionMethod($this, $methodName);
            if ($method->isPublic()) {
                return $this->$methodName();
            }
        }
    }
}