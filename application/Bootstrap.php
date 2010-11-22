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
  
  // protectedfunction _initDupa($value='')
  // {
  //   echo $value . '_initDupa()';
  // }
  
  protected function _initApplication()
  {
    
    $registry = new Zend_Registry(array(), ArrayObject::ARRAY_AS_PROPS);
    Zend_Registry::setInstance($registry);
    
    // Pobieranie danych z pliku konfiguracyjnego (zaladowanego przez Bootstrap) i dodanie ich do rejestru
    // $this->config = $this->getOptions();
    // Zend_Registry::set('config', $this->config);

    // Odczytanie opcji z sekcj app
    $resourcesConfig = $this->getOption('resources');
    Zend_Registry::set('Config_Resources_Db', $resourcesConfig['db']);
    
    // debugging
    $writer = new Zend_Log_Writer_Firebug();
    $this->logger = new Zend_Log($writer);

    // // routing
    $frontController = Zend_Controller_Front::getInstance();
    $router = $frontController->getRouter();
    
    // Zend_Controller_Router_Route::setDefaultTranslator($translator);
    
    $routes = new Zend_Config_Xml(APPLICATION_PATH . '/configs/routes.xml', APPLICATION_ENV);
    
    //$router->removeDefaultRoutes();
    
    $router->addConfig($routes, 'routes');
    
    // This should be read form applcation.ini
    $frontController->setBaseUrl('http://hhbd.megiteam.pl');
  }
       
  protected function _initView()
  {
    $this->bootstrap('layout');
    $layout = $this->getResource('layout');
    $view = $layout->getView();

    $view->doctype('HTML5');
    $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    $view->headMeta()->setCharset('utf-8');
    
    $appConfig = $this->getOption('app');
    Zend_Registry::set('Config_App', $appConfig);
    
    $view->appIncludes = $appConfig['includes'];
  
    
    $view->headMeta()->setName('robots', 'index,follow');
    $view->headMeta()->setName('author', 'Jakub Kułak, www.webascrazy.net');
    
    $view->headTitle()->setSeparator(' | ');
    $view->headTitle('HHBD.PL');
    
    $translator = new Zend_Translate('array', '../lang/en.php', 'en');
    $translator->addTranslation('../lang/pl.php', 'pl');
    
    $translator->setLocale('pl');
    
    $navigation = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
    $container = new Zend_Navigation($navigation);
    
    $view->navigation()->setTranslator($translator);    
    $view->navigation($container);
    
    Zend_Registry::set('Zend_Navigation', $navigation);

  }

}