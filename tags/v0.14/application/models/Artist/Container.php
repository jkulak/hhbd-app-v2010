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
      
      //it happens it has only spaces, so it's trimmed
      $this->concertInfo = trim($params['concertinfo']);
      
      $this->type = $this->_artistTypes[$params['type']];
      $this->isSpecial = ($params['special']==1)?true:false;
      
      $this->trivia = trim($params['trivia']);
      
      if (!empty($params['website'])) {
        $website = $params['website'];
        if (substr_count($website, 'http://') == 0) {
          $website = 'http://' . $website;
        }
        $this->website = $website;
      }

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
      if (!empty($params['projects'])) {
        $this->projects = $params['projects'];
      }

      // city
      $this->cities = $params['cities'];
      
      if (!empty($params['albums'])) {
        $this->albums = $params['albums'];
      }
      
      if (!empty($params['projectalbums'])) {
        $this->projectAlbums = $params['projectalbums'];
      }
    }
  }
  
  public function addAlbums($albums)
  {
    $this->albums = $albums;
  }
  
  public function addProjectAlbums($albums)
  {
    $this->projectAlbums = $albums;
  }
  
  public function addFeaturing($albums)
  {
    $this->featuring = $albums;
  }
  
  public function addMusic($albums)
  {
    $this->music = $albums;
  }
  
  public function addScratch($albums)
  {
    $this->scratch = $albums;
  }
  
  public function addPopularSongs($list)
  {
    $this->popularSongs = $list;
  }
}