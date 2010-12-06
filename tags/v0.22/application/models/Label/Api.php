<?php
/**
 * Label Api
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 12 October, 2010
 * @package hhbd
 **/

class Model_Label_Api extends Jkl_Model_Api
{
  static private $_instance;
  
  /**
   * Singleton instance
   *
   * @return Model_Label_Api
   */
  public static function getInstance()
  {
      if (null === self::$_instance) {
          self::$_instance = new self();
      }
      return self::$_instance;
  }

  /**
   * Creates object and fetches the list from database result
   */
  public function getList($query)
  {
    $result = $this->_db->fetchAll($query);
    $list = new Jkl_List(); 
    foreach ($result as $params) {
      $list->add(new Model_Label_Container($params));
    }
    return $list;
  }
  
  public function find($id, $full = false)
  {
    $query = 'select *, id AS lab_id from labels where id=' . $id;
    $result = $this->_db->fetchAll($query);
    $item = new Model_Label_Container($result[0], $full);
    return $item;
  }
  
  public function getFullList()
  {
    $query = "SELECT t1.id AS lab_id, t1.`name`, t1.`website`, count(t2.id) AS album_count
              FROM labels t1, albums t2
              WHERE (t1.id!=27 AND t2.`labelid`=t1.id)
              GROUP BY t1.`id`
              ORDER BY t1.`name`";
    return $this->getList($query);
  }
}
