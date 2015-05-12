<?php
/**
 * @Author: Daniel
 * @Date:   2015-05-12 11:56:37
 * @Last Modified by:   Daniel
 * @Last Modified time: 2015-05-12 19:54:16
 */

namespace ZfBootstrap\View\Helper\Navigation;


class BsNavBar extends BsNavMenu
{

  protected $options = array(
    'inverse' => true,
    'fluid' => false,
    'position' => 'fixed-top',
    'ulClass' => 'nav navbar-nav',
    'brandTitle' => 'Cool Zf2 App',
    'brandLink' => '',
    'brandImg' => '',
    'brandShowTitle' => true,
  );

  public function __invoke($container = null)
  {

    if (null !== $container) {
      $this->setContainer($container);
    }

    return $this;

  }

  public function render($container = null)
  {
    return $this->renderNavBar($container);
  }

  public function renderNavBar($container = null)
  {

    $html = '';
    $options = $this->getOptions();

    $navClass = 'navbar navbar-' . $options['position'];
    $navClass.= $options['inverse'] ? ' navbar-inverse' : ' navbar-default';

    $html = '<nav class="' . $navClass . '" role="navigation">' . PHP_EOL;

    $html.= '<div class="container';
    $html.= $options['fluid'] ? '-fluid">' : '">' . PHP_EOL;


    $html.= '<div class="navbar-header">' . PHP_EOL;
    $html.= '<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">';
    $html.= '<span class="icon-bar"></span>';
    $html.= '<span class="icon-bar"></span>';
    $html.= '<span class="icon-bar"></span>';
    $html.= '</button>' . PHP_EOL;
    

    $html.= '<a class="navbar-brand" href="';
    $html.= $options['brandLink'] != '' ? $options['brandLink'] : '#';
    $html.= '">';

    if ($options['brandImg'] != '') {
      $html.= '<img src="' . $options['brandImg'];
      $html.= '" alt="' . $options['brandTitle'] . '"/>';
    }

    if ($options['brandTitle'] != '' && $options['brandShowTitle']) {
      $html.= $options['brandImg'] != '' ? '&nbsp;&nbsp;' . $options['brandTitle'] : $options['brandTitle'];
    }

    $html.= '</a>' . PHP_EOL;
    $html.= '</div>' . PHP_EOL; //navbar-header

    $html.='<div class="collapse navbar-collapse">' . PHP_EOL;
    $html.= $this->renderMenu($container, $options) . PHP_EOL;
    $html.= '</div>' . PHP_EOL;

    $html.= '</div>' . PHP_EOL; //container
    $html.= '</nav>';

    return $html;

  }

  public function getOptions()
  {
    return $this->options;
  }

  public function setOptions($options)
  {

    if (is_array($options)) {
      foreach ($options as $key => $value) {
        $this->options[$key] = $value;
      }
    }

    return $this;

  }

}
