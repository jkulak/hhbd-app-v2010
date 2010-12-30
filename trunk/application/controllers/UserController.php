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
      $this->registerUser();
    }
    else
    {
      // what else?
    }
  }
  
  public function loginAction()
  {
    if ($this->getRequest()->isPost()) {
      $email = $this->_request->getPost('email');
      $password = $this->_request->getPost('password');
      if (empty($email) || empty($password))
      {
        $this->view->errors['email'][] = "Please provide your e-mail address and password.";
      }
      else
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($db);
        $authAdapter->setTableName('hhb_user');
        $authAdapter->setIdentityColumn('usr_email');
        $authAdapter->setCredentialColumn('usr_password');
        $authAdapter->setCredentialTreatment('MD5(?)');

        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($password . Model_User::$passwordSalt);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);

        // Did the participant successfully login?
        if ($result->isValid()) {
          $url = $this->getRequest()->getPost('url');
          // echo $url;
          // echo $this->_helper->('userLogin');
          $this->_redirect($url); 
         
        } else {
          $this->view->errors[] = "Login failed. Have you confirmed your account?";
        }
      }
    }
    else
    {
      if (Zend_Auth::getInstance()->hasIdentity()) {
        $this->_redirect('/');
      }
    }
  }
  
  public function logoutAction()
  {
    Zend_Auth::getInstance()->clearIdentity();
    $this->_redirect('/');
  }
  
  
  private function registerUser()
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