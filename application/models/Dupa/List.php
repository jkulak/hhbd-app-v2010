<?php

require_once 'Dupa/Navigation/Pack.php'; 

/**
 * Obsluga list (tak jak tablic)
 * 
 * @author Playah
 */
class Dupa_List implements ArrayAccess, Countable
{    
    /**
     * Ilosc elementow na liscie
     * 
     * @var unknown_type
     */
    public $cntItems;
    
    /**
     * Lista elementow
     * 
     * @var unknown_type
     */
    protected $_items;
    
    /**
     * Nawigacja i paczkowanie dla listy
     */
    protected $_navigation;
    
    public function __construct()
    {
        $this->_navigation = new Dupa_Navigation_Pack();
    }
    
    public function getNavigation()
    {
        return $this->_navigation;        
    }
    
    public function prepareNavigation( $pack, $packSize )
    {
        $this->_navigation->prepare( $pack, $packSize, $this->cntItems );
    }
    
    public function offsetSet( $offset, $value )
    {
        $this->_items[$offset] = $value;
    }

    public function offsetExists( $offset )
    {
        return isset( $this->_items[$offset] );
    }

    public function offsetUnset( $offset )
    {
        unset( $this->_items[$offset] );
    }

    public function offsetGet( $offset )
    {
        return $this->_items[$offset];
    }
    
	public function count()
	{
		return count( $this->_items );
    }    
}
?>