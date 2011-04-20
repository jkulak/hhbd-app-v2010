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
                $this->view->title = 'Błąd 404: Nieeee, nie mamy takiej strony (jeszcze)!';
                $this->view->message = 'Ale nie przejmuj się tym, to nie Twoja wina :) Sprawdź czy wpisałeś dobry adres, a najlepiej chodź na <a href="/">stronę główną</a> lub wpisz czego szukasz w naszej wyszukiwarce!';
                
                // see description below
                // $this->_forward('exception-log-this');
                
                break;
                
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                $this->getResponse()->setHttpResponseCode(500);
                switch($errors->exception->getCode()) {
                  
                  case 2002:
                    $this->_forward('exception-db-connection-failed');
                    break;
                    
                  case Jkl_Cache_Exception::EXCEPTION_MEMCACHED_CONNECTION_FAILED:
                    $this->_forward('exception-memcached-connection-failed');
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
        
        $logger = Zend_Registry::get('Logger');
        $logger->log($errors->exception, Zend_Log::EMERG);
        
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
      $this->view->message = 'Nie udało się połączyć z bazą danych...';
      $this->renderScript('error/error.phtml');
    }
    
    public function exceptionMemcachedConnectionFailedAction()
    {
      $this->view->message = 'Nie udało się połączyć z Memcached.';
      $this->renderScript('error/error.phtml');
    }
    
    // 2011, 22 Jan - I started doing this, but finally couldn't find solution for making simple database query outside models
    // public function exceptionLogThisAction()
    // {
    //   $uri = $this->view->request->getRequestUri();
    //   Jkl_Log::getInstance()->save404ErrorLog($uri);
    //   $this->renderScript('error/error.phtml');
    // }


}