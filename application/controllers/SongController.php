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
    $params = $this->getRequest()->getParams();
    $this->view->song = Model_Song_Api::getInstance()->find($params['id'], true);
    $this->view->song->featured = Model_Album_Api::getInstance()->getSongAlbums($params['id'], null);

    $this->view->popularSongs = Model_Song_Api::getInstance()->getMostPopular(25);

    $names = array();
    foreach ($this->view->song->artist->items as $key => $value) {
      $names[] = $value->name;
    }

    foreach ($this->view->song->featuring->items as $key => $value) {
      $names[] = $value->name;
    }


    // dodac
    $this->view->headTitle()->set(((!empty($names))?implode($names, ', ') . ' - ':'') . $this->view->song->title . ' tekst piosenki w www.hhbd.pl');
        $this->view->headMeta()->setName('keywords', implode($names, ',') . ',' . $this->view->song->title . ',tekst,teksty piosenek,słowa,teledyski,video');
    $this->view->headMeta()->setName('description', implode($names, ', ') . ' - ' . $this->view->song->title . ', tekst piosenki i inne ciekawe informacje na największej polskiej stronie o hip-hopie.');
  }
}