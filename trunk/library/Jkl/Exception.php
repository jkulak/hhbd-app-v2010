<?php

/**
* 
*/
class Jkl_Exception extends Exception
{
  
  function __construct($message = '', $code = null)
  {
    parent::__construct($message, $code);
  }
  
  public function __tostring()
  {
    $str = $this->message;
    $str .= ' exception';
    return $str;
  }
}

?>