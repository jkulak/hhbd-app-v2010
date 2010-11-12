<?php

require_once 'Dupa/Category/Container.php';  
require_once 'Dupa/Exception.php';
require_once 'Dupa/List.php';
require_once 'Zend/Db.php';  

/**
 * Api do zarzadzania kategoriami
 * uzycie: $Category = Dupa_Category_Api::getInstance();
 * 		   $Category->getCategory( $id );
 * 
 * @author Playah
 */
class Dupa_Category_Api
{
	const DB_ADAPTER    = 'Pdo_Mysql';
	const DB_HOST		= 'localhost';
	const DB_NAME		= 'omnicom7';
	const DB_USER		= 'omnicom7';
	const DB_PASS		= 'ping298pong';
	
	/**
	 * Domyslne wartosci paczkowania
	 */
    const DEFAULT_PACK        = 1;
    const DEFAULT_PACKSIZE    = 10;
		
	private $_db;
	
	static private $_instance;
	
	/**
	 * Konstruktor
	 *
	 * @return Dupa_Category_Api
	 */
	private function __construct()
	{
		$pdoParams = array( 'MYSQL_ATTR_INIT_COMMAND' => 'SET NAMES UTF8');
		$params = array( 'host'		=> self::DB_HOST,
						 'dbname'	=> self::DB_NAME,
						 'username'	=> self::DB_USER,
						 'password'	=> self::DB_PASS,
						 'driver_options' => $pdoParams);
		try
		{
			$this->_db = Zend_Db::factory( self::DB_ADAPTER, $params );
			$this->_db->getConnection();
			$this->_db->fetchAll('SET NAMES utf8');
		}
		catch( Zend_Db_Adapter_Exception $e )
		{
		    // problem z baza lub zalogowaniem do niej
		}
		catch( Zend_Exception $e )
		{
		    // problem z zaladowaniem odpowiedniego adaptera bazy
		}
	}
	
	/**
	 * Pobranie instancji klasy
	 * 
	 * @return Dupa_Category_Api
	 */
	static public function getInstance()
	{
		if( !self::$_instance )
			self::$_instance = new Dupa_Category_Api();
		return self::$_instance;
	}
	
	/**
	 * Pobranie kategorii o okreslonym id
	 * 
	 * @param $id Id kategorii
	 * 
	 * @return Dupa_Category_Container
	 */
	public function getCategory( $id )
	{
	    $res = null;
		$id = intval( $id );
		
		if( $id > 0 )
		{
    		$query = 'SELECT id, name, add_date, add_by, update_date, update_by FROM categories WHERE id = ?';
    		
    		try
    		{
    		    $result = $this->_db->fetchAll( $query, $id );
    		}		
    		catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error getting category', Dupa_Exception::ERROR_DB );
    		}
    		
    		if( $result )
    		{
    		    $category = new Dupa_Category_Container();
    		    
    		    $category->setId( $result[0]['id'] );
    		    $category->setName( $result[0]['name'] );
    		    $category->setAddDate( $result[0]['add_date'] );
    		    $category->setAddBy( $result[0]['add_by'] );
    		    $category->setUpdateDate( $result[0]['update_date'] );
    		    $category->setUpdateBy( $result[0]['update_by'] );
    		    $res = $category;
    		}
		}
		else
		    throw new Dupa_Exception( 'Error getting category', Dupa_Exception::ERROR_VALIDATE );

