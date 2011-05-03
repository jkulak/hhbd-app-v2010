<?php

class ListController extends Zend_Controller_Action
{
  
  public function init()
  {
    $this->_request = $this->getRequest();
    $this->_requestParams = $this->_request->getParams();
    $this->view->album = Model_Album_Api::getInstance()->find($this->_requestParams['id']);
  }

  public function indexAction()
  {
  }
  
  public function addtocollectionAction()
  {
    //check if logged in
    if ($this->_checkAuth()) {
      $userId = Zend_Auth::getInstance()->getIdentity()->usr_id;
      $albumId = $this->_requestParams['id'];
      if (Model_List::getInstance()->save($albumId, $userId, Model_List::TYPE_COLLECTION)) {
        $this->view->success = true;
      }
    }
  }
  
  public function addtowishlistAction()
  {
    if ($this->_checkAuth()) {
      $userId = Zend_Auth::getInstance()->getIdentity()->usr_id;
      $albumId = $this->_requestParams['id'];
      if (Model_List::getInstance()->save($albumId, $userId, Model_List::TYPE_WISHLIST)) {
        $this->view->success = true;
      }
    }
  }
  
  /**
   * Removes album from users collection
   *
   * @return void
   * @since 2011-05-03
   * @author Kuba
   * @file: ListController.php
   **/
  public function removefromcollectionAction()
  {
    if ($this->_checkAuth()) {
      $userId = Zend_Auth::getInstance()->getIdentity()->usr_id;
      $albumId = $this->_requestParams['id'];
      if (Model_List::getInstance()->remove($albumId, $userId, Model_List::TYPE_COLLECTION)) {
        $this->view->success = true;
      }
    }
  }
  
  /**
   * Removes album from users wishlist
   *
   * @return void
   * @since 2011-05-03
   * @author Kuba
   * @file: ListController.php
   **/
  public function removefromwishlistAction()
  {
    if ($this->_checkAuth()) {
      $userId = Zend_Auth::getInstance()->getIdentity()->usr_id;
      $albumId = $this->_requestParams['id'];
      if (Model_List::getInstance()->remove($albumId, $userId, Model_List::TYPE_WISHLIST)) {
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