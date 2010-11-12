<?php
/**
 * Artist Api
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 12 October, 2010
 * @package hhbd
 **/

class Model_Artist_Api extends Jkl_Model_Api
{  
  public function find($id, $full = false)
  {
    $query = 'select * from artists where id=' . $id;
    $result = $this->_db->fetchAll($query);
    $item = new Model_Artist_Container($result[0], $full);
    return $item;
  }
  
  /**
   * Creates object and fetches the list from database result
   */
  public function getList($query)
  {
    $result = $this->_db->fetchAll($query);
    $artists = new Jkl_List();
    
    foreach ($result as $params) {
      $artists->add(new Model_Artist_Container($params));
    }
    return $artists;
  }
  
  public function getNewest($num = 20)
  {
    $query = 'SELECT * ' . 
    'FROM artists ' .
    'ORDER by added DESC ' . 
    'LIMIT ' . $num;
    return $this->getList($query);
  }
}
