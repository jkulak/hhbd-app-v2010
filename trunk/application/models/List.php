<?php

class Model_List extends Zend_Db_Table_Abstract
{
  
  static private $_instance;
  
  // db table name
  protected $_name = 'hhb_lists';
  const TYPE_COLLECTION = 'c';
  const TYPE_WISHLIST = 'w';
  
  
  /**
   * Singleton instance
   *
   * @return Model_Search_Api
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
  private function _getList($query)
  {
    $result = $this->_db->fetchAll($query);;
    $list = new Jkl_List('User list'); 
    foreach ($result as $params) {
      // conversion to stdObject, because blah :/ Model_UserContainer takes objects as parameters
      $list->add(new Model_User_Container((object)$params));
    }
    return $list;
  }

  /*
  * Validate and save album to list
  */
  public function save($albumId, $userId, $listType)
  {
    $result = false;
    // Initialize the errors array
    $errors = array();

    if (!Zend_Validate::is($albumId, 'Digits')) {
      $errors[] = 'album id nieprawidłowe';
    }
    
    if (!Zend_Validate::is($userId, 'Digits')) {
      $errors[] = 'user id nieprawidłowe';
    }
    
    if (!in_array($listType, array('w', 'c'))) {
      $errors[] = 'typ listy nieprawidłowy';
    }

    // If no errors, insert the 
    if (count($errors) == 0) {
      $data = array (
        'lis_usr_id' => $userId,
        'lis_type' => $listType,
        'lis_alb_id' => $albumId,
        'lis_added_by' => $userId,
        'lis_added' => date('Y-m-d H:i:s'),
        'lis_added_ip' => Jkl_Tools_Network::getIps()
        );
        
      try {
        $result = $this->insert($data);
      } catch (Exception $e) {
        switch ($e->getCode()) {
          case 23000:
            Zend_Registry::get('Logger')->notice(sprintf('hhb_lists duplicate for usr_id: %d, alb_id: %d, lis_type: %s', $userId, $albumId, $listType));
            break;
          default:
            # code...
            break;
        }
      }
      
      return $result;
    }
    else
    {
      return $errors;
    }
  }
  
  /**
   * Remove album from users list
   *
   * @return boolean result
   * @since 2011-05-03
   * @author Kuba
   * @file: List.php
   **/
  public function remove($albumId, $userId, $listType)
  {
    $where = array(
      'lis_usr_id = ?' => $userId, 
      'lis_alb_id = ?' => $albumId, 
      'lis_type = ?' => $listType
      );
    
    $row = $this->delete($where);
    
    return $row !== null;
  }
  
  
  /**
   * Check if album is present in users list
   *
   * @return boolean is present
   * @since 2011-05-03
   * @author Kuba
   * @file: ListController.php
   **/
  public function present($albumId, $userId, $listType)
  {
    $where = array(
      'lis_usr_id = ?' => $userId, 
      'lis_alb_id = ?' => $albumId, 
      'lis_type = ?' => $listType
      );
    
    $row = $this->fetchRow($where);
    
    return $row !== null;
  }
}