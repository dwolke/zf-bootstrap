<?php
/**
 * @Author: Daniel
 * @Date:   2015-05-11 17:58:13
 * @Last Modified by:   Daniel
 * @Last Modified time: 2015-05-12 19:53:00
 */

namespace ZfBootstrap;

use Zend\Mvc\MvcEvent;

class Module
{

  public function getAutoloaderConfig() {

    return array(
      'Zend\Loader\ClassMapAutoloader' => array(
        __DIR__ . '/autoload_classmap.php',
      ),
      'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
          __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
        ),
      ),
    );

  }

  public function getConfig() {
    return include __DIR__ . '/config/module.config.php';
  }

  public function onBootstrap(MvcEvent $e) {

    $sm = $e->getApplication()->getServiceManager();
    $viewHelperManager = $sm->get('ViewHelperManager');
    $navHelperConfigurator = $sm->get('BootstrapNavHelperConfigurator');
    $navHelperPluginManager = $viewHelperManager->get('Navigation')->getPluginManager();
    $navHelperConfigurator->configureServiceManager($navHelperPluginManager);
    
  }

}
