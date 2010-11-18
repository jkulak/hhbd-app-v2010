<?php

/**
 * Artist
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 11 October, 2010
 * @package default
 **/

class Model_Artist_Container
{
  
  public $id;
  public $name;
    
  function __construct($params, $full = false)
  {
    $this->id = $params['id'];
    $this->name = $params['name'];
  }

  public function url($canonical = false)
  {
    return Jkl_Tools_Url::createUrl($this->name);
    // return $this->title;
  }
}