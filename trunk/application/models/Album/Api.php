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
  /**
   * Creates object and fetches the list from database result
   */
  public function getList($query)
  {
    $result = $this->_db->fetchAll($query);;
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
      $params['tracklist'] = $this->getTracklist($id);
      $params['eps'] = $this->getEps($id);
      if (!empty($params['epforid'])) $params['epfor'] = $this->getEpFor($params['epforid']);
      $params['duration'] = $this->getDuration($id);
      $params['votecount'] = $this->getVoteCount($id);
      $params['rating'] = $this->getRating($id);
    }
    $item = new Model_Album_Container($params, $full);
    return $item;
  }
  
  private function getRating($id)
  {
    $query = 'SELECT rating FROM ratings_avg WHERE albumid=' . $id;
    $result = $this->_db->fetchAll($query);
    if (isset($result[0])) {
      $rating = $result[0]['rating'];
    }
    else {
      $rating = '';
    }
    return $rating;
  }

  private function getVoteCount($id)
  {
     $query = 'SELECT COUNT(id) as votecount FROM ratings WHERE albumid="' . $id . '"';
     $result = $this->_db->fetchAll($query);
     return $result[0]['votecount'];
  }
  
  private function getDuration($id)
  {
    $query = 'SELECT sum(t1.length) as duration ' .
      'FROM songs AS t1, album_lookup AS t2 ' .
      'WHERE (t2.songid=t1.id AND t2.albumid=' . $id . ')';
    $result = $this->_db->fetchAll($query);
    if ($result[0]['duration'] > 0) {
      $duration = sprintf( "%02.2d:%02.2d", floor( $result[0]['duration'] / 60 ), $result[0]['duration'] % 60 );
    }
    else
    {
      $duration = 0;
    }
    return $duration;
  }
  
  private function getEpFor($id)
  {
    $albumApi = new Model_Album_Api();
    $epFor = $albumApi->find($id);
    return $epFor;
  }
  
  private function getEps($id)
  {
    $query = 'SELECT id FROM albums WHERE epfor=' . $id . ' ORDER BY year DESC';
    $result = $this->_db->fetchAll($query);
    $eps = new Jkl_List();
    $albumApi = new Model_Album_Api();
    foreach ($result as $params) {  
      $ep = $albumApi->find($params['id']);
      $eps->add($ep);
    }
    return $eps;
  }
  
  private function getTracklist($id)
  {
    $query = 'SELECT t1.id, t2.track ' . 
        'FROM songs AS t1, album_lookup AS t2 ' .
        'WHERE (t1.id=t2.songid AND t2.albumid=' . $id . ') ' . 
        'ORDER BY t2.track';
    $result = $this->_db->fetchAll($query);
    $tracklist = new Jkl_List();
    $songApi = new Model_Song_Api();
    foreach ($result as $params) {  
      $song = $songApi->find($params['id']);
      if (strlen($params['track']) > 2) {
        $song->track = substr($params['track'], 0, 1) . '-' . substr($params['track'], 1, 2);

      }
      else {
        $song->track = $params['track'];
      }
      
      $tracklist->add($song);
    }
    return $tracklist;
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
  
  public function getNewest($count = 20)
  {
    $query = 'SELECT *, t3.id as alb_id, t1.id as art_id, t4.id as lab_id, t3.added as alb_added, t3.addedby as alb_addedby, t3.viewed as alb_viewed ' .
      'FROM artists AS t1, album_artist_lookup AS t2, albums AS t3, labels AS t4 ' .
      'WHERE (t1.id=t2.artistid AND t2.albumid=t3.id AND t4.id=t3.labelid AND t3.year' . '<="' . date('Y-m-d') . '") ' . 
      'ORDER BY t3.year DESC ' . 
      'LIMIT ' . $count;
    return $this->getList($query);
  }
  
  public function getAnnounced($count = 20)
  {
    $query = 'SELECT *, t3.id as alb_id, t1.id as art_id, t4.id as lab_id, t3.added as alb_added, t3.addedby as alb_addedby, t3.viewed as alb_viewed ' .
      'FROM artists AS t1, album_artist_lookup AS t2, albums AS t3, labels AS t4 ' .
      'WHERE (t1.id=t2.artistid AND t2.albumid=t3.id AND t4.id=t3.labelid AND t3.year' . '>"' . date('Y-m-d') . '") ' . 
      'ORDER BY t3.year ASC ' . 
      'LIMIT ' . $count;
    return $this->getList($query);
  }
  
  public function getFirstLetters()
  {
    $query = 'SELECT DISTINCT(SUBSTR(title, 1,1)) as name from albums order by name ASC';
    return  $this->_db->fetchAll($query);
  }
  
  public function getArtistsAlbums($id, $exclude = array(), $count = 10) {
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
      'ORDER BY t3.viewed DESC ' . 
      'LIMIT ' . $count;
    return $this->getList($query);
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
  
  public function increaseViewed($id)
  {
    $query = 'UPDATE albums SET viewed=viewed+1 WHERE id=' . $id;
    $this->_db->query($query);
  }
}