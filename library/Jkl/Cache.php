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
             'caching' => $config['cache']['front']['caching'],
             'cache_id_prefix' => 'hhbdpl',
             'logging' => false,
             'write_control' => true,
             'automatic_serialization' => $config['cache']['front']['automatic_serialization'],
             'ignore_user_abort' => true,
             'lifetime' => $config['cache']['front']['lifetime']
         ) );

     return Zend_Cache::factory($oFrontend, $oBackend);
   }

   private function _testCacheEngine($host, $port)
   {
     $memCacheTest = new Memcache();
     if (!($memCacheTest->connect($host, $port))) {
       throw new Jkl_Cache_Exception('Test connection to Memcached failed (' . $host . ':' . $port . ') probably Memcached is not running.', Jkl_Cache_Exception::EXCEPTION_MEMCACHED_CONNECTION_FAILED);
     }
     else {
       $memCacheTest->close();
       unset($memCacheTest);
     }

   }
}
