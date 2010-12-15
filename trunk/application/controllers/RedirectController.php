<?php

class RedirectController extends Zend_Controller_Action
{

  public function init()
  {
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
    $urlName = $this->params['urlName'];
    $type = $this->params['type'];
    
    switch ($type) {
      case 'a':
        $data = Model_Album_Api::getInstance()->redirectFromOld($urlName);
        $redirect = Jkl_Tools_Url::createUrl($data['art_name'] . '-' . $data['alb_title'] . '-a' . $data['alb_id'] . '.html');
        break;
        
      case 'n':
        $data = Model_Artist_Api::getInstance()->redirectFromOld($urlName);
        $redirect = Jkl_Tools_Url::createUrl($data['art_name'] . '-p' . $data['art_id'] . '.html');
        break;
      
      case 'l':
        $data = Model_Label_Api::getInstance()->redirectFromOld($urlName);
        $redirect = Jkl_Tools_Url::createUrl($data['lab_name'] . '-l' . $data['lab_id'] . '.html');
        break;
      
      case 's':
        $data = Model_Song_Api::getInstance()->redirectFromOld($urlName);
        $redirect = Jkl_Tools_Url::createUrl($data['sng_title'] . '-s' . $data['sng_id'] . '.html');
        break;
      
      default:
        break;
    }
    if ($data) {
      header("Location: /" . $redirect, true, 301);
      exit;
    }
    else {
      header("Location: /404.html", true, 404);
      exit();
    }

  }

}