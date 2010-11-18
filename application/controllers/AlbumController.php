<?php

class AlbumController extends Zend_Controller_Action
{

  private $albumApi;
  
  public function init()
  {
    $this->albumApi = new Model_Album_Api();
    
    $this->view->headMeta()->setName('keywords', 'hhbd.pl, polski hip-hop, albumy');
    $this->view->headTitle()->headTitle('Album');
    $this->view->headMeta()->setName('description', 'Albumy w hhbd.pl');
  }

  public function indexAction()
  {
    $this->view->firstLetters = $this->albumApi->getFirstLetters();
    $this->view->popularAlbums = $this->albumApi->getPopular(5);
    
    $this->view->albums = $this->albumApi->getNewest();
    
    $this->view->subTitle = 'Najnowsze albumy';
    $this->view->headTitle($this->view->subTitle, 'PREPEND');
    $this->view->headMeta()->setName('description', 'Lista ostatnio wydanych albumów z polskim hip-hopem');
  }
    
  public function announcedAction()
  {
    $this->view->firstLetters = $this->albumApi->getFirstLetters();
    $this->view->popularAlbums = $this->albumApi->getPopular(5);
    
    $this->view->albums = $this->albumApi->getAnnounced();
    
    $this->view->subTitle = 'Albumy zapowiedziane';
    $this->view->headTitle($this->view->subTitle, 'PREPEND');
    $this->view->headMeta()->setName('keywords', 'zapowiedzi, polski hip-hop, najbliższe premiery');
    $this->view->headMeta()->setName('description', 'Lista zapowiedzianych albumów z polskim hip-hopem. Sprawdź najbliższe premiery.');
    
    $this->renderScript('album/index.phtml');
  }
  
  public function firstletterAction()
  {
    $this->view->firstLetters = $this->albumApi->getFirstLetters();
    $this->view->popularAlbums = $this->albumApi->getPopular(5);

    $params = $this->getRequest()->getParams();    
    $this->view->albums = $this->albumApi->getLike($params['letter'] . '%');
    
    $this->view->subTitle = 'Albumy zaczynające się na [' . $params['letter'] . ']';
    $this->view->headTitle($this->view->subTitle, 'PREPEND');
    $this->view->headMeta()->setName('keywords', 'albumy, polski hip-hop, najbliższe premiery');
    $this->view->headMeta()->setName('description', 'Lista albumów z polskim hip-hopem. Sprawdź najbliższe premiery.');
    
    $this->renderScript('album/index.phtml');
  }
  
  public function viewAction()
  {
    $params = $this->getRequest()->getParams();
    // $album = $this->albumApi->find($params['id'], true);
    // $this->view->album = $album;
    
    // $this->view->popularAlbums = $this->albumApi->getPopular(10);
    $this->view->bestAlbums = $this->albumApi->getBest(10);
    
    $this->view->headTitle()->set($album->artist->name . ' - ' . $album->title . ' | HHBD.PL');
    $this->view->headMeta()->setName('keywords', $album->artist->name . ', ' . $album->title . ', download, tekst, wrzuta, chomikuj');
    $this->view->headMeta()->setName('description', 'Lista utworów, okładka, linki oraz inne szczegółowe informacje o albumie: ' . $album->artist->name . ' - ' . $album->title . ' na największej polskiej stroni o polskim hip-hopie.');
  }
}