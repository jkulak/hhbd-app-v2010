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

  public function find($id, $full = false)
  {
    $query = 'select * from labels where id=' . $id;
    $result = $this->_db->fetchAll($query);
    $item = new Model_Label_Container($result[0], $full);
    return $item;
  }
}
