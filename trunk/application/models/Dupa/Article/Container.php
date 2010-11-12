<?php

/**
 * Kontener artykulu
 * 
 * @author Playah
 */
class Dupa_Article_Container
{	
	/**
	 * Statusy artykulu
	 */
	const STATUS_ENABLED	= 'enabled';
	const STATUS_DISABLED	= 'disabled';
	const STATUS_DELETED	= 'deleted';
	const STATUS_DEV	    = 'dev';

	/**
	 * Id artykulu
	 * 
	 * @var int
	 */
	protected $_id;
	
	/**
	 * Tytul artykulu
	 * 
	 * @var string
	 */
	protected $_title;

	/**
	 * Wstep (lead) artykulu
	 * 
	 * @var string
	 */
	protected $_lead;
	
	/**
	 * Tresc artykulu
	 * 
	 * @var string
	 */
	protected $_content;
	
	/**
	 * Data dodania
	 * 
	 * @var string
	 */
	protected $_addDate;
	
	/**
	 * Uzytkownik, ktory dodal artykul
	 * 
	 * @var Dupa_User_Container
	 */
	protected $_addBy;
	
	/**
	 * Data ostatniej aktualizacji
	 * 
	 * @var string
	 */
	protected $_updateDate;
	
	/**
	 * Uzytkownik, ktory ostatnio aktualizowal artykul
	 * 
	 * @var Dupa_User_Container
	 */
	protected $_updateBy;
	
	/**
	 * Pelna Data (dzien, miesiac, rok, godzina, minuta) wlaczenia artykulu
	 * 
	 * @var string
	 */
	protected $_enableDateFull;
	
	/**
	 * Data (dzien, miesiac, rok) wlaczenia artykulu
	 * 
	 * @var string
	 */
	protected $_enableDate;
	
	/**
	 * Czas (godzina, minuta) wlaczenia artykulu
	 * 
	 * @var string
	 */
	protected $_enableTime;
	
	/**
	 * Data wylaczenia artykulu
	 * 
	 * @var string
	 */
	protected $_disableDate;
	
	/**
	 * Status
	 * 
	 * @var (enabled/disabled/test)
	 */
	protected $_status; 
	
	/**
	 * Kategorie
	 * 
	 * @var Dupa_List of Dupa_Article_Container
	 */
	protected $_categories; 
	
	/**
	 * Konstruktor
	 * 
	 * @return Dupa_Article_Container
	 */
	function __construct() { }
	
	public function setStatus( $status )
    {
        $this->_status = Dupa_Article_Container::checkStatus( $status );
    }

	public function setDisableDate( $disableDate )
    {
        $this->_disableDate = $disableDate;
    }

	public function setEnableDate( $enableDate )
    {
        $this->_enableDate = $enableDate;
    }

	public function setEnableDateFull( $enableDate )
    {
        $this->_enableDateFull = $enableDate;
    }
    
	public function setEnableTime( $enableTime )
    {
        $this->_enableTime = $enableTime;
    }
    
	public function setUpdateBy( $updateBy )
    {
        $this->_updateBy = $updateBy;
    }

	public function setUpdateDate( $updateDate )
    {
        $this->_updateDate = $updateDate;
    }

	public function setAddBy( $addBy )
    {
        $this->_addBy = $addBy;
    }

	public function setAddDate( $addDate )
    {
        $this->_addDate = $addDate;
    }

	public function setContent( $content )
    {
        $this->_content = $content;
    }

	public function setLead( $lead )
    {
        $this->_lead = $lead;
    }

	public function setTitle( $title )
    {
        $this->_title = $title;
    }

	public function setId( $id )
    {
        $this->_id = $id;
    }

	public function getStatus()
    {
        return $this->_status;
    }

	public function getDisableDate()
    {
        return $this->_disableDate;
    }

	public function getEnableDateFull()
    {
        return $this->_enableDateFull;
    }
    
	public function getEnableDate()
    {
        return $this->_enableDate;
    }

	public function getEnableTime()
    {
        return $this->_enableTime;
    }
    
	public function getUpdateBy()
    {
        return $this->_updateBy;
    }

	public function getUpdateDate()
    {
        return $this->_updateDate;
    }

	public function getAddBy()
    {
        return $this->_addBy;
    }

	public function getAddDate()
    {
        return $this->_addDate;
    }

	public function getContent()
    {
        return $this->_content;
    }

	public function getLead()
    {
        return $this->_lead;
    }

	public function getTitle()
    {
        return $this->_title;
    }

	public function getId()
    {
        return $this->_id;
    }

	public function getCategory( $index )
    {
        if( isset( $this->_categories[$index] ) )
            return $this->_categories[$index];
        else
            return null;
    }
    
	public function getCategories()
    {
        return $this->_categories;
    }

	public function setCategories( Dupa_List $categories )
    {
        $this->_categories = $categories;
    }

	public function setCategory( Dupa_Category_Container $category )
    {
        if( !$this->_categories )
            $this->_categories = new Dupa_List();
        $this->_categories[ count( $this->_categories ) ] = $category;
    }
    
    static public function checkStatus( $status )
    {
        return in_array( $status, array( self::STATUS_ENABLED,
                                         self::STATUS_DISABLED,
                                         self::STATUS_DELETED,
                                         self::STATUS_DEV ) ) ? $status : null;
    }
}

?>