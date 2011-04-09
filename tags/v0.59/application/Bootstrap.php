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
    
    $registry = new Zend_Registry(array(), ArrayObject::ARRAY_AS_PROPS);
    Zend_Registry::setInstance($registry);
    
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
    
    // $frontController->throwExceptions(false);
    
    
    $this->_setUpLogger();
    // In case I need baseUrl()
    //$frontController->setBaseUrl($this->config['resources']['frontController']['baseUrl'] . '/hhbd');
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
    
    $configApp = Zend_Registry::get('Config_App');
    $view->headIncludes = $configApp['includes'];

    // Navigation, not used
    // $navigation = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
    // $container = new Zend_Navigation($navigation);
    // Zend_Registry::set('Zend_Navigation', $navigation);
    
    // In case I want to turn translation on
    // $translator = new Zend_Translate('array', '../lang/en.php', 'en');
    // $translator->addTranslation('../lang/pl.php', 'pl');
    // $translator->setLocale('pl');
    // $view->navigation()->setTranslator($translator);
    // $view->navigation($container);
  }

  private function _setUpLogger() {
    $dir = $path = '/tmp/logs';
    
    if (!file_exists($dir)) {
      $result = mkdir($dir, 0777, true);
      if (!$result) {
        throw new Jkl_Exception('Cannot created log directory: ' . $dir);
      }
    }

    $file = $path . '/log.txt';
    file_put_contents($file, '', FILE_APPEND);

    $logger = new Zend_Log(new Zend_Log_Writer_Stream($file));
    Zend_Registry::set('Logger', $logger);
    
    return true;
  }
}