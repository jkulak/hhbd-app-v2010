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
    
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
    // $this->view->firstLetters = $this->albumApi->getFirstLetters();
    // $this->view->popularAlbums = $this->albumApi->getPopular(5);

    $this->view->artists = $this->artistApi->getNewest();
  }
  
  public function viewAction()
  {
    $artist = $this->artistApi->find($this->params['id'], true);
    $this->view->artist = $artist;
  }
}