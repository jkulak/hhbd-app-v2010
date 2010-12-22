<?php
/**
* Jkl_Og
*/
class Jkl_Og
{
  private $_title;
  private $_description;
  private $_image;
  
  function __construct()
  {
    # code...
  }
  
  public function setTitle($title)
  {
    $this->_title = $title;
  }
  
  public function setDescription($description)
  {
    $this->_description = $description;
  }
  
  public function setImage($image)
  {
    $this->_image = $image;
  }
  
  public function echoMeta()
  {
    $ogMeta = '';
    if (isset($this->_title)) {
      $ogMega .= '<meta property="og:title" content="' . $this->_title . '" />';
    }
    
    if (isset($this->_description)) {
      $ogMega .= '<meta property="og:description" content="' . htmlentities($this->_description) . '" />';
    }
    
    if (isset($this->_image)) {
      $ogMega .= '<meta property="og:image" content="' . $this->_image . '" />';
    }
    
    return $ogMega;
  }
  
}
