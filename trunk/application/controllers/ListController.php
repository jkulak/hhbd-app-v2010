<?php

class ListController extends Zend_Controller_Action
{
  
  public function init()
  { 
    // $ajaxContext = $this->_helper->getHelper('AjaxContext');
    // $ajaxContext->addActionContext('addtocollection', 'json')->initContext();
    
    // $contextSwitch = $this->_helper->getHelper('contextSwitch');
    //         $contextSwitch->addActionContext('addtocollection', 'json')
    //                       ->initContext();
                          
    // $this->_helper->ajaxContext()->addActionContext('addtocollection', 'json')->initContext('json');
    
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    
    $ajaxContext->addActionContext('add-to-collection', 'json');
    $ajaxContext->addActionContext('add-to-wishlist', 'json');
    $ajaxContext->addActionContext('remove-from-collection', 'json');
    $ajaxContext->addActionContext('remove-from-wishlist', 'json');
    
    $ajaxContext->initContext('json');
    
    $this->_request = $this->getRequest();
    $this->_requestParams = $this->_request->getParams();
    
    if (!$this->_request->isXmlHttpRequest()) {
        $this->view->album = Model_Album_Api::getInstance()->find($this->_requestParams['id']);
    }
  }

  public function indexAction()
  {
  }
  
  /**
   * Adds album to users collection
   *
   * @return void
   * @since 2011-05-03
   * @author Kuba
   * @file: ListController.php
   **/
  public function addToCollectionAction()
  {
    $this->view->test = 'yes';
    $this->_addToList(Model_List::TYPE_COLLECTION);
  }
  
  /**
   * Adds album to users wishlist
   *
   * @return void
   * @since 2011-05-03
   * @author Kuba
   * @file: ListController.php
   **/
  public function addToWishlistAction()
  {
    $this->_addToList(Model_List::TYPE_WISHLIST);
  }
  
  /**
   * Removes album from users collection
   *
   * @return void
   * @since 2011-05-03
   * @author Kuba
   * @file: ListController.php
   **/
  public function removeFromCollectionAction()
  {
    $this->_removeFromList(Model_List::TYPE_COLLECTION);
  }
  
  /**
   * Removes album from users wishlist
   *
   * @return void
   * @since 2011-05-03
   * @author Kuba
   * @file: ListController.php
   **/
  public function removeFromWishlistAction()
  {
    $this->_removeFromList(Model_List::TYPE_WISHLIST);
  }
  
  /**
   * Add album to users list
   *
   * @return boolean
   * @since 2011-05-03
   * @author Kuba
   * @file: ListController.php
   **/
  private function _addToList($listType)
  {
    $this->view->success = false;
    if ($this->_checkAuth()) {
      $userId = Zend_Auth::getInstance()->getIdentity()->usr_id;
      $albumId = $this->_requestParams['id'];
      if (Model_List::getInstance()->save($albumId, $userId, $listType)) {
        $this->view->success = true;
      }
    }
  }
  
  private function _removeFromList($listType)
  {
    if ($this->_checkAuth()) {
      $userId = Zend_Auth::getInstance()->getIdentity()->usr_id;
      $albumId = $this->_requestParams['id'];
      if (Model_List::getInstance()->remove($albumId, $userId, $listType)) {
        $this->view->success = true;
      }
    }
  }
  
  /**
   * Check if user is logged in, and if not, redirect to information page
   *
   * @return boolean if is logged
   * @since 2011-05-03
   * @author Kuba
   * @file: ListController.php
   **/
  private function _checkAuth()
  {
    $auth = Zend_Auth::getInstance();
    if ($result = !$auth->hasIdentity()) {
      $this->_forward('not-logged-in', 'user');
    }
    return !$result;
  }
}