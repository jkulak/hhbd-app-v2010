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
  const TYPE_ARTIST = "Wykonawca";
  const TYPE_PROJECT = "Projekt";
  const TYPE_MALE = "Raper";
  
  private $_artistTypes = array(
    'x' => Model_Artist_Container::TYPE_ARTIST,
    'b' => Model_Artist_Container::TYPE_PROJECT,
    'm' => Model_Artist_Container::TYPE_MALE);
  
  public $id;
  public $name;  
    
  function __construct($params, $full = false)
  {
    // print_r($params);
    
    $this->id = $params['id'];
    $this->name = $params['name'];
    $this->url = Jkl_Tools_Url::createUrl($this->name);
    if (!empty($params['since'])) {
      $this->started = ($params['since']!='0000-00-00')?$params['since']:null;
    }
    if (!empty($params['till'])) {
      $this->ended = ($params['till']!='0000-00-00')?$params['till']:null;
    }
    
    if ($full) {
      $this->realName = $params['realname'];
      $this->profile = $params['profile'];
      $this->concertInfo = $params['concertinfo'];
      
      $this->type = $this->_artistTypes[$params['type']];
      $this->isSpecial = ($params['special']==1)?true:false;
      $this->trivia = $params['trivia'];
      $this->website = $params['website'];

      $this->added = $params['added'];
      $this->addedBy = $params['addedby'];
      $this->updated = $params['updated'];
      $this->updatedBy = $params['updatedby'];

      $this->viewed = $params['viewed'];
      $this->status = $params['status'];
      $this->hits = $params['hits']; 
      
      // list of photos
      $this->photos = $params['photos'];
      
      // also known as
      $this->alsoKnownAs = $params['aka'];
      
      // band members
      if (!empty($params['members'])) {
        $this->members = $params['members'];
      }
      
      // member of bands
      $this->projects = $params['projects'];
      
      // city
      $this->cities = $params['cities'];
      
    }
  }
}