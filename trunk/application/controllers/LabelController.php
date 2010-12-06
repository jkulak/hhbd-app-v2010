<?php

class LabelController extends Zend_Controller_Action
{
  
  public function init()
  {
  }

  public function indexAction()
  {
    $this->view->labels = Model_Label_Api::getInstance()->getFullList();
  }
  
  public function viewAction()
  {
    $params = $this->getRequest()->getParams();
    $label = Model_Label_Api::getInstance()->find($params['id'], true);
    $label->releases = Model_Album_Api::getInstance()->getLabelReleases($label->id, null);    
    $this->view->label = $label;
  }
}