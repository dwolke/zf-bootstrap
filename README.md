ZfBootstrap
===========

Simple Module with ViewHelpers for using with ZF2 Navigation with Bootstrap 3.

Actually created for personal use, but feel free to use this in your own projects.


Usage
-----

Create your navigation in `module.config.php`:

	'navigation' => array(
		'default' => array(
			array(
				'label' => 'Startseite',
				'route' => 'home',
			),
			//Dropdown-Menu
			array(
				'label' => 'Dropdown-Test',
				'uri' => '#',
				'dropdown' => true,
				'pages'       => array(
					array(
						'label'   => 'Bar',
						'route'   => 'bar',
						'controller'  => 'Application\Controller\Index',
						'action'      => 'bar',
					),
					// Menu divider
					array(
						'type' => 'uri',
						'divider' => true,
					),
					array(
						'label'   => 'Baz',
						'route'   => 'baz',
						'controller'  => 'Application\Controller\Index',
						'action'      => 'baz',
					),
				),
			),
		),
	),

add the navigation to the service manager:

	'service_manager' => array(
		
		// this adds the default Navigation
		'factories' => array(
			'my_navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
		),
	
	),

and use the viewhelpers in your view/layout scripts:

	<?php
		// render only the 'ul'
		echo $this->navigation('my_navigation')->bsNavMenu()->setUlClass('nav navbar-nav');
		
		// or render the whole Navbar
		echo $this->navigation('my_navigation')->bsNavBar()->setOptions($navbarOptions);

	?>

`$navbaroptions` is an Array with the following options:

 * `inverse` : true | false
 * `fluid` true | false (container-fluid | container)
 * `position` (string): fixed-top | fixed-bottom | static-top
 * `ulClass` (string): the css class for the first 'ul'
 * `brandTitle` (string) 
 * `brandLink` (string)
 * `brandImg` (string): path to img
 * `brandShowTitle` : true | false
 
all options are optional ;-)