		return $res;
	}
	
	/**
	 * Dodanie kategorii
	 * 
	 * @param $category Kategoria
	 * 
	 * @return int Id dodanej kategorii
	 */
	public function addCategory( Dupa_Category_Container $category )
	{
	    $res = null;

		if( $category )
		{
		    $data = array(	'name'	    => $category->getName(),
		                	'add_date'  => date( 'Y-m-d H:i:s' ),
		                	'add_by'	=> $category->getAddBy(),
		                	'update_date' => date( 'Y-m-d H:i:s' ),
							'update_by' => $category->getUpdateBy() );

		    try
		    {
    		    $result = $this->_db->insert( 'categories', $data );
		    }
			catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error adding category', Dupa_Exception::ERROR_DB );
    		}

    		if( $result == 1 )
                $id = $this->_db->lastInsertId();
            else
                throw new Dupa_Exception( 'Error adding category', Dupa_Exception::ERROR_DB );
		}
		else
		    throw new Dupa_Exception( 'Error adding category', Dupa_Exception::ERROR_VALIDATE );

		return $id;
	}	
	
	/**
	 * Modyfikacja kategorii
	 * 
	 * @param $category Kategoria
	 * 
	 * @return bool Czy modyfikacja sie powiodla
	 */
	public function setCategory( Dupa_Category_Container $category )
	{
	    $res = null;

		if( $category )
		{
		    $data = array(	'name'	    => $category->getName(),
							'update_date' => date( 'Y-m-d H:i:s' ),
							'update_by' => $category->getUpdateBy() );

		    try
		    {
    		    $result = $this->_db->update( 'categories', $data, 'id = ' . $category->getId() );
		    }
			catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error setting category', Dupa_Exception::ERROR_DB );
    		}
		}
		else
		    throw new Dupa_Exception( 'Error setting category', Dupa_Exception::ERROR_VALIDATE );

		return $result;
	}
	
	/**
	 * Usuniecie kategorii
	 * 
	 * @param $id Id kategorii
	 * 
	 * @return bool Czy usuniecie sie powiodlo
	 */
	public function delCategory( $id )
	{
	    $res = null;
        $id = intval( $id );
        
		if( $id > 0 )
		{
		    try
		    {
    		    $result = $this->_db->delete( 'categories', 'id = ' . $id );
		    }
			catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error deleting category', Dupa_Exception::ERROR_DB );
    		}
		}
		else
		    throw new Dupa_Exception( 'Error deleting category', Dupa_Exception::ERROR_VALIDATE );

		return $result;
	}	
	
	/**
	 * Pobranie listy artykulow
	 * 
	 * @param int $pack Numer paczki
	 * @param int $packSize Wielkosc paczki
	 * 
	 * @return Dupa_List
	 */
	public function getCategoriesList( $pack = null, $packSize = null )
	{
	    $pack = intval( $pack ) ? intval( $pack ) : self::DEFAULT_PACK;
	    $packSize = intval( $packSize ) ? intval( $packSize ) : self::DEFAULT_PACKSIZE;
	    $list = null;
	    
		$start = ( $pack - 1 ) * $packSize;
		$end = $packSize;
	    
		$query = 'SELECT id, name, add_date, add_by, update_date, update_by ' .
		         'FROM categories limit ' . $start .', ' . $end;
		
		try
		{
		    $result = $this->_db->fetchAll( $query );
		}		
		catch( Zend_Db_Exception $e )
		{
		    throw new Dupa_Exception( 'Error getting categories list', Dupa_Exception::ERROR_DB );
		}

		if( $result )
		{
		    $list = new Dupa_List();
		    for( $i = 0, $cnt = count( $result ); $i < $cnt; $i++ )
		    {
    		    $category = new Dupa_Category_Container();
    		    
    		    $category->setId( $result[$i]['id'] );
    		    $category->setName( $result[$i]['name'] );
    		    $category->setAddDate( $result[$i]['add_date'] );
    		    $category->setAddBy( $result[$i]['add_by'] );
    		    $category->setUpdateDate( $result[$i]['update_date'] );
    		    $category->setUpdateBy( $result[$i]['update_by'] );
    		    
    		    $list[$i] = $category;
		    }
		}

		return $list;
	}
	
	/**
	 * Pobranie listy artykulow
	 * 
	 * @param int $pack Numer paczki
	 * @param int $packSize Wielkosc paczki
	 * 
	 * @return Dupa_List
	 */
	public function getCategoriesCount()
	{
	    $res = null;

		$query = 'SELECT count(*) as cnt ' .
		         'FROM categories';
		
		try
		{
		    $res = $this->_db->fetchAll( $query );
		}		
		catch( Zend_Db_Exception $e )
		{
		    throw new Dupa_Exception( 'Error getting categories list', Dupa_Exception::ERROR_DB );
		}

		return $res;
	}
}
