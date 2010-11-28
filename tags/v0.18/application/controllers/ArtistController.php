<?php

class ArtistController extends Zend_Controller_Action
{
  
  public function init()
  {
    $this->view->headMeta()->setName('keywords', 'hhbd.pl, polski hip-hop, albumy');
    $this->view->headTitle()->headTitle('Album');
    $this->view->headMeta()->setName('description', 'Albumy w hhbd.pl');
    
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
    $this->view->artists = Model_Artist_Api::getInstance()->getNewest();
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
  }
}