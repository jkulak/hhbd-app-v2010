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
    
    // echo get_class($this) . '->' . __FUNCTION__ . '(' .$params['alb_id'] . ')<br />';
    
    $this->_appConfig = Zend_Registry::get('Config_App');

    $this->id = $params['alb_id'];
    $this->title = $params['title'];
    
    if (!empty($params['art_id'])) {
      $artistApi = new Model_Artist_Api();
      $this->artist = $artistApi->find($params['art_id'], $full);
    }    
    
    if (!empty($params['lab_id'])) {
      $labelApi = new Model_Label_Api();
      $this->label = $labelApi->find($params['lab_id'], $full);
      if ($this->label->name == 'BRAK') $this->label->name = '--';
    } else {
      $this->label->name = '--';
    }
    
    $this->legal = ($params['legal']=='y')?true:false;
    $this->releaseDate = $params['year'];
    $this->releaseDateNormalized = Jkl_Tools_Date::getNormalDate($this->releaseDate);
    $this->catalogNumber = $params['catalog_cd'];
    
    $this->epFor = $params['epfor'];
    $this->ep = $params['singiel'];

    // TODO: users api
    if (!empty($params['alb_addedby'])) $this->addedBy = $params['alb_addedby'];
    if (!empty($params['alb_added'])) $this->added = $params['alb_added'];
    if (!empty($params['alb_viewed'])) $this->views = $params['alb_viewed'];
    
    $this->updated = $params['updated'];
    
    if (!empty($params['cover'])) {
      $this->cover = $this->_appConfig['paths']['albumCoverPath'] . $params['cover'];
      $this->thumbnail = $this->_appConfig['paths']['albumThumbnailPath'] . substr($params['cover'], 0, -4) . $this->_appConfig['paths']['albumThumbnailSuffix'];
    }
    else
    {
      $this->cover = $this->_appConfig['paths']['albumCoverPath'] . 'cd.png';
      $this->thumbnail = $this->_appConfig['paths']['albumThumbnailPath'] . 'cd.png';
    }
    
    if (!empty($params['rating'])) {
      $this->rating = number_format($params['rating'], 1);
    } else {
      $this->rating = '--';
    }

    $this->updated = $params['updated'];
    $this->status = $params['status'];
    
    if ($full) {
      $this->tracklist = $params['tracklist'];
      $this->description = $params['description'];
      $this->eps = $params['eps'];
      $this->duration = $params['duration'];
      $this->voteCount = $params['votecount'];
    }
    
    $this->url = Jkl_Tools_Url::createUrl($this->title);
    
  }
  
  /**
   * Checks if album is announced, or already released
   */
  public function isAnnounced() {
    return ($this->releaseDate >= date('Y-m-d'));
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