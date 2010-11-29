<?php

class SongController extends Zend_Controller_Action
{
  
  public function init()
  {
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
  }
}