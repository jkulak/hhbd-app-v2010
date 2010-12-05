<?php
/**
* 
*/
abstract class Jkl_Model_Api
{
  protected $_db;
  
  function __construct()
  {
    $dbRes = Zend_Registry::get('Config_Resources');
    
    $pdoParams = array( 'MYSQL_ATTR_INIT_COMMAND' => 'SET NAMES utf8' );
    $params = array(
      'host'      => $dbRes['db']['params']['host'],
      'dbname'    => $dbRes['db']['params']['dbname'],
      'username'  => $dbRes['db']['params']['username'],
      'password'  => $dbRes['db']['params']['password'],
      'port'      => (isset($dbRes['db']['params']['port'])?$dbRes['db']['params']['port']:''),
      'charset'   => 'utf8',
      'driver_options' => $pdoParams);
    try
    {
      $this->_db = Zend_Db::factory($dbRes['db']['adapter'], $params );
      $this->_db->getConnection();
    }
    catch( Zend_Db_Adapter_Exception $e )
    {
        // problem z zaladowaniem odpowiedniego adaptera bazy
    }
    catch( Zend_Exception $e )
    {
      echo 'Jkl_Model_Api::__construct, Exception';
    }
  }
}