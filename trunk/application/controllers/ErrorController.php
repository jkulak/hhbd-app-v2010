<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Tym razem nie odnaleziono strony. Sprawdź czy wpisałeś dobry adres, a najlepiej kliknij w logo hhbd.pl';
                break;
                
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                // $this->view->exception = $errors->exception;
                $this->getResponse()->setHttpResponseCode(500);
                switch($errors->exception->getCode()) {
                  case Jkl_Model_Exception::EXCEPTION_DB_CONNECTION_FAILED:
                    
                    $this->_forward('exception-db-connection-failed');
                    break;
                    
                  default:
                    $this->view->message = 'Exception caught (' . get_class($errors->exception) . '), but no specific handler in ErrorHandler defined';
                    $this->view->exception = $errors->exception;
                    break;
                  }
                
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error 500';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
        
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
    
    public function exceptionDbConnectionFailedAction()
    {
      $this->view->message = 'Nie udało się połączyć z bazą danych.';
      $this->renderScript('error/error.phtml');
    }


}