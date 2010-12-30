<?php

class Model_User extends Zend_Db_Table_Abstract
{
  
  static private $_instance;
  
  // db table name
  protected $_name = 'hhb_user';
  static public $passwordSalt = 'this is long enough safety salt!';
  
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

  /*
  * Validate and save user into db
  */
  public function save(array $data)
  {
    // Initialize the errors array
    $errors = array();

    // Password must be at least 6 characters
    $validDisplayName = new Zend_Validate_StringLength(3, 30);
    if (! $validDisplayName->isValid($data['display-name'])) {
       $errors['displayName'][] = "Twoja nazwa musi mieć od 6 do 20 znaków.";
    }
    else
    {
      $result = $this->findByDisplayName($data['display-name']);
      if (count($result) > 0) {
        $errors['displayName'][] = "Ktoś już używa takiej nazwy, wpisz inną.";
      }
    }

    // First Name
    // if (! Zend_Validate::is($data['first-name'], 'NotEmpty')) {
    //    $errors['fistName'][] = "Please provide your first name.";
    // }
    // 
    // // Last Name
    // if (! Zend_Validate::is($data['last-name'], 'NotEmpty')) {
    //    $errors['lastName'][] = "Please provide your last name.";
    // }

    // Does Email already exist?
    if (Zend_Validate::is($data['email'], 'EmailAddress')) {

     $result = $this->findByEmail($data['email']);
     // var_dump($result); die();
     if ($result) {
       $errors['email'][] = "Konto dla tego adresu e-mail już istnieje, podaj inny";
     }

    } else {
     $errors['email'][] = "Podaj poprawny adres e-mail.";
    }

    // Password must be at least 6 characters
    $validPassword = new Zend_Validate_StringLength(6,20);
    if (! $validPassword->isValid($data['password'])) {
       $errors['password'][] = "Twoje hasło musi mieć od 6 do 20 znaków.";
    }

    // If no errors, insert the 
    if (count($errors) == 0) {
      $data = array (
        // 'usr_first_name' => $data['first-name'],
        // 'usr_last_name' => $data['last-name'],
        'usr_display_name' => $data['display-name'],
        'usr_email' => $data['email'],
        'usr_password' => md5($data['password'] . self::$passwordSalt),
        'usr_recovery_key' => ''
        );
        $result = $this->insert($data);
      return $result;
    }
    else
    {
      return $errors;
    }
  }
  
  public function findByEmail($email)
  {
    $email = strval($email);
    
    $result = $this->fetchAll('usr_email = "' . $email . '"');
    if (count($result) > 0) {
      return new Model_User_Container($result->current());
    }
    else
    {
      return false;
    }
    
  }
  
  public function findByDisplayName($name)
  {
    $name = strval($name);
    
    $rows = $this->fetchAll('usr_display_name = "' . $name . '"');
    return $rows;
  }
  
  public function findById($id)
  {
    $id = intval($id);
    $result = $this->find($id);
    return new Model_User_Container($result->current());    
  }
}