<?php

class UserController extends Zend_Controller_Action
{
  
  public function init()
  {
    $this->_request = $this->getRequest();
  }

  public function indexAction()
  {

  }
  
  public function viewAction()
  {
    $id = $this->_request->get('id');
    $this->view->user = Model_User::getInstance()->findById($id);
  }
  
  public function registrationAction()
  {
    // if post data received, it's user creation
    $reg = $this->_request->getPost('registration');
    if (!empty($reg)) {
      $this->_registerUser();
    }
    else
    {
      // if is registered - move to homesite
      if (Zend_Auth::getInstance()->hasIdentity()) {
        $this->_redirect($this->view->url(array(), 'homeSite'));
      }
    }
  }
  
  public function loginAction()
  {
    $errors = array();
    if ($this->getRequest()->isPost()) {
      $email = $this->_request->getPost('email');
      $password = $this->_request->getPost('password');
      if (empty($email) || empty($password))
      {
        $errors['login'][] = "Podaj adres e-mail i hasło i spróbuj jeszcze raz.";
      }
      else
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($db);
        $authAdapter->setTableName('hhb_users');
        $authAdapter->setIdentityColumn('usr_email');
        $authAdapter->setCredentialColumn('usr_password');
        $authAdapter->setCredentialTreatment('MD5(?)');

        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($password . Model_User::$passwordSalt);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);

        // Did the participant successfully login?
        if ($result->isValid()) {
          // retrive user data needed to build links
          $data = $authAdapter->getResultRowObject(array('usr_display_name', 'usr_id', 'usr_is_admin'));
          $auth->getStorage()->write($data);
          
          $url = $this->getRequest()->getPost('url');
          $this->_redirect($url); 
         
        } else {
          $errors['login'][] = "Podane dane do logowania nie są poprawne. Popraw je i spróbuj jeszcze raz.";
        }
      }
    }
    else
    {
      if (Zend_Auth::getInstance()->hasIdentity()) {
        $this->_redirect('/');
      }
    }
    $this->view->errors = $errors;
  }
  
  public function logoutAction()
  {
    Zend_Auth::getInstance()->clearIdentity();
    $this->_redirect('/');
  }
  
  
  private function _registerUser()
  {
    $result = Model_User::getInstance()->save($this->_request->getPost());
    
    if (! is_array($result)) {
      $this->view->user = Model_User::getInstance()->findById($result);
      $this->view->created = 1;
    } else {
      $this->view->errors = $result;
      // $this->view->firstName = $this->_request->getPost('first-name');
      // $this->view->lastName = $this->_request->getPost('last-name');
      $this->view->email = $this->_request->getPost('email');
      $this->view->password = $this->_request->getPost('password');
      $this->view->displayName = $this->_request->getPost('display-name');
    }
  }
}