<?php
/**
 * Artist Api
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 12 October, 2010
 * @package hhbd
 **/

class Model_Artist_Api extends Jkl_Model_Api
{
  static private $_instance;
  
  /**
   * Singleton instance
   *
   * @return Model_Artist_Api
   */
  public static function getInstance()
  {
      if (null === self::$_instance) {
          self::$_instance = new self();
      }

      return self::$_instance;
  }
  
  /**
   * Creates object and fetches the list from database result
   */
  public function getList($query)
  {
    $result = $this->_db->fetchAll($query);
    $artists = new Jkl_List();
    
    foreach ($result as $params) {
      $artists->add(new Model_Artist_Container($params));
    }
    return $artists;
  }
  
  public function find($id, $full = false)
  {
    $query = 'select * from artists where id=' . $id;
    $result = $this->_db->fetchAll($query);
    $params = $result[0];
    
    if ($full) {
      // photos
      $params['photos'] = Model_Image_Api::getInstance()->getArtistPhotos($id);
    
      // also known as
      $params['aka'] = $this->_getAka($id);

        // band members
      $params['members'] = $this->_getMembers($id);

      // member of bands
      $params['projects'] = $this->_getProjects($id);

      // city
      $params['cities'] = Model_City_Api::getInstance()->getArtistCities($id);
    } // full
    
    $item = new Model_Artist_Container($params, $full);
    return $item;
  }
  
  private function _getMembers($id)
  {
    $query = 'SELECT t1.name, t1.id, t2.insince, t2.awaysince FROM artists AS t1, band_lookup AS t2 ' .
    	   'WHERE (t1.id=t2.artistid AND t2.bandid=' . $id . ') ORDER BY t1.name';
    return $this->getList($query);
  }
  
  private function _getProjects($id)
  {
    $query = 'SELECT t1.name, t1.id, t2.insince AS since, t2.awaysince AS till FROM artists AS t1, band_lookup AS t2 ' .
    	   'WHERE (t1.id=t2.bandid AND t2.artistid=' . $id . ') ORDER BY t1.name';
    return $this->getList($query);
  }
  
  private function _getAka($id)
  {
    $query = 'SELECT t1.altname FROM altnames_lookup AS t1 ' .
              'WHERE (t1.artistid=' . $id . ') ORDER BY t1.altname';
    $result = $this->_db->fetchAll($query);
    $aka = new Jkl_List('Also known as list');
    foreach ($result as $key => $value) {
      $aka->add($value['altname']);
    }
    return $aka;
  }
  
  public function getNewest($num = 20)
  {
    $query = 'SELECT * ' . 
    'FROM artists ' .
    'ORDER by added DESC ' . 
    'LIMIT ' . $num;
    return $this->getList($query);
  }
  
  public function getSongFeaturing($id)
  {
    $query = 'SELECT t1.id, t3.feattype ' . 
      'FROM artists t1, feature_lookup t2, feattypes t3 ' . 
      'WHERE (t2.artistid=t1.id AND t3.id=t2.feattype AND t2.songid="' . $id . '") ' . 
      'ORDER BY t1.name';
    $result = $this->_db->fetchAll($query);
    $featuring = new Jkl_List();
    foreach ($result as $params) {
      $artist = $this->find($params['id']);
      $artist->featType = $params['feattype'];
      $featuring->add($artist);
      }
      
    return $featuring;
  }

  public function getSongMusic($id)
  {
    $query = 'SELECT * ' .
      'FROM artists AS t1, music_lookup AS t2 ' .
      'WHERE (t1.id=t2.artistid AND t2.songid=' . $id . ') ' .
      'ORDER BY t1.name';

    return $this->getList($query);
  }
  
  public function getSongScratch($id)
  {
    $query = 'SELECT * ' .
      'FROM artists AS t1, scratch_lookup AS t2 ' .
      'WHERE (t1.id=t2.artistid AND t2.songid=' . $id . ') ' .
      'ORDER BY t1.name';
    
   return $this->getList($query);
  }

  public function getSongArtist($id)
  {
    $query = 'SELECT * ' .
      'FROM artists AS t1, artist_lookup AS t2 ' .
      'WHERE (t1.id=t2.artistid AND t2.songid=' . $id . ') ' .
      'ORDER BY t1.name';
    
   return $this->getList($query);
  }
}