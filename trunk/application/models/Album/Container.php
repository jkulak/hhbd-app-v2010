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
      $this->artist = $artistApi->find($params['art_id']);
    }    
    
    if (!empty($params['lab_id'])) {
      $labelApi = new Model_Label_Api();
      $this->label = $labelApi->find($params['lab_id']);
      if ($this->label->name == 'BRAK') $this->label->name = '--';
    } else {
      $this->label->name = '--';
    }
    
    if (!empty($params['legal'])) {
      $this->legal = ($params['legal']=='y')?true:false;
    }
    
    $this->releaseDate = $params['year'];
    $this->releaseDateNormalized = Jkl_Tools_Date::getNormalDate($this->releaseDate);
    
    if (!empty($params['catalog_cd'])) {
      $this->catalogNumber = $params['catalog_cd'];
    }
    
    if (!empty($params['epfor'])) {
      $this->epFor = $params['epfor'];
    }
    
    if (!empty($params['singiel'])) {
      $this->ep = $params['singiel'];
    }

    // TODO: users api
    if (!empty($params['alb_addedby'])) $this->addedBy = $params['alb_addedby'];
    if (!empty($params['alb_added'])) $this->added = $params['alb_added'];
    if (!empty($params['alb_viewed'])) $this->views = $params['alb_viewed'];
    if (!empty($params['updated'])) $this->updated = $params['updated'];
    
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
    if (!empty($params['updated'])) {
      $this->updated = $params['updated'];
    }
    
    if (!empty($params['status'])) {
      $this->status = $params['status'];
    }
    
    if ($full) {
      $this->tracklist = $params['tracklist'];
      $this->description = $params['description'];
      $this->eps = $params['eps'];
      $this->duration = $params['duration'];
      $this->voteCount = $params['votecount'];
    }
    
    $this->url = Jkl_Tools_Url::createUrl($this->title);
    
    // description autogeneration, if description is not set, seo purposes
    if (empty($this->description) AND $full) {
      $music = array();
      $scratch = array();
      $feat = array();
      $rap = array();
      
      foreach ($this->tracklist->items as $key => $value) {
        foreach ($value->featuring->items as $data) {
          $feat[] = $data->name;
        }
        foreach ($value->music->items as $data) {
          $music[] = $data->name;
        }
        foreach ($value->scratch->items as $data) {
          $scratch[] = $data->name;
        }
        foreach ($value->artist->items as $data) {
          $rap[] = $data->name;
        } 
      }
      
      $eps = array();
      foreach ($this->eps->items as $key => $value) {
        $eps[] = $value->title;
      }
      
      if ($this->isAnnounced()) {
        $this->description = 'Długo oczekiwany album ' . $this->title . ', został zapowiedziany przez wytwórnię ' . $this->label->name .
        '. Premiera planowana jest na ' . $this->releaseDateNormalized . ', czyli już niedługo! ' .
        (!empty($this->tracklist->items)?'Album ma zawierać ' . sizeof($this->tracklist->items) . ' utworów. ' .
        'Płyta będzie otwarta utworem ' . $this->tracklist->items[0]->title . ', a zamknięta utworem ' . $this->tracklist->items[sizeof($this->tracklist->items)-1]->title . '. ':'') .
        'Czekamy z niecierpliwością. ' .
        '';
      } else {
        $this->description = 'Album "' . $this->title . '", został wydany przez wytwórnię ' . $this->label->name . ', ' .
        $this->releaseDateNormalized . '. ' .
        (!empty($this->tracklist->items)?'Album zawiera ' . sizeof($this->tracklist->items) . ' utworów' . (($this->duration!="--")?' i trwa ' . $this->duration:''). '. ' .
        'Płyta rozpoczyna się utworem "' . $this->tracklist->items[0]->title . '", a kończy utworem "' . $this->tracklist->items[sizeof($this->tracklist->items)-1]->title . '". ':'');
      }
      $this->description .= 
        ((!empty($eps))?'Album "' . $this->title . '" jest poprzedzony singlami: "' . implode(array_unique($eps), '", "') . '". ':'') .
        ((!empty($this->epFor))?'Album "' . $this->title . '" jest singlem do albumu "' . $this->epFor->title . '". ':'') .
        ((!empty($rap))?'Za rymy i rap na płycie, odpowiedzialni są: ' . implode(array_unique($rap), ', ') . '. ':'') . 
        ((!empty($music))?'Warstwę muzyczną zapewnili: ' . implode(array_unique($music), ', ') . '. ':'') . 
        ((!empty($scratch))?'Scratch i cuty na płycie to zasługa: ' . implode(array_unique($scratch), ', ') . '. ':'') . 
        ((!empty($feat))?'Gościnnie na albumie udzielają się: ' . implode(array_unique($feat), ', ') . '. ':'') . 
        '' . 
        $this->artist->name . ' to prawdziwy polski hip-hop. ' . 
        'Aby zobaczyć teksty piosenek, należy kliknąć w tytuły na liście powyżej. ';
    }
    
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