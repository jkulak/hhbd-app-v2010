<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
      $this->view->headMeta()->setName('keywords', 'polski hip-hop, albumy');
      $this->view->headTitle()->headTitle('Hhbd.pl - Hip-hopowa baza danaych', 'SET');
      $this->view->headMeta()->setName('description', 'Albumy w hhbd.pl');
    }

    public function indexAction()
    {
      $this->view->newestList = Model_Album_Api::getInstance()->getNewest(3);
      $this->view->announcedList = Model_Album_Api::getInstance()->getAnnounced(3);
    }
    
    public function aboutAction()
    {
    }
    
    public function contactAction()
    {
    }
}