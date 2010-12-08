<?php

/**
* 
*/
class Jkl_Cache
{
  public $_cache;
  static private $_instance;
  
  function __construct()
  {
     $this->_cache = $this->_initMemcached();
  }
  
  /**
   * Singleton instance
   *
   * @return Jkl_Db
   */
  public static function getInstance()
  {
      if (null === self::$_instance) {
          self::$_instance = new self();
      }
      return self::$_instance;
  }
  
  private function _initMemcached()
   {
     $config = Zend_Registry::get('Config_App');

     // test if we have connection to memCached, should be turned off after some time
     $this->_testCacheEngine($config['cache']['backend']['host'], $config['cache']['backend']['port']);

     $oBackend = new Zend_Cache_Backend_Memcached(array(
           'servers' =>array(
             array(
             'host' => $config['cache']['backend']['host'],
             'port' => $config['cache']['backend']['port']
             )
           ),
           'compression' => $config['cache']['backend']['compression']
         ));

     $oFrontend = new Zend_Cache_Core(
         array(
             'caching' => true,
             'cache_id_prefix' => 'hhbdpl',
             'logging' => false,
             'write_control' => true,
             'automatic_serialization' => true,
             'ignore_user_abort' => true,
             'lifetime' => 3600
         ) );

     return Zend_Cache::factory($oFrontend, $oBackend);
   }

   private function _testCacheEngine($host, $port)
   {
     $memCacheTest = new Memcache();
     if (!($memCacheTest->connect($host, $port))) {
       throw new Jkl_Exception('Test connection to Memcached failed (' . $host . ':' . $port . ') probably Memcached is not running.');
     }
     else {
       $memCacheTest->close();
       unset($memCacheTest);
     }
   }
}
