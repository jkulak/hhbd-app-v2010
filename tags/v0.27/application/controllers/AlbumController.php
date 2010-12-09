<?php

class AlbumController extends Zend_Controller_Action
{
  
  public function init()
  {
    $this->view->headMeta()->setName('keywords', 'polski hip-hop, albumy');
    $this->view->headTitle()->headTitle('Albumy', 'PREPEND');
    $this->view->headMeta()->setName('description', 'Albumy w hhbd.pl');
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
    $page = (!empty($this->params['page']))?$this->params['page']:1;
    
    $this->view->popularAlbums = Model_Album_Api::getInstance()->getPopular(10);
    $this->view->bestAlbums = Model_Album_Api::getInstance()->getBest(10);
    $this->view->albums = Model_Album_Api::getInstance()->getNewest(12, $page);
    $albumCount =  Model_Album_Api::getInstance()->getAlbumCount();

    // pagination
    if ($albumCount > 12) {
      $paginator = Zend_Paginator::factory($albumCount);
      $paginator->setCurrentPageNumber($page);
      $paginator->setItemCountPerPage(12);
      $paginator->setPageRange(15);
      Zend_View_Helper_PaginationControl::setDefaultViewPartial('common/_paginatorTable.phtml');
      $this->view->paginator = $paginator;
    }

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
    $this->view->title = 'Albumy zapowiedziane';
    $this->view->headTitle($this->view->title, 'PREPEND');
    
    $page = (!empty($this->params['page']))?$this->params['page']:1;
    
    $this->view->popularAlbums = Model_Album_Api::getInstance()->getPopular(10);
    $this->view->bestAlbums = Model_Album_Api::getInstance()->getBest(10);
    
    $albumCount =  Model_Album_Api::getInstance()->getAnnouncedCount();
    $this->view->albums = Model_Album_Api::getInstance()->getAnnounced(12, $page);
    
    if ($albumCount > 12) {
      $paginator = Zend_Paginator::factory($albumCount);
      $paginator->setCurrentPageNumber($page);
      $paginator->setItemCountPerPage(12);
      $paginator->setPageRange(15);
      Zend_View_Helper_PaginationControl::setDefaultViewPartial('common/_paginatorTable.phtml');
      $this->view->paginator = $paginator;
    }
    
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

  public function viewAction()
  {
    $params = $this->getRequest()->getParams();
    $album = Model_Album_Api::getInstance()->find($params['id'], true);
    $this->view->album = $album;
    $this->view->artistsAlbums = Model_Album_Api::getInstance()->getArtistsAlbums($album->artist->id, array($album->id), 10);
    $this->view->popularAlbums = Model_Album_Api::getInstance()->getPopular(10);
    $this->view->bestAlbums = Model_Album_Api::getInstance()->getBest(10);
    if (!empty($album->label)) {
      $this->view->labelsAlbums = Model_Album_Api::getInstance()->getLabelsAlbums($album->label->id, array($album->id), 10);
    }

    $this->view->currentUrl = $this->getRequest()->getBaseUrl() . $this->getRequest()->getRequestUri();

    $this->view->title = $album->artist->name . ' - ' . $album->title;
    $this->view->headTitle()->set($this->view->title, 'PREPEND');
    $this->view->headMeta()->setName('keywords', $album->artist->name . ',' . $album->title . ',teksty,download,teksty,premiera,hip-hop,polski hip hop');
    $this->view->headMeta()->setName('description', $album->artist->name . ' "' . $album->title . '" lista utworów, okładka, teksty, słowa piosenek, premiera, oraz inne szczegółowe informacje o albumie na największej polskiej stronie o polskim hip-hopie.');
  }
}