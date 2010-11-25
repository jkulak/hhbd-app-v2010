<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
      $this->view->newestList = Model_Album_Api::getInstance()->getNewest(3);
      $this->view->announcedList = Model_Album_Api::getInstance()->getAnnounced(3);
    }
}