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
  
  private $_appConfig;
  
  function __construct() {
    $this->_appConfig = Zend_Registry::get('Config_App');
    parent::__construct();
  }
  
  public function find($id, $full = false)
  {
    $query = 'select * from artists where id=' . $id;
    $result = $this->_db->fetchAll($query);
    $params = $result[0];
    
    if ($full) {
      // photos
      $params['photos'] = $this->_getPhotos($id);
    
      // also known as
      $params['aka'] = $this->_getAka($id);

        // band members
      $params['members'] = $this->_getMembers($id);

      // member of bands
      $params['projects'] = $this->_getProjects($id);

      // city
      $params['cities'] = $this->_getCities($id);
    } // full
    
    $item = new Model_Artist_Container($params, $full);
    return $item;
  }
  
  private function _getCities($id)
  {
    $query = 'SELECT t1.name AS city FROM cities AS t1, artists AS t2, artist_city_lookup AS t3 ' .
    	   'WHERE (t3.cityid=t1.id AND t3.artistid=t2.id AND t2.id=' . $id . ')';
    $result = $this->_db->fetchAll($query);
    $list = new Jkl_List('Also known as list');
    foreach ($result as $key => $value) {
      $list->add($value);
    }
    return $list;
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
  
  private function _getMainPhoto($id)
  {
    $query = 'SELECT * FROM artists_photos WHERE (artistid=' . $id . ' AND main="y")';
    $result = $this->_db->fetchAll($query);
    $pictures = new Jkl_List('Picture list');
    if (sizeof($result) != 0) {
      foreach ($result as $key => $value) {
        $value['url'] = $this->_appConfig['paths']['artistPhotoPath'] . $value['filename'];
        $pictures->add(new Model_Image_Container($value));
      }
    } else {
      $params['url'] = $this->_appConfig['paths']['artistPhotoPath'] . 'no.png';
      $pictures->add(new Model_Image_Container($params));
    }
    return $pictures;
  }

  private function _getPhotos($id)
  {
    $query = 'SELECT * FROM artists_photos WHERE (artistid=' . $id . ') ORDER BY main';
    $result = $this->_db->fetchAll($query);
    $pictures = new Jkl_List('Picture list');
    if (sizeof($result) != 0) {
      foreach ($result as $key => $value) {
        $value['url'] = $this->_appConfig['paths']['artistPhotoPath'] . $value['filename'];
        $pictures->add(new Model_Image_Container($value));
      }
    } else {
      $params['url'] = $this->_appConfig['paths']['artistPhotoPath'] . 'no.png';
      $pictures->add(new Model_Image_Container($params));
    }
    return $pictures;
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
  
  public function getNewest($num = 20)
  {
    $query = 'SELECT * ' . 
    'FROM artists ' .
    'ORDER by added DESC ' . 
    'LIMIT ' . $num;
    return $this->getList($query);
  }
}