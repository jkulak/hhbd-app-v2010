<?php

class AlbumController extends Zend_Controller_Action
{

  private $albumApi;
  
  public function init()
  {
    $this->albumApi = new Model_Album_Api();
    
    $this->view->headMeta()->setName('keywords', 'polski hip-hop, albumy');
    $this->view->headTitle()->headTitle('Albumy', 'PREPEND');
    $this->view->headMeta()->setName('description', 'Albumy w hhbd.pl');
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
    $page = (!empty($this->params['page']))?$this->params['page']:1;
    
    $this->view->firstLetters = $this->albumApi->getFirstLetters();
    $this->view->popularAlbums = $this->albumApi->getPopular(10);
    $this->view->bestAlbums = $this->albumApi->getBest(10);
    $this->view->albums = $this->albumApi->getNewest(12, $page);
    $albumCount =  $this->albumApi->getAlbumCount();
    
    // pagination
    $paginator = Zend_Paginator::factory($albumCount);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(12);
    $paginator->setPageRange(15);
    Zend_View_Helper_PaginationControl::setDefaultViewPartial('common/_paginatorTable.phtml');
    $this->view->paginator = $paginator;
    
    // seo
    $this->view->title = 'Lista polskich albumów hip-hopowych';
    $this->view->headTitle($this->view->title, 'PREPEND');    
    
    $keywords = array();
    $description = array();
    foreach ($this->view->albums->items as $key => $value) {
      $keywords[] = $value->artist->name;
      $description[] = $value->title;
    }
    
    $this->view->headMeta()->setName('keywords', 'lista albumów, ' . implode(array_unique($keywords), ', '));
    $this->view->headMeta()->setName('description', 'Lista wydanych w polsce albumów hip-hopowych, ' . implode(array_unique($description), ', '));
  }
    
  public function announcedAction()
  {
    $page = (!empty($this->params['page']))?$this->params['page']:1;
    
    $this->view->popularAlbums = $this->albumApi->getPopular(10);
    $this->view->bestAlbums = $this->albumApi->getBest(10);
    
    $albumCount =  $this->albumApi->getAnnouncedCount();
    $this->view->albums = $this->albumApi->getAnnounced(12, $page);
    
    $paginator = Zend_Paginator::factory($albumCount);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(12);
    $paginator->setPageRange(15);
    Zend_View_Helper_PaginationControl::setDefaultViewPartial('common/_paginatorTable.phtml');
    $this->view->paginator = $paginator;
    
    $this->view->title = 'Albumy zapowiedziane';
    $this->view->headTitle($this->view->title, 'PREPEND');
    
    $keywords = array();
    $description = array();
    foreach ($this->view->albums->items as $key => $value) {
      $keywords[] = $value->artist->name;
      $description[] = $value->title;
    }
    
    $this->view->headMeta()->setName('keywords', 'lista albumów, ' . implode(array_unique($keywords), ', '));
    $this->view->headMeta()->setName('description', 'Lista zapowiedzianych w polsce albumów hip-hopowych, ' . implode(array_unique($description), ', ') . '. Sprawdź najbliższe premiery!');
    
    $this->renderScript('album/index.phtml');
  }
  
  public function firstletterAction()
  {
    $this->view->firstLetters = $this->albumApi->getFirstLetters();
    $this->view->popularAlbums = $this->albumApi->getPopular(5);
 
    $this->view->albums = $this->albumApi->getLike($this->params['letter'] . '%');
    
    $this->view->subTitle = 'Albumy zaczynające się na [' . $params['letter'] . ']';
    $this->view->headTitle($this->view->subTitle, 'PREPEND');
    $this->view->headMeta()->setName('keywords', 'albumy, polski hip-hop, najbliższe premiery');
    $this->view->headMeta()->setName('description', 'Lista albumów z polskim hip-hopem. Sprawdź najbliższe premiery.');
    
    $this->renderScript('album/index.phtml');
  }
  
  public function viewAction()
  {
    $params = $this->getRequest()->getParams();
    $album = $this->albumApi->find($params['id'], true);
    $this->view->album = $album;
    $this->albumApi->increaseViewed($album->id);
    
    $this->view->artistsAlbums = $this->albumApi->getArtistsAlbums($album->artist->id, array($album->id), 10);
    $this->view->popularAlbums = $this->albumApi->getPopular(10);
    $this->view->bestAlbums = $this->albumApi->getBest(10);
    if (!empty($album->label)) {
      $this->view->labelsAlbums = $this->albumApi->getLabelsAlbums($album->label->id, array($album->id), 10);
    }
    
    $this->view->currentUrl = $this->getRequest()->getBaseUrl() . $this->getRequest()->getRequestUri();
    
    $this->view->title = $album->artist->name . ' - ' . $album->title;
    $this->view->headTitle()->set($this->view->title, 'PREPEND');
    $this->view->headMeta()->setName('keywords', $album->artist->name . ', ' . $album->title . ', download, teksty, hip-hop, polski hip hop');
    $this->view->headMeta()->setName('description', 'Lista utworów, okładka, linki oraz inne szczegółowe informacje o albumie: ' . $album->artist->name . ' - ' . $album->title . ' na największej polskiej stronie o polskim hip-hopie.');
  }
}