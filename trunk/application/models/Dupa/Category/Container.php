<?php

/**
 * Kontener kategorii
 * 
 * @author Playah
 */
class Dupa_Category_Container
{	
	/**
	 * Id kategorii
	 * 
	 * @var int
	 */
	protected $_id;
	
	/**
	 * nazwa kategorii
	 * 
	 * @var string
	 */
	protected $_name;

	/**
	 * Data dodania
	 * 
	 * @var string
	 */
	protected $_addDate;
	
	/**
	 * Uzytkownik, ktory dodal kategorie
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
	 * Uzytkownik, ktory ostatnio aktualizowal kategorie
	 * 
	 * @var Dupa_User_Container
	 */
	protected $_updateBy;
	
	/**
	 * Konstruktor
	 * 
	 * @return Dupa_Article_Container
	 */
	function __construct() { }
	
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

	public function setName( $name )
    {
        $this->_name = $name;
    }

	public function setId( $id )
    {
        $this->_id = $id;
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

	public function getName()
    {
        return $this->_name;
    }

	public function getId()
    {
        return $this->_id;
    }
}

?>