<?php

/**
 * User Container
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 29 December, 2010
 * @package default
 **/
 
class Model_User_Container
{
  private $_id;
  private $_email;
  private $_firstName;
  private $_lastName;
  private $_displayName;
  
  function __construct($params)
  {
    $this->_id = $params->usr_id;
    $this->_email = $params->usr_email;
    $this->_displayName = $params->usr_display_name;
    if (isset($params->usr_first_name)) {
      $this->_firstName = $params->usr_first_name;
    }
    
    if (isset($params->usr_last_name)) {
      $this->_lastName = $params->usr_last_name;
    } 
  }
  
  public function getId()
  {
    return $this->_id;
  }
  
  public function getEmail()
  {
    return $this->_email;
  }
  
  public function getDisplayName()
  {
    return $this->_displayName;
  }
  
  public function getFirstName()
  {
    return $this->_firstName;
  }
  
  public function getLastName()
  {
    return $this->_lastName;
  }
  
  public function getUrlName()
  {
    return Jkl_Tools_Url::createUrl($this->_displayName);
  }
}