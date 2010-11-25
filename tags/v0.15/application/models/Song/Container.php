<?php

/**
 * Song
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 12 November, 2010
 * @package default
 **/

class Model_Song_Container
{
  function __construct($params, $full = false)
  {
    
    // print_r($params);
    
    $this->id = $params['id'];
    $this->title = $params['title'];
    
    $this->track = (!empty($params['track'])?$params['track']:null);
    if (empty($params['length'])) {
      $this->duration = '--:--';
    }
    else {
      $this->duration = sprintf( "%02.2d:%02.2d", floor( $params['length'] / 60 ), $params['length'] % 60 );
    }
    $this->bpm = $params['bpm'];
    
    if (!empty($params['featuring'])) {
      $this->featuring = $params['featuring'];
    }
    
    if (!empty($params['music'])) {
      $this->music = $params['music'];
    }
    
    if (!empty($params['scratch'])) {
      $this->scratch = $params['scratch'];
    }
    
    if (!empty($params['artist'])) {
      $this->artist = $params['artist'];
    }
  }
}