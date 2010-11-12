<?php

class ArtistController extends Zend_Controller_Action
{

  private $artistApi;
  
  public function init()
  {
    $this->artistApi = new Model_Artist_Api();
    
    $this->view->headMeta()->setName('keywords', 'hhbd.pl, polski hip-hop, albumy');
    $this->view->headTitle()->headTitle('Album');
    $this->view->headMeta()->setName('description', 'Albumy w hhbd.pl');
  }

  public function indexAction()
  {
    // $this->view->firstLetters = $this->albumApi->getFirstLetters();
    // $this->view->popularAlbums = $this->albumApi->getPopular(5);

    $this->view->artists = $this->artistApi->getNewest();
  }
  
  public function viewAction()
  {
    $params = $this->getRequest()->getParams();
    $artist = $this->artistApi->find($params['id'], true);
    $this->view->artist = $artist;
  }
}