<?php

/**
 * Image
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 11 October, 2010
 * @package default
 **/

class Model_Image_Container
{
  
  public $id;
  public $filename;
  public $source = null;
  public $sourceUrl = null;
  public $isMain = false;
  public $url;
    
  function __construct($params)
  {
    // print_r($params);
    
    $this->id = $params['id'];
    $this->filename = $params['filename'];
    $this->url = $params['url'];
    $this->source = $params['source'];
    $this->sourceUrl = $params['sourceurl'];
    $this->isMain = ($params['main'] == 'y');
  }
}