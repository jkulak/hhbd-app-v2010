<?php

class Model_Article_Container
{

  protected $_id;
  protected $_title;
  protected $_lead;
  protected $_body;
  protected $_author;
  protected $_categoryId;
  
  
  function __construct($params = array()) {
    if (sizeof($params)) {
      if (isset($params['id'])) {
        $this->_id = $params['id'];
      }
      $this->setTitle($params['title']);
      $this->_lead = $params['lead'];
      $this->_body = $params['body'];
      $this->_author = $params['author'];
      $this->_categoryId = $params['categoryId'];
    }
  }
  
  $oesd->Dupa = "sfsdf";
  
  public function __set($name, $value)
  {
    $method = 'set' . $name;
    if (('mapper' == $name) || !method_exists($this, $method)) {
        throw new Exception('Invalid article property');
    }
    $this->$method($value);
  }
  
  public function __get($name)
  {
    $method = 'get' . $name;
    if (('mapper' == $name) || !method_exists($this, $method)) {
       throw new Exception('Invalid article property');
    }
    return $this->$method();
  }
  
  public function setTitle($title)
  {
    $this->_title = (string) $title;
    return $this;
  }

  public function getTitle()
  {
    return $this->_title;
  }

  public function setId($id)
  {
    $this->_id = (int) $id;
    return $this;
  }

  public function getId()
  {
    return $this->_id;
  }
  
  public function setLead($lead)
  {
    $this->_lead = (string) $lead;
    return $this;
  }

  public function getLead()
  {
    return $this->_lead;
  }
  
  public function setBody($body)
  {
    $this->_body = (string) $body;
    return $this;
  }

  public function getBody()
  {
    return $this->_body;
  }
  
  public function setAuthor($author)
  {
    $this->_author = (string) $author;
    return $this;
  }

  public function getAuthor()
  {
    return $this->_author;
  }
  
  public function setCategoryId($categoryId)
  {
    $this->_categoryId = (int) $categoryId;
    return $this;
  }

  public function getCategoryId()
  {
    return $this->_categoryId;
  }
  
  public function save() {
    //tutaj pobieram instancje Article_Dao
    // i jako parametr przekazuje $this, albo $this->toArray();
    // jezeli sie da, to na podstawie istnienia id albo update albo insert na bazie
    return true;
  }
}