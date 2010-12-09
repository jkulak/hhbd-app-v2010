<?php

class LabelController extends Zend_Controller_Action
{
  
  public function init()
  {
    $this->view->headMeta()->setName('keywords', 'wytwórnia,polski hip-hop,lista,polskie wytwórnie');
    $this->view->headTitle()->headTitle('Lista wytwórni hip-hopowych - Hhbd.pl', 'PREPEND');
    $this->view->headMeta()->setName('description', 'Wytwórnie w hhbd.pl');
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
    $this->view->labels = Model_Label_Api::getInstance()->getFullList();
    $this->view->withMostAlbums = Model_Label_Api::getInstance()->getWithMostAlbums();

    $labelKeywords = '';
    for ($i=0; $i < 5; $i++) { 
      $labelKeywords .= $this->view->withMostAlbums->items[$i]->name . ',';
    }
    
    $this->view->headTitle()->set('Lista wytwórni hip-hopowych - Hhbd.pl');
    $this->view->headMeta()->setName('keywords', $labelKeywords . 'wytwórnia,polski hip-hop,lista,polskie wytwórnie');
    $this->view->headMeta()->setName('description', 'Lista polskich wytwórni, wydających hip-hop, polski hip-hop.');
  }
  
  public function viewAction()
  {
    $params = $this->getRequest()->getParams();
    $label = Model_Label_Api::getInstance()->find($params['id'], true);
    $label->releases = Model_Album_Api::getInstance()->getLabelReleases($label->id, null);
    $this->view->label = $label;
    $this->view->withMostAlbums = Model_Label_Api::getInstance()->getWithMostAlbums();
    
    $artists = array();
    foreach ($label->releases->items as $key => $value) {
      $artists[] = $value->artist->name;
    }
    $artists = array_unique($artists);
    $this->view->label->artists = implode(', ', $artists);
    
    $this->view->headTitle()->set($label->name . ' - Hhbd.pl');
    $this->view->headMeta()->setName('keywords', $label->name . ',' . implode(',', $artists) . ',wytwórnia,polski hip-hop');
    $this->view->headMeta()->setName('description', $label->name . ' to wytwórnia wydająca polski hip-hop, w jej szeregach są tacy artyści jak: ' . implode(', ', $artists));
  }
}