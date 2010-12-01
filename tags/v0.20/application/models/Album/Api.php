<?php
/**
 * Album Api
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 12 October, 2010
 * @package hhbd
 **/
 
// extends Api
class Model_Album_Api extends Jkl_Model_Api
{
  
  static private $_instance;
  
  /**
   * Singleton instance
   *
   * @return Model_Album_Api
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
    $albums = new Jkl_List(); 
    foreach ($result as $params) {
      $albums->add(new Model_Album_Container($params));
    }
    return $albums;
  }
  
  public function find($id, $full = false)
  {
    $query = "SELECT *, t1.id as alb_id, t1.labelid AS lab_id, t1.epfor as epforid, t1.added as alb_added, t1.addedby as alb_addedby, t1.viewed as alb_viewed, t3.id as art_id " . 
    "FROM albums t1, album_artist_lookup t2, artists t3 " . 
    "WHERE (t3.id=t2.artistid AND t2.albumid=t1.id AND t1.id='" . $id . "')";
    $result = $this->_db->fetchAll($query);
    $params = $result[0];    
    if ($full) { 
      $params['tracklist'] = Model_Song_Api::getInstance()->getTracklist($id);
      $params['eps'] = $this->getEps($id);
      if (!empty($params['epforid'])) $params['epfor'] = $this->getEpFor($params['epforid']);
      $params['duration'] = Model_Song_Api::getInstance()->getAlbumDuration($id);
      $params['votecount'] = Model_Rating_Api::getInstance()->getAlbumVoteCount($id);
      $params['rating'] = Model_Rating_Api::getInstance()->getAlbumRating($id);
    }
    $item = new Model_Album_Container($params, $full);
    return $item;
  }

  private function getEpFor($id)
  {
    return Model_Album_Api::getInstance()->find($id);
  }
  
  private function getEps($id)
  {
    $query = 'SELECT id FROM albums WHERE epfor=' . $id . ' ORDER BY year DESC';
    $result = $this->_db->fetchAll($query);
    $eps = new Jkl_List();
    $albumApi = Model_Album_Api::getInstance();
    foreach ($result as $params) {  
      $ep = $albumApi->find($params['id']);
      $eps->add($ep);
    }
    return $eps;
  }

  public function getLike($like = '')
  {
    $query = 'SELECT *, t3.id as alb_id, t1.id as art_id, t4.id as lab_id, t3.added as alb_added, t3.addedby as alb_addedby, t3.viewed as alb_viewed ' .
      'FROM artists AS t1, album_artist_lookup AS t2, albums AS t3, labels AS t4 ' .
      'WHERE (t3.title LIKE "' . $like . '" AND t1.id=t2.artistid AND t2.albumid=t3.id AND t4.id=t3.labelid AND t3.year' . '<="' . date('Y-m-d') . '") ' . 
      'ORDER BY t3.year DESC';
    return $this->getList($query);
  }
  
  public function getPopular($count = 20)
  {
    $query = 'SELECT *, t3.id as alb_id, t1.id as art_id, t4.id as lab_id, t3.added as alb_added, t3.addedby as alb_addedby, t3.viewed as alb_viewed ' .
      'FROM artists AS t1, album_artist_lookup AS t2, albums AS t3, labels AS t4 ' .
      'WHERE (t1.id=t2.artistid AND t2.albumid=t3.id AND t4.id=t3.labelid AND t3.year' . '<="' . date('Y-m-d') . '") ' . 
      'ORDER BY t3.viewed DESC ' . 
      'LIMIT ' . $count;
    return $this->getList($query);
  }
  
  public function getBest($count = 10)
  {
    $query = 'SELECT *, t1.id AS alb_id, t2.rating AS rating, t1.title, t3.artistid AS art_id ' . 
      'FROM albums t1, ratings_avg t2, album_artist_lookup t3 ' . 
      ' WHERE (t1.id=t2.albumid AND t3.albumid=t1.id) ' . 
      'ORDER BY t2.rating DESC ' .
      'LIMIT ' . $count;
    return $this->getList($query);
  }
  
  public function getNewest($count = 20, $page = 1)
  {
    $page = (int)$page - 1;
    $query = 'SELECT *, t3.id as alb_id, t1.id as art_id, t4.id as lab_id, t3.added as alb_added, t3.addedby as alb_addedby, t3.viewed as alb_viewed ' .
      'FROM artists AS t1, album_artist_lookup AS t2, albums AS t3, labels AS t4 ' .
      'WHERE (t1.id=t2.artistid AND t2.albumid=t3.id AND t4.id=t3.labelid AND t3.year' . '<="' . date('Y-m-d') . '") ' . 
      'ORDER BY t3.year DESC ' . 
      'LIMIT ' . $count . ' ' . 
      'OFFSET ' . ($page*$count);
    return $this->getList($query);
  }
  
  public function getAnnounced($count = 20, $page = 1)
  {
    $page = (int)$page - 1;
    $query = 'SELECT *, t3.id as alb_id, t1.id as art_id, t4.id as lab_id, t3.added as alb_added, t3.addedby as alb_addedby, t3.viewed as alb_viewed ' .
      'FROM artists AS t1, album_artist_lookup AS t2, albums AS t3, labels AS t4 ' .
      'WHERE (t1.id=t2.artistid AND t2.albumid=t3.id AND t4.id=t3.labelid AND t3.year' . '>"' . date('Y-m-d') . '") ' . 
      'ORDER BY t3.year ASC ' . 
      'LIMIT ' . $count . ' ' . 
      'OFFSET ' . ($page*$count);
    return $this->getList($query);
  }
  
  public function getFirstLetters()
  {
    $query = 'SELECT DISTINCT(SUBSTR(title, 1,1)) as name from albums order by name ASC';
    return  $this->_db->fetchAll($query);
  }
  
  public function getArtistsAlbums($id, $exclude = array(), $count = 10, $order = 'viewed') {
    $excludeCondition = '';
    if (!empty($exclude)) {
      foreach ($exclude as $key => $value) {
        $excludeCondition .= ' AND t3.id<>' . $value . ' ';
      }
    }    
    $query = 'SELECT *, t3.id as alb_id, t1.id as art_id, t4.id as lab_id, t3.added as alb_added, t3.addedby as alb_addedby, t3.viewed as alb_viewed ' .
      'FROM artists AS t1, album_artist_lookup AS t2, albums AS t3, labels AS t4 ' .
      'WHERE (t1.id=t2.artistid AND t2.albumid=t3.id AND t4.id=t3.labelid AND t1.id="' . $id . '"' . 
      $excludeCondition . 
      ') ' . 
      'ORDER BY t3.' . $order . ' DESC ' . 
      (($count)?'LIMIT ' . $count:'');
    return self::getList($query);
  }
  
  public function getLabelsAlbums($id, $exclude = array(), $count = 10) {
    $excludeCondition = '';
    if (!empty($exclude)) {
      foreach ($exclude as $key => $value) {
        $excludeCondition .= ' AND t3.id<>' . $value . ' ';
      }
    }
    $query = 'SELECT *, t3.id as alb_id, t1.id as art_id, t4.id as lab_id, t3.added as alb_added, t3.addedby as alb_addedby, t3.viewed as alb_viewed ' .
      'FROM artists AS t1, album_artist_lookup AS t2, albums AS t3, labels AS t4 ' .
      'WHERE (t1.id=t2.artistid AND t2.albumid=t3.id AND t4.id=t3.labelid AND t4.id="' . $id . '"' . 
      $excludeCondition . 
      ') ' . 
      'ORDER BY t3.viewed DESC ' . 
      'LIMIT ' . $count;
    return $this->getList($query);
  }
  
  // This needs to be moved to separate counting system, not to kill datbase
  public function increaseViewed($id)
  {
    $query = 'UPDATE albums SET viewed=viewed+1 WHERE id=' . $id;
    $this->_db->query($query);
  }
  
  public function getAlbumCount()
  {
    $query = 'SELECT count(id) as albumcount FROM albums WHERE (year<="' . date('Y-m-d') . '")';
    $result = $this->_db->fetchAll($query);
    return (int)$result[0]['albumcount'];    
  }

  public function getAnnouncedCount()
  {
    $query = 'SELECT count(id) as albumcount FROM albums WHERE (year>"' . date('Y-m-d') . '")';
    $result = $this->_db->fetchAll($query);
    return (int)$result[0]['albumcount'];    
  }
  
  public function getFeaturingByArtist($id, $limit = 10)
  {
    $query = 'SELECT DISTINCT(a1.id) as alb_id
              FROM albums a1, songs a2, feature_lookup a3, album_lookup a4
              WHERE (a3.artistid=' . $id . ' AND a3.songid=a2.id AND a2.id=a4.songid AND a1.id=a4.albumid)';
    $albumIds = $this->_db->fetchAll($query);
    if (empty($albumIds)) {
      return false;
    }
    
    $condition = array();
    
    foreach ($albumIds as $key => $value) {
      $condition[] = 't3.albumid=' . $value['alb_id'];
    }
    
    $query = 'SELECT *, t1.id AS alb_id, t2.id AS art_id
              FROM albums t1, artists t2, album_artist_lookup t3
              WHERE (t2.id=t3.artistid AND t1.id=t3.albumid AND (
              ' . implode($condition, ' OR ') . ')
              )' . 
              (($limit != null)?' LIMIT ' . $limit:'');

    return $this->getList($query);
  }
  
  public function getMusicByArtist($id, $limit = 10)
  {
    $query = 'SELECT DISTINCT(a1.id) as alb_id
              FROM albums a1, songs a2, music_lookup a3, album_lookup a4
              WHERE (a3.artistid=' . $id . ' AND a3.songid=a2.id AND a2.id=a4.songid AND a1.id=a4.albumid)';
    $albumIds = $this->_db->fetchAll($query);
    if (empty($albumIds)) {
      return false;
    }
    
    $condition = array();
    
    foreach ($albumIds as $key => $value) {
      $condition[] = 't3.albumid=' . $value['alb_id'];
    }
    
    $query = 'SELECT *, t1.id AS alb_id, t2.id AS art_id
              FROM albums t1, artists t2, album_artist_lookup t3
              WHERE (t2.id=t3.artistid AND t1.id=t3.albumid AND (
              ' . implode($condition, ' OR ') . ')
              )' . 
              (($limit != null)?' LIMIT ' . $limit:'');

    return $this->getList($query);
  }
  
  public function getScratchByArtist($id, $limit = 10)
  {
    $query = 'SELECT DISTINCT(a1.id) as alb_id
              FROM albums a1, songs a2, scratch_lookup a3, album_lookup a4
              WHERE (a3.artistid=' . $id . ' AND a3.songid=a2.id AND a2.id=a4.songid AND a1.id=a4.albumid)';
    $albumIds = $this->_db->fetchAll($query);
    if (empty($albumIds)) {
      return false;
    }
    
    $condition = array();
    
    foreach ($albumIds as $key => $value) {
      $condition[] = 't3.albumid=' . $value['alb_id'];
    }
    
    $query = 'SELECT *, t1.id AS alb_id, t2.id AS art_id
              FROM albums t1, artists t2, album_artist_lookup t3
              WHERE (t2.id=t3.artistid AND t1.id=t3.albumid AND (
              ' . implode($condition, ' OR ') . ')
              )' . 
              (($limit != null)?' LIMIT ' . $limit:'');

    return $this->getList($query);
  }
  
  // List of albums that feature a song with given ID
  public function getSongAlbums($id, $limit)
  {
    $query = "SELECT *, t1.id as alb_id, t3.id as art_id  
              FROM albums t1, album_lookup t2, artists t3, album_artist_lookup t4 
              WHERE (t1.id=t2.albumid AND t2.songid='$id' AND t4.albumid=t1.id AND t4.artistid=t3.id)
              " .
              (($limit != null)?' LIMIT ' . $limit:'');
    return $this->getList($query);
  }
}