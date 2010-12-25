<?php

class NewsController extends Zend_Controller_Action
{
  
  public function init()
  {
    $this->view->headMeta()->setName('keywords', 'newsy,aktualności,wiadomości,polski,hip-hop');
    $this->view->headTitle()->headTitle('Aktualności', 'PREPEND');
    $this->view->headMeta()->setName('description', 'Najświeższe aktualności ze światka polskiego hip-hopu');
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
  }
  
  public function viewAction()
  {
  }
}