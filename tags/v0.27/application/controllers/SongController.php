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
    $this->view->song = Model_Song_Api::getInstance()->find($params['id'], true);
    
    // sidenotes
    $albumSongs = array();
    foreach ($this->view->song->featured->items as $key => $value) {
      $albumSongs[] = Model_Song_Api::getInstance()->getTracklist($value->id, null);
    }
    $this->view->albumSongs = $albumSongs;
    $this->view->popularSongs = Model_Song_Api::getInstance()->getMostPopular(15);

    // seo meta
    $this->view->headTitle()->set($this->view->song->albumArtist->name . ' - ' . $this->view->song->title . ' tekst, teledysk piosenki w www.hhbd.pl');
        $this->view->headMeta()->setName('keywords', $this->view->song->albumArtist->name . ' - ' . $this->view->song->title . ',tekst,teledysk,teksty piosenek,słowa,teledyski,video');
    $this->view->headMeta()->setName('description', $this->view->song->albumArtist->name . ' - ' . $this->view->song->title . ', tekst piosenki, teledysk i inne ciekawe informacje na największej polskiej stronie o hip-hopie.');
  }
}