<?php

require_once 'Dupa/Article/Container.php';
require_once 'Dupa/Category/Container.php';   
require_once 'Dupa/Exception.php';
require_once 'Dupa/List.php';    
require_once 'Zend/Db.php';   

/**
 * Api do artykulow
 * uzycie: $Article = Dupa_Article_Api::getInstance();
 * 		   $Article->getArticle( $id );
 * 
 * @author Playah
 */
class Dupa_Article_Api
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
    const DEFAULT_PACKSIZE    = 100;
	
	/**
	 * Domyslne wartosci kierunku sortowania
	 */
    const SORT_ORDER_ASC      = 'asc';
    const SORT_ORDER_DESC     = 'desc';
    const SORT_ORDER_DEFAULT  = self::SORT_ORDER_ASC;
    
    /**
     * Mozliwosc sortowania po roznych datach
     */
    const SORT_TYPE_ID          = 'id';
    const SORT_TYPE_ACTIVATE    = 'activate_date';
    const SORT_TYPE_DEACTIVATE  = 'deactivate_date';
    const SORT_TYPE_ADDED       = 'add_date';
    const SORT_TYPE_DEFAULT     = self::SORT_TYPE_ACTIVATE;

    /**
     * Uchwyt do bazy
     * 
     * @var unknown_type
     */
	private $_db;
	
	/**
	 * Singleton
	 * 
	 * @var Dupa_Article_Api
	 */
	static private $_instance;
	
	/**
	 * Nazwy miesiecy w dopelniaczu
	 * 
	 * @var array
	 */
	protected $_monthsNames = array( 'stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia' );
	
	/**
	 * Konstruktor
	 * 
	 * @return void
	 */
	private function __construct()
	{
		$pdoParams = array( 'MYSQL_ATTR_INIT_COMMAND' => 'SET NAMES utf8' );
		
		$params = array( 'host'		=> self::DB_HOST,
						 'dbname'	=> self::DB_NAME,
						 'username'	=> self::DB_USER,
						 'password'	=> self::DB_PASS,
						 'charset'	=> 'utf8',
    					 'driver_options' => $pdoParams);
		try
		{
			$this->_db = Zend_Db::factory( self::DB_ADAPTER, $params );
			$this->_db->getConnection();
			/**
				@TODO: tymczasow wywolanie set names - bo nie dziala ustawienie kodowania przez PDO 
			*/
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
	 * @return Dupa_Article
	 */
	static public function getInstance()
	{
		if( !self::$_instance )
			self::$_instance = new Dupa_Article_Api();
		return self::$_instance;
	}
	
	/**
	 * Pobranie artykulu o okreslonym id
	 * 
	 * @param $id Id artykulu
	 * 
	 * @return Dupa_Article_Container
	 */
	public function getArticle( $id )
	{
	    $res = null;
		$id = intval( $id );
		
		if( $id > 0 )
		{
    		$query = 'SELECT id, title, lead, content, add_date, add_by, update_date, update_by, activate_date, deactivate_date, status FROM articles WHERE id = ?';
    		
    		try
    		{
    		    $result = $this->_db->fetchAll( $query, $id );
    		}		
    		catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error getting article: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
    		
    		if( $result )
    		{
    		    $article = new Dupa_Article_Container();
    	    
    		    $actTime = strtotime( $result[0]['activate'] );
    		    $fullDate = $result[0]['activate'];
    	        $time = date( 'H:i:s', $actTime );
    	        $date = date( 'j', $actTime ) . ' ' . $this->_monthsNames[ date( 'n', $actTime ) - 1 ] . ' ' . date( 'Y', $actTime );

    		    $article->setId( $result[0]['id'] );
    		    $article->setTitle( $result[0]['title'] );
    		    $article->setLead( $result[0]['lead'] );
    		    $article->setContent( $result[0]['content'] );
    		    $article->setAddDate( $result[0]['added'] );
    		    $article->setAddBy( $result[0]['addedby'] );
    		    $article->setUpdateDate( $result[0]['updated'] );
    		    $article->setUpdateBy( $result[0]['updatedby'] );
    		    $article->setEnableDate( $date );
    		    $article->setEnableTime( $time );
    		    $article->setEnableDateFull( $fullDate );
    		    $article->setDisableDate( $result[0]['deactivate'] );
    		    $article->setStatus( $result[0]['status'] );

    		    $article->setCategories( $this->getArticleCategoriesList( $article->getId() ) );

    		    $res = $article;
    		}
		}
		else
		    throw new Dupa_Exception( 'Error getting article', Dupa_Exception::ERROR_VALIDATE );

		return $res;
	}
	
	/**
	 * Dodanie artykulu
	 * 
	 * @param $article Artykul
	 * 
	 * @return int Id dodanego artykulu
	 */
	public function addArticle( Dupa_Article_Container $article )
	{
	    $res = null;

		if( $article )
		{
		    $data = array(
		                'title'	        => $article->getTitle(),
		                'lead'	        => $article->getLead(),
		                'content'	    => $article->getContent(),
		                'add_date'	    => date( 'Y-m-d H:i:s' ),
		                'add_by'	    => $article->getAddBy(),
						'update_date'	=> date( 'Y-m-d H:i:s' ),
		                'update_by'     => $article->getUpdateBy(),
		                'activate_date'	=> $article->getEnableDate(),
		                'deactivate_date' => $article->getDisableDate(),
		                'status'	    => $article->getStatus() );

		    try
		    {
    		    $result = $this->_db->insert( 'articles', $data );
		    }
			catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error adding article: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}

    		if( $result == 1 )
    		{
    		    $res = $this->_db->lastInsertId();

    		    if( $categories = $article->getCategories() )
    		        $this->addArticleCategories( $res, $categories );
    		}                
            else
                throw new Dupa_Exception( 'Error adding article', Dupa_Exception::ERROR_DB );
		}
		else
		    throw new Dupa_Exception( 'Error adding article', Dupa_Exception::ERROR_VALIDATE );

		return $res;
	}	

	/**
	 * Modyfikacja artykulu
	 * 
	 * @param $article Artykul
	 * 
	 * @return bool Czy modyfikacja sie powiodla
	 */
	public function setArticle( Dupa_Article_Container $article )
	{
	    $res = null;

		if( $article )
		{
		    $data = array(
		                'title'	        => $article->getTitle(),
		                'lead'	        => $article->getLead(),
		                'content'	    => $article->getContent(),
		                'update_date'	=> date( 'Y-m-d H:i:s' ),
		                'update_by'     => $article->getUpdateBy(),
		                'activate_date'	=> $article->getEnableDate() ? $article->getEnableDate() : null,
		                'deactivate_date' => $article->getDisableDate() ? $article->getDisableDate() : null,
		                'status'	    => $article->getStatus() );

		    try
		    {
    		    $res = $this->_db->update( 'articles', $data, 'id = ' . $article->getId() );
		    }
			catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error setting article: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}

    		if( $res == 1 )
    		{
    		    $this->delArticleCategories( $article->getId() );
    		    if( $categories = $article->getCategories() )
    		        $this->addArticleCategories( $article->getId(), $categories );
    		}                
            else
                throw new Dupa_Exception( 'Error adding article', Dupa_Exception::ERROR_DB );
		}
		else
		    throw new Dupa_Exception( 'Error setting article', Dupa_Exception::ERROR_VALIDATE );

		return $res;
	}
	
	/**
	 * Usuniecie artykulu
	 * 
	 * @param $id Id artykulu
	 * 
	 * @return bool Czy usuniecie sie powiodlo
	 */
	public function delArticle( $id )
	{
	    $res = null;
        $id = intval( $id );
        
		if( $id > 0 )
		{
		    $data = array( 'status'	=> Dupa_Article_Container::STATUS_DELETED );
		    try
		    {
    		    $res = $this->_db->update( 'articles', $data, 'id = ' . $id );
		    }
			catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error deleting article: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
		}
		else
		    throw new Dupa_Exception( 'Error deleting article', Dupa_Exception::ERROR_VALIDATE );

		return $res;
	}	
	
	/**
	 * Pobranie listy artykulow
	 * 
	 * @param int $categoryId Id kategorii; jezeli nie podano - pobierz ze wszystkich
	 * @param int $pack Numer paczki
	 * @param int $packSize Wielkosc paczki
	 * @param string $order Sortowanie
	 * @param string $year Rok, z ktorego pobierac artykuly (nalezy podawac tez $month)
	 * @param string $month Miesiac, z ktorego pobierac artykul (nalezy podawac tez $year)
	 * 
	 * @return Dupa_List
	 */
	public function getArticlesList( $categoryId = null, $pack = null, $packSize = null, $order = null, $year = null, $month = null, $sort = null )
	{
	    $pack = intval( $pack ) ? intval( $pack ) : self::DEFAULT_PACK;
	    $packSize = intval( $packSize ) ? intval( $packSize ) : self::DEFAULT_PACKSIZE;
	    $categoryId = $categoryId ? intval( $categoryId ) : $categoryId;
	    $order = self::checkSortOrder( $order );
	    $sort = self::checkSort( $sort );
	    
	    if( $year && $month )
	    {
    	    $year = intval( $year );
    	    $month = intval( $month );
    	    $month = $month < 10 ? '0' . strval( $month ) : strval( $month );
    	    $date = $year . '-' . $month;
	    }
	    
		$list = new Dupa_List();
	    
	    if( $categoryId > 0 || $categoryId === null )
	    {    	    
    		$start = ( $pack - 1 ) * $packSize;
    		$end = $packSize;
    		
    		if( !$categoryId )
    		{
        		$query = 'SELECT id, title, lead, add_date, add_by, update_date, update_by, activate_date, deactivate_date, status ' .
        		         'FROM articles ' .
        		         'WHERE status = "' . Dupa_Article_Container::STATUS_ENABLED . ( isset( $date ) ? '" and substring( added, 1, 7 ) = "' . $date . '" ': '' ) .
        		         'and activate is not null ' .
        		         'ORDER by ' . $sort . ' ' . $order . ' limit ' . $start . ', ' . $end;
        		$queryCnt = 'SELECT count(*) as cnt ' .
            		        'FROM articles ' .
            		        'WHERE status = "' . Dupa_Article_Container::STATUS_ENABLED . ( isset( $date ) ? '" and substring( added, 1, 7 ) = "' . $date . '" ': '' ) .
        		         	'and activate is not null';
    		}
            else
            {
        		$query = 'SELECT id, title, lead, add_date, add_by, update_date, update_by, activate_date, deactivate_date, status ' .
        		         'FROM articles a ' .
        		         'INNER JOIN categories_has_articles ac ON a.id = ac.articles_id ' .
        		         'WHERE status = "' . Dupa_Article_Container::STATUS_ENABLED . '" and ac.categories_id = ' . $categoryId . ' ' .
        		         'and activate is not null ' .
        		         ( isset( $date ) ? 'AND substring( added, 1, 7 ) = "' . $date . '" ': '' ) .
        		         'ORDER by ' . $sort . ' ' . $order . ' limit ' . $start . ', ' . $end;
        		$queryCnt = 'SELECT count(*) as cnt ' .
            		        'FROM articles a ' .
            		        'INNER JOIN categories_has_articles ac ON a.id = ac.articles_id ' .
            		        'WHERE status = "' . Dupa_Article_Container::STATUS_ENABLED . '" and ac.categories_id = ' . $categoryId . ' ' .
        		            'and activate is not null ' .
            		        ( isset( $date ) ? 'AND substring( added, 1, 7 ) = "' . $date . '" ': '' );
            }
    		
    		try
    		{
    		    $result = $this->_db->fetchAll( $query );
    		    $resultCnt = $this->_db->fetchAll( $queryCnt );
    		}		
    		catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error getting articles list: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
    
    		if( $result )
    		{
    		    for( $i = 0, $cnt = count( $result ); $i < $cnt; $i++ )
    		    {
        		    $article = new Dupa_Article_Container();
    	    
        		    $actTime = $result[$i]['activate_date'] ? strtotime( $result[$i]['activate_date'] ) : null;
        		    $fullDate = $result[$i]['activate_date'];
        	        $time = $actTime ? date( 'H:i:s', $actTime ) : null;
        	        $date = $actTime ? date( 'j', $actTime ) . ' ' . $this->_monthsNames[ date( 'n', $actTime ) - 1 ] . ' ' . date( 'Y', $actTime ) : null;
            		    
        		    $article->setId( $result[$i]['id'] );
        		    $article->setTitle( $result[$i]['title'] );
        		    $article->setLead( $result[$i]['lead'] );
        		    $article->setAddDate( $result[$i]['add_date'] );
        		    $article->setAddBy( $result[$i]['add_by'] );
        		    $article->setUpdateDate( $result[$i]['update_date'] );
        		    $article->setUpdateBy( $result[$i]['update_by'] );
        		    $article->setEnableDate( $date );
        		    $article->setEnableTime( $time );
        		    $article->setEnableDateFull( $fullDate );
        		    $article->setDisableDate( $result[$i]['deactivate_date'] );
        		    $article->setStatus( $result[$i]['status'] );
        		    
        		    $article->setCategories( $this->getArticleCategoriesList( $article->getId() ) );
        		    
        		    $list[$i] = $article;
    		    }
    		    $list->cntItems = $resultCnt[0]['cnt'];    		    
    		    $list->prepareNavigation( $pack, $packSize );
    		}
	    }
		else
		    throw new Dupa_Exception( 'Error getting articles list', Dupa_Exception::ERROR_VALIDATE );
		    
		return $list;
	}
	
	/**
	 * Pobranie listy artykulow do formatki
	 * 
	 * @param int $categoryId Id kategorii; jezeli nie podano - pobierz ze wszystkich
	 * @param int $pack Numer paczki
	 * @param int $packSize Wielkosc paczki
	 * @param string $order Sortowanie
	 * @param string $year Rok, z ktorego pobierac artykuly (nalezy podawac tez $month)
	 * @param string $month Miesiac, z ktorego pobierac artykul (nalezy podawac tez $year)
	 * 
	 * @return Dupa_List
	 */
	public function getArticlesListForm( $categoryId = null, $pack = null, $packSize = null, $order = null, $year = null, $month = null )
	{
	    $pack = intval( $pack ) ? intval( $pack ) : self::DEFAULT_PACK;
	    $packSize = intval( $packSize ) ? intval( $packSize ) : self::DEFAULT_PACKSIZE;
	    $categoryId = $categoryId ? intval( $categoryId ) : $categoryId;
	    $order = self::checkSortOrder( $order );
	    
	    if( $year && $month )
	    {
    	    $year = intval( $year );
    	    $month = intval( $month );
    	    $month = $month < 10 ? '0' . strval( $month ) : strval( $month );
    	    $date = $year . '-' . $month;
	    }
	    
		$list = new Dupa_List();
	    
	    if( $categoryId > 0 || $categoryId === null )
	    {    	    
    		$start = ( $pack - 1 ) * $packSize;
    		$end = $packSize;
    		
    		if( !$categoryId )
    		{
        		$query = 'SELECT id, title, lead, add_date, add_by, update_date, update_by, activate_date, deactivate_date, status ' .
        		         'FROM articles ' .
        		         ( isset( $date ) ? 'WHERE substring( added, 1, 7 ) = "' . $date . '" ': '' ) .
        		         'ORDER by id ' . $order . ' limit ' . $start . ', ' . $end;
        		$queryCnt = 'SELECT count(*) as cnt ' .
            		        'FROM articles ' .
            		        ( isset( $date ) ? 'WHERE substring( added, 1, 7 ) = "' . $date . '" ': '' );
    		}
            else
            {
        		$query = 'SELECT id, title, lead, add_date, add_by, update_date, update_by, activate_date, deactivate_date, status ' .
        		         'FROM articles a ' .
        		         'INNER JOIN categories_has_articles ac ON a.id = ac.articles_id ' .
        		         'WHERE ac.categories_id = ' . $categoryId . ' ' .
        		         ( isset( $date ) ? 'AND substring( added, 1, 7 ) = "' . $date . '" ': '' ) .
        		         'ORDER by id ' . $order . ' limit ' . $start . ', ' . $end;
        		$queryCnt = 'SELECT count(*) as cnt ' .
            		        'FROM articles a ' .
            		        'INNER JOIN categories_has_articles ac ON a.id = ac.articles_id ' .
            		        'WHERE ac.categories_id = ' . $categoryId . ' ' .
            		        ( isset( $date ) ? 'AND substring( added, 1, 7 ) = "' . $date . '" ': '' );
            }
    		
    		try
    		{
    		    $result = $this->_db->fetchAll( $query );
    		    $resultCnt = $this->_db->fetchAll( $queryCnt );
    		}		
    		catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error getting articles list: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
    
    		if( $result )
    		{
    		    for( $i = 0, $cnt = count( $result ); $i < $cnt; $i++ )
    		    {
        		    $article = new Dupa_Article_Container();
    	    
        		    $actTime = strtotime( $result[$i]['activate_date'] );
        		    $fullDate = $result[$i]['activate_date'];
        	        $time = date( 'H:i:s', $actTime );
        	        $date = date( 'j', $actTime ) . ' ' . $this->_monthsNames[ date( 'n', $actTime ) - 1 ] . ' ' . date( 'Y', $actTime );
            		    
        		    $article->setId( $result[$i]['id'] );
        		    $article->setTitle( $result[$i]['title'] );
        		    $article->setLead( $result[$i]['lead'] );
        		    $article->setAddDate( $result[$i]['add_date'] );
        		    $article->setAddBy( $result[$i]['add_by'] );
        		    $article->setUpdateDate( $result[$i]['update_date'] );
        		    $article->setUpdateBy( $result[$i]['update_by'] );
        		    $article->setEnableDate( $date );
        		    $article->setEnableTime( $time );
        		    $article->setEnableDateFull( $fullDate );
        		    $article->setDisableDate( $result[$i]['deactivate_date'] );
        		    $article->setStatus( $result[$i]['status'] );
        		    
        		    $article->setCategories( $this->getArticleCategoriesList( $article->getId() ) );
        		    
        		    $list[$i] = $article;
    		    }
    		    $list->cntItems = $resultCnt[0]['cnt'];    		    
    		    $list->prepareNavigation( $pack, $packSize );
    		}
	    }
		else
		    throw new Dupa_Exception( 'Error getting articles list', Dupa_Exception::ERROR_VALIDATE );
		    
		return $list;
	}
	
	/**
	 * Pobranie ilosci artykulow
	 * 
	 * @param int $categoryId Id kategorii; jezeli nie podano - pobierz ze wszystkich
	 * @param string $year Rok, z ktorego pobierac artykuly (nalezy podawac tez $month)
	 * @param string $month Miesiac, z ktorego pobierac artykul (nalezy podawac tez $year)
	 * 
	 * @return Dupa_List
	 */
	public function getArticlesCount( $categoryId = null, $year = null, $month = null )
	{
	    $categoryId = $categoryId ? intval( $categoryId ) : $categoryId;

	    if( $year && $month )
	    {
    	    $year = intval( $year );
    	    $month = intval( $month );
    	    $month = $month < 10 ? '0' . strval( $month ) : strval( $month );
    	    $date = $year . '-' . $month;
	    }
	    
		$list = new Dupa_List();
	    
	    if( $categoryId > 0 || $categoryId === null )
	    {
    		if( !$categoryId )
    		{
        		$query = 'SELECT count(*) as cnt ' .
            		        'FROM articles ' .
            		        ( isset( $date ) ? 'WHERE status = "' . Dupa_Article_Container::STATUS_ENABLED . '" and substring( added, 1, 7 ) = "' . $date . '" ': '' );
    		}
            else
            {
        		$query = 'SELECT count(*) as cnt ' .
            		        'FROM articles a ' .
            		        'INNER JOIN categories_has_articles ac ON a.id = ac.articles_id ' .
            		        'WHERE status = "' . Dupa_Article_Container::STATUS_ENABLED . '" and ac.categories_id = ' . $categoryId . ' ' .
            		        ( isset( $date ) ? 'AND substring( added, 1, 7 ) = "' . $date . '" ': '' );
            }
    		
    		try
    		{
    		    $result = $this->_db->fetchAll( $query );
    		}		
    		catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error getting articles list: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
	    }
		else
		    throw new Dupa_Exception( 'Error getting articles list', Dupa_Exception::ERROR_VALIDATE );
		    
		return $result;
	}
	
	/**
	 * Pobranie ilosci artykulow dla formatki
	 * 
	 * @param int $categoryId Id kategorii; jezeli nie podano - pobierz ze wszystkich
	 * @param string $year Rok, z ktorego pobierac artykuly (nalezy podawac tez $month)
	 * @param string $month Miesiac, z ktorego pobierac artykul (nalezy podawac tez $year)
	 * 
	 * @return Dupa_List
	 */
	public function getArticlesCountForm( $categoryId = null, $year = null, $month = null )
	{
	    $categoryId = $categoryId ? intval( $categoryId ) : $categoryId;

	    if( $year && $month )
	    {
    	    $year = intval( $year );
    	    $month = intval( $month );
    	    $month = $month < 10 ? '0' . strval( $month ) : strval( $month );
    	    $date = $year . '-' . $month;
	    }
	    
		$list = new Dupa_List();
	    
	    if( $categoryId > 0 || $categoryId === null )
	    {
    		if( !$categoryId )
    		{
        		$query = 'SELECT count(*) as cnt ' .
            		        'FROM articles ' .
            		        ( isset( $date ) ? 'WHERE substring( added, 1, 7 ) = "' . $date . '" ': '' );
    		}
            else
            {
        		$query = 'SELECT count(*) as cnt ' .
            		        'FROM articles a ' .
            		        'INNER JOIN categories_has_articles ac ON a.id = ac.articles_id ' .
            		        'WHERE ac.categories_id = ' . $categoryId . ' ' .
            		        ( isset( $date ) ? 'AND substring( added, 1, 7 ) = "' . $date . '" ': '' );
            }
    		
    		try
    		{
    		    $result = $this->_db->fetchAll( $query );
    		}		
    		catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error getting articles list: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
	    }
		else
		    throw new Dupa_Exception( 'Error getting articles list', Dupa_Exception::ERROR_VALIDATE );
		    
		return $result;
	}
	
	/**
	 * Pobranie listy dat, z kiedy pochodza artykuly
	 * 
	 * @param int $categoryId Id kategorii; jezeli nie podano - pobierz ze wszystkich
	 * @param int $pack Numer paczki
	 * @param int $packSize Wielkosc paczki
	 * 
	 * @return Dupa_List
	 */
	public function getArticlesDates( $categoryId = null, $pack = null, $packSize = null )
	{
	    $pack = intval( $pack ) ? intval( $pack ) : self::DEFAULT_PACK;
	    $packSize = intval( $packSize ) ? intval( $packSize ) : self::DEFAULT_PACKSIZE;
	    $categoryId = $categoryId ? intval( $categoryId ) : $categoryId;
	
		$list = new Dupa_List();
	    
	    if( $categoryId > 0 || $categoryId === null )
	    {    	    
    		$start = ( $pack - 1 ) * $packSize;
    		$end = $packSize;
    		
	        if( !$categoryId )
    		{
        		$query = 'SELECT substring( add_date, 1, 4 ) as year, substring( add_date, 6, 2 ) as month ' .
        		         'FROM articles ' . 
        		         'GROUP BY substring( add_date, 1, 7 ) ' .
        		         'ORDER BY year, month desc limit ' . $start . ', ' . $end;
    		}
            else
            {
        		$query = 'SELECT substring( add_date, 1, 4 ) as year, substring( add_date, 6, 2 ) as month ' .
        		         'FROM articles a ' .
        		         'INNER JOIN categories_has_articles ac ON a.id = ac.articles_id ' .
        		         'WHERE ac.categories_id = ' . $categoryId . ' ' .
        		         'GROUP BY substring( add_date, 1, 7 ) ' .
        		         'ORDER BY year, month desc limit ' . $start . ', ' . $end;
            }
    		try
    		{
    		    $result = $this->_db->fetchAll( $query );
    		}		
    		catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error getting articles dates: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
    		
    		for( $i = 0, $cnt = count( $result ); $i < $cnt; $i++ )
    		{
    		    if( $result[$i]['year'] && $result[$i]['month'] )
    		    {
    		        $list[$i]['year'] = $result[$i]['year'];
    		        $list[$i]['month'] = $result[$i]['month'];
    		    }
    		}
            $list->cntItems = $list->length();
	    }

	    return $list;
	}

	/**
	 * Dodanie kategorii do artykulu
	 * 
	 * @param $articleId Id artykulu
	 * @param $catId Id kategorii
	 * 
	 * @return bool Czy operacja sie powiodla
	 */
	public function addArticleCategory( $articleId, $catId )
	{
	    $res = null;
	    $articleId = intval( $articleId );
	    $catId = intval( $catId );

		if( $articleId > 0 && $catId > 0 )
		{
		    $data = array(
		                'cat_id'	 => $catId,
		                'art_id'	 => $articleId );

		    try
		    {
    		    $result = $this->_db->insert( 'articles_categories', $data );
		    }
			catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error adding category to article: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
		}
		else
		    throw new Dupa_Exception( 'Error adding category to article', Dupa_Exception::ERROR_VALIDATE );

		return $result;
	}

	/**
	 * Dodanie listy kategorii do artykulu
	 * 
	 * @param $articleId Id artykulu
	 * @param $catId Id kategorii
	 * 
	 * @return bool Czy operacja sie powiodla
	 */
	public function addArticleCategories( $articleId, Dupa_List $cats )
	{
	    $res = null;
	    $articleId = intval( $articleId );

	    $cnt = count( $cats );

		if( $articleId > 0 && $cnt > 0 )
		{
            for( $i = 0; $i < $cnt; $i++ )
                $res = $this->addArticleCategory( $articleId, $cats[$i]->getId() );
		}
		else
		    throw new Dupa_Exception( 'Error adding categories to article', Dupa_Exception::ERROR_VALIDATE );

		return $res;
	}
	
	/**
	 * Usuniecie wszystkich kategorii artykulu
	 * 
	 * @param $articleId Id artykulu
	 * 
	 * @return bool Czy usuniecie sie powiodlo
	 */
	public function delArticleCategories( $articleId )
	{
	    $res = null;
        $articleId = intval( $articleId );
        
		if( $articleId > 0 )
		{
		    try
		    {
    		    $res = $this->_db->delete( 'articles_categories', 'art_id = ' . $articleId );
		    }
			catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error deleting article categories: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
		}
		else
		    throw new Dupa_Exception( 'Error deleting article categories', Dupa_Exception::ERROR_VALIDATE );

		return $res;
	}	
	
	/**
	 * Pobranie listy kategorii artykulu
	 * 
	 * @param int $articleId Id artykulu
	 * 
	 * @return Dupa_List
	 */
	public function getArticleCategoriesList( $articleId )
	{
	    $articleId = intval( $articleId );

		$list = new Dupa_List();
	    
	    if( $articleId > 0 )
	    {
    		$query = 'SELECT id, name, add_date, add_by, update_date, update_by ' .
    		         'FROM categories c ' .
    		         'INNER JOIN articles_categories ca ON ca.cat_id = c.id ' .
    		         'WHERE ca.art_id = ' . $articleId;
    		$queryCnt = 'SELECT count(*) as cnt ' .
    		         'FROM categories c ' .
    		         'INNER JOIN articles_categories ca ON ca.cat_id = c.id ' .
    		         'WHERE ca.art_id = ' . $articleId;
		
    		try
    		{
    		    $result = $this->_db->fetchAll( $query );
    		    $resultCnt = $this->_db->fetchAll( $queryCnt );

    		}		
    		catch( Zend_Db_Exception $e )
    		{
    		    throw new Dupa_Exception( 'Error getting article categories list: ' . $e->getMessage(), Dupa_Exception::ERROR_DB );
    		}
    
    		if( $result )
    		{
    		    for( $i = 0, $cnt = count( $result ); $i < $cnt; $i++ )
    		    {
        		    $cat = new Dupa_Category_Container();
        		    
        		    $cat->setId( $result[$i]['id'] );
        		    $cat->setName( $result[$i]['name'] );
        		    $cat->setAddDate( $result[$i]['add_date'] );
        		    $cat->setAddBy( $result[$i]['add_by'] );
        		    $cat->setUpdateDate( $result[$i]['update_date'] );
        		    $cat->setUpdateBy( $result[$i]['update_by'] );
        		    
        		    $list[$i] = $cat;
    		    }
    		    $list->cntItems = $resultCnt[0]['cnt'];    		    
    		}
	    }
		else
		    throw new Dupa_Exception( 'Error getting article categories list', Dupa_Exception::ERROR_VALIDATE );
		    
		return $list;
	}
	
    static public function checkSortOrder( $sortOrder )
    {
        return in_array( $sortOrder, array( self::SORT_ORDER_ASC,
                                            self::SORT_ORDER_DESC ) ) ? $sortOrder : self::SORT_ORDER_DEFAULT;
    }
	
    static public function checkSort( $sort )
    {
        return in_array( $sort, array( self::SORT_TYPE_ACTIVATE,
                                       self::SORT_TYPE_ADDED,
                                       self::SORT_TYPE_DEACTIVATE,
                                       self::SORT_TYPE_ID ) ) ? $sort : self::SORT_TYPE_DEFAULT;
    }
}
