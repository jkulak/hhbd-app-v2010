<?php

class SongController extends Zend_Controller_Action
{
  
  public function init()
  {
    $this->view->headMeta()->setName('keywords', 'polski hip-hop, albumy');
    $this->view->headTitle()->headTitle('Piosenki', 'PREPEND');
    $this->view->headMeta()->setName('description', 'Piosenki, teksty, teledyski w hhbd.pl');
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
  }
  
  public function viewAction()
  {
    // content
    $params = $this->getRequest()->getParams();
    $song = Model_Song_Api::getInstance()->find($params['id'], true);
    $song->autoDescription = $this->_generateDescription($song);
    $this->view->song = $song;
    
    // sidenotes
    $albumSongs = array();
    foreach ($this->view->song->featured->items as $key => $value) {
      $albumSongs[] = Model_Song_Api::getInstance()->getTracklist($value->id, null);
    }
    $this->view->albumSongs = $albumSongs;
    $this->view->popularSongs = Model_Song_Api::getInstance()->getMostPopular(15);
    $this->view->autoPlay = isset($this->params['autoplay']);

    // seo meta
    $this->view->headTitle()->set($this->view->song->albumArtist->name . ' - ' . $this->view->song->title . ' tekst, teledysk, sample');
        $this->view->headMeta()->setName('keywords', $this->view->song->albumArtist->name . ',' . $this->view->song->title . ',tekst,teledysk,sample');
    $this->view->headMeta()->setName('description', $this->view->song->albumArtist->name . ' - ' . $this->view->song->title . ', tekst, teledysk, premiera, informacje o samplach i inne ciekawe informacje o piosence na największej polskiej stronie o hip-hopie.');
  }
  
  // description autogeneration, displayedfor SEO purposes
  private function _generateDescription($song)
  {
    $description = '';
    
    $description .= 'Utwór ' . $song->title . ', ';
    if (!empty($song->artist->items)) {
      $description .= 'na którym wokalnie udziela się ';
      foreach ($song->artist->items as $key => $value) {
        $description .= $value->name;
        if (sizeof($song->featured->items) - 1 > $key) {
          $description .= (sizeof($song->artist->items)-3 == $key)?', ':' i ';
        }
      }
      $description .= ', ';
    }
    
    $description .= 'został wydany na ' . sizeof($song->featured->items) . ' ';
    $description .= ((sizeof($song->featured->items)>1)?'albumach':'albumie') . ': ';
    foreach ($song->featured->items as $key => $value) {
      $description .= '"' . $value->title . '"';
      if (sizeof($song->featured->items) - 1 > $key) {
        $description .= (sizeof($song->featured->items) - 2 == $key)?', ':' i ';
      }
    }
    $description .= '. ';
    
    if (!empty($song->music->items)) {
      $description .= 'Muzykę do tego numer zrobił ';
      foreach ($song->music->items as $key => $value) {
        $description .= $value->name;
        if (sizeof($song->featured->items) - 1 > $key) {
          $description .= (sizeof($song->music->items) - 1 == $key)?', ':' i ';
        }
      }
      $description .= '. ';
    }
    
    if (!empty($song->scratch->items)) {
      $description .= 'Skreczowaniem i cutami zajął się  ';
      foreach ($song->scratch->items as $key => $value) {
        $description .= $value->name;
        if (sizeof($song->featured->items) - 1 > $key) {
          $description .= (sizeof($song->scratch->items) - 2 == $key)?', ':' i ';
        }
      }
      $description .= '. ';
    }
    
    if (!empty($song->duration)) {
      $description .= 'Utwór ' . $song->title . ' trwa ' . $song->duration . '. ';
    }

    if (!empty($song->featuring->items)) {
      $description .= 'Gościnnie na albumie udzielaja się ';
      foreach ($song->featuring->items as $key => $value) {
        $description .= $value->name . ' (' . $value->featType . ')';
        if (sizeof($song->featured->items) - 1 > $key) {
          $description .= (sizeof($song->featuring->items) - 1 == $key)?', ':' i ';
        }
      }
      $description .= '. ';
    }
    
    if (empty($song->lyrics)) {
      $description .= 'Nie posiadamy na razie tekstu tego utworu. Jeżeli masz, wyślij nam tekst ' . $song->title . '. ';
    }
    
    return $description;
  }
}