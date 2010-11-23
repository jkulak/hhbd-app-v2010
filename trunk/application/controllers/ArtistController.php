<?php

class ArtistController extends Zend_Controller_Action
{

  private $artistApi;
  
  public function init()
  {
    $this->artistApi = Model_Artist_Api::getInstance();
    
    $this->view->headMeta()->setName('keywords', 'hhbd.pl, polski hip-hop, albumy');
    $this->view->headTitle()->headTitle('Album');
    $this->view->headMeta()->setName('description', 'Albumy w hhbd.pl');
    
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
    $this->view->artists = $this->artistApi->getNewest();
  }
  
  public function viewAction()
  {
    $artist = $this->artistApi->find($this->params['id'], true);
    
    $albumApi = Model_Album_Api::getInstance();
    $artist->addAlbums($albumApi->getArtistsAlbums($artist->id, array(), false, 'year'));
    
    if (!empty($artist->projects->items)) {
      $projectAlbums = new Jkl_List('Projects list');
      $temp = new Jkl_List('Temp');
      foreach ($artist->projects->items as $key => $value) {
        $temp = $albumApi->getArtistsAlbums($value->id, array(), false, 'year');
        $projectAlbums->items = array_merge($temp->items, $projectAlbums->items);
      }
      $artist->addProjectAlbums($projectAlbums);
    }
    
    $artist->addFeaturing($albumApi->getFeaturingByArtist($artist->id, 10000));
    $artist->addMusic($albumApi->getMusicByArtist($artist->id, 10000));
    $artist->addScratch($albumApi->getScratchByArtist($artist->id, 10000));
    
    $songApi = Model_Song_Api::getInstance();
    $artist->addPopularSongs($songApi->getMostPopularByArtist($artist->id, 10));    
      
    $this->view->artist = $artist;
  }
}