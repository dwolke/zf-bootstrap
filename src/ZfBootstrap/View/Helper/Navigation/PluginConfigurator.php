<?php
/**
 * @Author: Daniel
 * @Date:   2015-05-11 18:18:38
 * @Last Modified by:   Daniel
 * @Last Modified time: 2015-05-12 19:54:12
 */

namespace ZfBootstrap\View\Helper\Navigation;

use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceManager;

class PluginConfigurator implements ConfigInterface
{

  /**
   * @var array Nav View helpers
   */
  protected $helpers = array(
    'bsNavMenu'     => 'ZfBootstrap\View\Helper\Navigation\BsNavMenu',
    'bsNavBar'      => 'ZfBootstrap\View\Helper\Navigation\BsNavbar',
  );

  public function configureServiceManager(ServiceManager $serviceManager)
  {
    foreach($this->helpers as $name => $fqcn) {
      $serviceManager->setInvokableClass($name, $fqcn);
    }
  }

}
