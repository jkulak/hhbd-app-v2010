<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
      $albumApi = new Model_Album_Api();
      $this->view->newestList = $albumApi->getNewest(3);
      $this->view->announcedList = $albumApi->getAnnounced(3);
    }
}