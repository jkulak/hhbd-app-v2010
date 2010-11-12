<?php

/**
 * Paczkowanie dla listy
 * 
 * @author Playah *
 */
class Dupa_Navigation_Pack
{
    /**
     * Konstruktor
     * 
     * @return Navigation_Pack
     */
    function __construct() { }
    
    protected $_packs;
    
    protected $_currentPack;
    
    protected $_prevPack;
    
    protected $_nextPack;
    
    protected $_totalPacks;

    protected $_packSize;
    
    protected $_totalItems;
    
    public function prepare( $pack, $packSize, $totalItems )
    {
        $this->_currentPack = intval( $pack );
        $this->_packSize = intval( $packSize );
        $this->_totalItems = intval( $totalItems );
        
        if( $this->getPackSize() )
        {
            $this->_totalPacks = ceil( $this->getTotalItems() / $this->getPackSize() );
            $this->_prevPack = $this->getCurrentPack() > 1 ? $this->getCurrentPack() - 1 : null;
            $this->_nextPack = $this->getCurrentPack() < $this->getTotalPacks() ? $this->getCurrentPack() + 1 : null;
        }        
    }
    
	public function getPacks()
    {
        return $this->_packs;
    }
    
	public function getCurrentPack()
    {
        return $this->_currentPack;
    }
    
	public function getPrevPack()
    {
        return $this->_prevPack;
    }
    
	public function getNextPack()
    {
        return $this->_nextPack;
    }

	public function getPackSize()
    {
        return $this->_packSize;
    }
    
	public function getTotalItems()
    {
        return $this->_totalItems;
    }
    
	public function getTotalPacks()
    {
        return $this->_totalPacks;
    }
}
?>