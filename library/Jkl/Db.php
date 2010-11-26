<?php

/**
* 
*/
class Jkl_Db extends Jkl_Cache
{
  private $_db;
  private $_queryCount = 0;
  
  static private $_instance;
  
  /**
   * Singleton instance
   *
   * @return Jkl_Db
   */
  public static function getInstance($adapter, $params)
  {
      if (null === self::$_instance) {
          self::$_instance = new self($adapter, $params);
      }

      return self::$_instance;
  }  
  
  function __construct($adapter, $params) {
    $this->_db = Zend_Db::factory($adapter, $params);
    parent::__construct();
  }
  
  /*
  * FechtAll with memcached support
  */
  public function fetchAll($query)
  {
    $this->_queryCount++;
    
    $result = unserialize($this->_cache->load(md5($query)));
    if (empty($result)) {
      $result = $this->_db->fetchAll($query);
      $this->_cache->save(serialize($result), md5($query));
    }
    return $result;
  }
  
  public function query($query)
  {
    $this->_queryCount++;
    return $this->_db->query($query);
  }
  
  public function getQueryCount()
  {
    return $this->_queryCount;
  }
  
}
