<?php

class ArtistController extends Zend_Controller_Action
{

  public function init()
  {
    $this->view->headMeta()->setName('keywords', 'hhbd.pl, polski hip-hop, albumy');
    $this->view->headMeta()->setName('description', 'Albumy w hhbd.pl');

    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
    $this->view->firstLetters = Model_Artist_Api::getInstance()->getFirstLetters();

    $page = (!empty($this->params['letter']))?$this->params['letter']:'mostPopular';

    if ($page == 'mostPopular') {
      $this->view->artists = Model_Artist_Api::getInstance()->getMostPopular();
    }
    else {
      $this->view->artists = Model_Artist_Api::getInstance()->getLike($page . '%');
    }
    
    $this->view->mostProjectAlbums = Model_Artist_Api::getInstance()->getWithMostProjectAlbums();
    $this->view->mostSoloAlbums = Model_Artist_Api::getInstance()->getWithMostSoloAlbums();
    
    

    $this->view->title = 'Lista wykonawców';
    $this->view->headTitle()->headTitle('Lista polskich wykonawców hip-hop', 'PREPEND');
    $this->view->headMeta()->setName('description', 'Lista polskich wykonawców hip-hop');
    $this->view->headMeta()->setName('keywords', 'polski hip-hop,wykonawcy');    
  }

  public function viewAction()
  {
    $artist = Model_Artist_Api::getInstance()->find($this->params['id'], true);

    $artist->addAlbums(Model_Album_Api::getInstance()->getArtistsAlbums($artist->id, array(), false, 'year'));

    if (!empty($artist->projects->items)) {
      $projectAlbums = new Jkl_List('Projects list');
      $temp = new Jkl_List('Temp');
      foreach ($artist->projects->items as $key => $value) {
        $temp = Model_Album_Api::getInstance()->getArtistsAlbums($value->id, array(), false, 'year');
        $projectAlbums->items = array_merge($temp->items, $projectAlbums->items);
      }
      $artist->addProjectAlbums($projectAlbums);
    }

    $artist->addFeaturing(Model_Album_Api::getInstance()->getFeaturingByArtist($artist->id, null));
    $artist->addMusic(Model_Album_Api::getInstance()->getMusicByArtist($artist->id, null));
    $artist->addScratch(Model_Album_Api::getInstance()->getScratchByArtist($artist->id, null));
    
    $artist->addPopularSongs(Model_Song_Api::getInstance()->getMostPopularByArtist($artist->id, 10));
      
    $this->view->artist = $artist;
    
    // seo
    $albumListTmp = array();
    foreach ($artist->albums->items as $key => $value) {
      $albumListTmp[] = $value->title;
    }
    if (!empty($artist->projectAlbums)) {
      foreach ($artist->projectAlbums->items as $key => $value) {
        $albumListTmp[] = $value->title;
      }
    }
    
    $albumList = array();
    for ($i=0; $i < 3; $i++) { 
      if (isset($albumListTmp[$i])) {
        $albumList[] = $albumListTmp[$i];
      }
    }
    
    $this->view->headTitle()->headTitle($artist->name . ' - biografia, dyskografia, albumy, teksty', 'PREPEND');
    $this->view->headMeta()->setName('description', $artist->name . ' - teksty, dyskografia, albumy '. implode($albumList, ', '));
    $this->view->headMeta()->setName('keywords', $artist->name . ',' . implode(',', $albumListTmp) . ',teksty,dyskografia,albumy' );
  }
}