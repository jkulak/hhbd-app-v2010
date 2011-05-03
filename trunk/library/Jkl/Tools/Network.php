<?php
/**
 * Network
 *
 * @author Kuba
 * @version $Id$
 * @copyright __MyCompanyName__, 2 May, 2011
 * @package Tools
 **/
 
class Jkl_Tools_Network
{
  
  function __construct()
  {
    # code...
  }
  
  public static function getIps()
  {
    // $string = urlencode($string);
    $result = array();

    if (!empty($_SERVER['REMOTE_ADDR']))
    {
      $result[] = $_SERVER['REMOTE_ADDR'];
    }

    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
      $result[] = $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $result[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    if (!empty($_SERVER['HTTP_VIA']))
    {
      $result[] = $_SERVER['HTTP_VIA'];
    }

    return join("|", $result);
  }
}
