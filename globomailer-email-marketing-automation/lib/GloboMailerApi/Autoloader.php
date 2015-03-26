<?php
/**
 * This file contains the autoloader class for the GloboMailerApi PHP-SDK.
 *
 * @author Incite Minds Ltd <support@globosupport.com>
 * @link http://www.globomailer.com/
 * @copyright 2013-2014 http://www.globomailer.com/
 */
 
 
/**
 * The GloboMailerApi Autoloader class.
 * 
 * From within a Yii Application, you would load this as:
 * 
 * <pre>
 * require_once(Yii::getPathOfAlias('application.vendors.GloboMailerApi.Autoloader').'.php');
 * Yii::registerAutoloader(array('GloboMailerApi_Autoloader', 'autoloader'), true);
 * </pre>
 * 
 * Alternatively you can:
 * <pre>
 * require_once('Path/To/GloboMailerApi/Autoloader.php');
 * GloboMailerApi_Autoloader::register();
 * </pre>
 * 
 * @author Incite Minds Ltd <support@globosupport.com>
 * @package GloboMailerApi
 * @since 1.0
 */
class GloboMailerApi_Autoloader
{
    /**
     * The registrable autoloader
     * 
     * @param string $class
     */
    public static function autoloader($class)
    {
        if (strpos($class, 'GloboMailerApi') === 0) {
            $className = str_replace('_', '/', $class);
            $className = substr($className, 15);
            if (is_file($classFile = dirname(__FILE__) . '/'. $className.'.php')) {
                require_once($classFile);
            }
        }
    }
    
    /**
     * Registers the GloboMailerApi_Autoloader::autoloader()
     */
    public static function register()
    {
        spl_autoload_register(array('GloboMailerApi_Autoloader', 'autoloader'));
    }
}