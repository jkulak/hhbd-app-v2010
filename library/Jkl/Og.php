<?php
/**
* Jkl_Og
*/
class Jkl_Og
{
  private $_title;
  private $_description;
  private $_image;
  private $_siteName;
  
  function __construct($siteName)
  {
    $this->_siteName = $siteName;
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
      $ogMeta .= '<meta property="og:title" content="' . str_replace('"', '&quot;', $this->_title) . '" />' . "\n";
    }
    
    if (isset($this->_description)) {
      $description = substr($this->_description, 0, strpos($this->_description, ' ', 297) - 1) . '...';
      $ogMeta .= '<meta property="og:description" content="' . str_replace('"', '&quot;', $description) . '" />' . "\n";
    }
    
    if (isset($this->_image)) {
      $ogMeta .= '<meta property="og:image" content="' . $this->_image . '" />' . "\n";
    }
    
    $ogMeta .= '<meta property="og:site_name" content="' . $this->_siteName . '" />' . "\n";
    
    return $ogMeta;
  }
  
}