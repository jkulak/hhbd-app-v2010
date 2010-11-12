<?php
/**
 * Song Api
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 12 November, 2010
 * @package hhbd
 **/

class Model_Song_Api extends Jkl_Model_Api
{

  public function find($id, $full = false)
  {
    $query = 'select * from songs where id=' . $id;
    $result = $this->_db->fetchAll($query);
    $params = $result[0];
    
    $params['featuring'] = $this->getFeaturing($id);
    $params['music'] = $this->getMusic($id);
    $params['scratch'] = $this->getScratch($id);
    $params['artist'] = $this->getArtist($id);
    // getSamples
    // getLyrics

    $item = new Model_Song_Container($params, $full);
    return $item;
  }
  
  private function getFeaturing($id)
  {
    $query = 'SELECT t1.id, t3.feattype ' . 
      'FROM artists t1, feature_lookup t2, feattypes t3 ' . 
      'WHERE (t2.artistid=t1.id AND t3.id=t2.feattype AND t2.songid="' . $id . '") ' . 
      'ORDER BY t1.name';
    
    
    $result = $this->_db->fetchAll($query);
    $featuring = new Jkl_List();
    $artistApi = new Model_Artist_Api();
    foreach ($result as $params) {
      $artist = $artistApi->find($params['id']);
      $artist->featType = $params['feattype'];
      $featuring->add($artist);
      }
    return $featuring;
  }

  private function getMusic($id)
  {
    $query = 'SELECT t1.id ' .
      'FROM artists AS t1, music_lookup AS t2 ' .
      'WHERE (t1.id=t2.artistid AND t2.songid=' . $id . ') ' .
      'ORDER BY t1.name';
    
    $result = $this->_db->fetchAll($query);
    $featuring = new Jkl_List();
    $artistApi = new Model_Artist_Api();
    foreach ($result as $params) {
      $featuring->add($artistApi->find($params['id']));
      }
    return $featuring;
  }
  
  private function getScratch($id)
  {
    $query = 'SELECT t1.id ' .
      'FROM artists AS t1, scratch_lookup AS t2 ' .
      'WHERE (t1.id=t2.artistid AND t2.songid=' . $id . ') ' .
      'ORDER BY t1.name';
    
    $result = $this->_db->fetchAll($query);
    $featuring = new Jkl_List();
    $artistApi = new Model_Artist_Api();
    foreach ($result as $params) {
      $featuring->add($artistApi->find($params['id']));
      }
    return $featuring;
  }

  private function getArtist($id)
  {
    $query = 'SELECT t1.id ' .
      'FROM artists AS t1, artist_lookup AS t2 ' .
      'WHERE (t1.id=t2.artistid AND t2.songid=' . $id . ') ' .
      'ORDER BY t1.name';
    
    $result = $this->_db->fetchAll($query);
    $featuring = new Jkl_List();
    $artistApi = new Model_Artist_Api();
    foreach ($result as $params) {
      $featuring->add($artistApi->find($params['id']));
      }
    return $featuring;
  }
}
