<?php

/**
 * Album
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 11 October, 2010
 * @package default
 **/

class Model_Album_Container
{
  
  public $id;
  public $title;
  public $artist;
  public $releaseDate;
  public $cover;
  
  private $_appConfig;
  
  function __construct($params, $full = false)
  {

    $this->_appConfig = Zend_Registry::get('Config_App');

    $this->id = $params['alb_id'];
    $this->title = $params['title'];
    
    $artistApi = new Model_Artist_Api();
    $this->artist = $artistApi->find($params['art_id'], $full);
    
    $labelApi = new Model_Label_Api();
    $this->label = $labelApi->find($params['labelid'], $full);
    
    $this->legal = ($params['legal']=='y')?true:false;
    $this->releaseDate = $params['year'];
    $this->releaseDateNormalized = Jkl_Tools_Date::getNormalDate($this->releaseDate);
    $this->catalogNumber = $params['catalog_cd'];
    
    $this->epFor = $params['epfor'];
    $this->ep = $params['singiel'];

    // TODO: users api
    $this->addedBy = $params['alb_addedby'];
    $this->added = $params['alb_added'];
    $this->updated = $params['updated'];
    $this->views = $params['alb_viewed'];
    
    if (!empty($params['cover'])) {
      $this->cover = $this->_appConfig['paths']['albumCoverPath'] . $params['cover'];
      $this->thumbnail = $this->_appConfig['paths']['albumThumbnailPath'] . substr($params['cover'], 0, -4) . $this->_appConfig['paths']['albumThumbnailSuffix'];
    }
    else
    {
      $this->cover = $this->_appConfig['paths']['albumCoverPath'] . 'cd.png';
      $this->thumbnail = $this->_appConfig['paths']['albumThumbnailPath'] . 'cd.png';
    }

    $this->updated = $params['updated'];
    $this->status = $params['status'];
    
    if ($full) {
      $this->tracklist = $params['tracklist'];
      $this->description = $params['description'];
      $this->eps = $params['eps'];
      $this->duration = $params['duration'];
      $this->rating = $params['rating'];
      $this->voteCount = $params['votecount'];
    }
    
  }
  
  /**
   * Checks if album is announced, or already released
   */
  public function isAnnounced() {
    return ($this->releaseDate >= date('Y-m-d'));
  }
  
  public function url($canonical = false)
  {
    return Jkl_Tools_Url::createUrl($this->title);
    // return $this->title;
  }
}
     // [premier] => 
     // [media_mc] => 1
     // [catalog_mc] => 
     // [media_cd] => 1
     // [media_lp] => 0
     // [catalog_lp] => 
     // [artistabout] => 
     // [notes] => 