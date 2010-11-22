<?php
/**
* 
*/
abstract class Jkl_Model_Api
{
  protected $_db;
  
  function __construct()
  {
    $this->_db = Zend_Registry::get('Config_Resources_Db');
    
    $pdoParams = array( 'MYSQL_ATTR_INIT_COMMAND' => 'SET NAMES utf8' );
    $params = array(
      'host' => $this->_db['params']['host'],
      'dbname' => $this->_db['params']['dbname'],
      'username' => $this->_db['params']['username'],
      'password' => $this->_db['params']['password'],
      'port' => (isset($this->_db['params']['port'])?$this->_db['params']['port']:''),
      'charset' => 'utf8',
      'driver_options' => $pdoParams);

    try
    {
      $this->_db = Zend_Db::factory( $this->_db['adapter'], $params );
      $this->_db->getConnection();
    }
    catch( Zend_Db_Adapter_Exception $e )
    {
        // problem z zaladowaniem odpowiedniego adaptera bazy
    }
    catch( Zend_Exception $e )
    {
      echo 'Jkl_Model_Api::__construct, wyjątek';
    }
  }
}
