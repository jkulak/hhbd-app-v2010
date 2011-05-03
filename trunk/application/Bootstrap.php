<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
   
  // initiates autoloader for modules
  protected function _initAutoload()
  {
     $moduleLoader = new Zend_Application_Module_Autoloader(array(
       'namespace' => '',
       'basePath' => APPLICATION_PATH)
       );
    return $moduleLoader;
  }
  
  protected function _initApplication()
  {
    Zend_Registry::set('Logger', $this->bootstrap('log')->getResource('log'));
    
    // Load configuration from file, put it in the registry
    $appConfig = $this->getOption('app');
    Zend_Registry::set('Config_App', $appConfig);

    // Read Resources section and put it in registry
    $resourcesConfig = $this->getOption('resources');
    Zend_Registry::set('Config_Resources', $resourcesConfig);
    
    // Read Resources section and put it in registry
    $resourcesConfig = $this->getOption('resources');
    Zend_Registry::set('Memcached', Jkl_Cache::getInstance());

    // Start routing
    $frontController = Zend_Controller_Front::getInstance();
    $router = $frontController->getRouter();
    
    // In case I want to turn on translation
    // Zend_Controller_Router_Route::setDefaultTranslator($translator);
    $routes = new Zend_Config_Xml(APPLICATION_PATH . '/configs/routes.xml', APPLICATION_ENV);
    //$router->removeDefaultRoutes();
    $router->addConfig($routes, 'routes');
  }
       
  protected function _initView()
  {
    $this->bootstrap('layout');
    $layout = $this->getResource('layout');
    $view = $layout->getView();

    $view->doctype('HTML5');
    // $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    $view->headMeta()->setCharset('utf-8');    
    $view->headMeta()->setName('robots', 'index,follow');
    $view->headMeta()->setName('author', 'Jakub Kułak, www.webascrazy.net');
    $view->headTitle()->setSeparator(' - ');
    $view->headTitle('Hhbd.pl');
  }
}