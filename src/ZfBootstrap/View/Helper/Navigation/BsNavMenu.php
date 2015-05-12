<?php
/**
 * @Author: Daniel
 * @Date:   2015-05-11 18:24:04
 * @Last Modified by:   Daniel
 * @Last Modified time: 2015-05-12 19:54:19
 */

namespace ZfBootstrap\View\Helper\Navigation;

use RecursiveIteratorIterator;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\View\Helper\Navigation\AbstractHelper;
use Zend\View\Exception;

class BsNavMenu extends AbstractHelper
{

  protected $addClassToListItem = false;
  protected $escapeLabels = true;
  protected $ulClass = 'navigation';
  protected $liActiveClass = 'active';

  public function __invoke($container = null)
  {

    if (null !== $container) {
      $this->setContainer($container);
    }

    return $this;

  }

  public function render($container = null)
  {
    return $this->renderMenu($container);
  }

  public function renderMenu($container = null, array $options = array())
  {

    $this->parseContainer($container);

    if (null === $container) {
      $container = $this->getContainer();
    }

    $options = $this->normalizeOptions($options);

    $html = $this->renderNormalMenu(
        $container,
        $options['ulClass'],
        $options['indent'],
        $options['escapeLabels'],
        $options['addClassToListItem'],
        $options['liActiveClass']
      );

    return $html;

  }

  protected function renderNormalMenu(
    AbstractContainer $container,
    $ulClass,
    $indent,
    $escapeLabels,
    $addClassToListItem,
    $liActiveClass
    ) {

    $html = '';

    $minDepth = null;
    $maxDepth = null;
    $onlyActive = false;

    // find deepest active
    $found = $this->findActive($container);

    /* @var $escaper \Zend\View\Helper\EscapeHtmlAttr */
    $escaper = $this->view->plugin('escapeHtmlAttr');

    if ($found) {
      $foundPage  = $found['page'];
      $foundDepth = $found['depth'];
    } else {
      $foundPage = null;
    }

    // create iterator
    $iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);

    if (is_int($maxDepth)) {
      $iterator->setMaxDepth($maxDepth);
    }

    // iterate container
    $prevDepth = -1;

    foreach ($iterator as $page) {

      $depth = $iterator->getDepth();
      $isActive = $page->isActive(true);

      if ($depth < $minDepth || !$this->accept($page)) {
        // page is below minDepth or not accepted by acl/visibility
        continue;
      } elseif ($onlyActive && !$isActive) {

        // $onlyActive raus???
        
        // page is not active itself, but might be in the active branch
        $accept = false;

        if ($foundPage) {

          if ($foundPage->hasPage($page)) {
            // accept if page is a direct child of the active page
            $accept = true;
          } elseif ($foundPage->getParent()->hasPage($page)) {

            // page is a sibling of the active page...
            if (!$foundPage->hasPages(!$this->renderInvisible) || is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
              // accept if active page has no children, or the children are too deep to be rendered
              $accept = true;
            }

          }

        }

        if (!$accept) {
          continue;
        }

      }

      // make sure indentation is correct
      $depth -= $minDepth;
      $myIndent = $indent . str_repeat('  ', $depth);

      if ($depth > $prevDepth) {

        // start new ul tag
        if ($ulClass && $depth ==  0) {
          $ulClass = ' class="' . $escaper($ulClass) . '"';
        } else {
          $ulClass = '';
        }

        if ($page->getParent() instanceOf \Zend\Navigation\Page\Uri && $page->getParent()->hasPages()) {
          if ($page->getParent()->dropdown) {
            $ulClass = ' class="dropdown-menu"';
          }
        }

        $html .= $myIndent . '<ul' . $ulClass . '>' . PHP_EOL;

      } elseif ($prevDepth > $depth) {

        // close li/ul tags until we're at current depth
        for ($i = $prevDepth; $i > $depth; $i--) {
          $ind = $indent . str_repeat('        ', $i);
          $html .= $ind . '  </li>' . PHP_EOL;
          $html .= $ind . '</ul>' . PHP_EOL;
        }

        // close previous li tag
        $html .= $myIndent . '</li>' . PHP_EOL;

      } else {
        // close previous li tag
        $html .= $myIndent . '</li>' . PHP_EOL;
      }

      // render li tag and page
      $liClasses = array();

      // Is page active?
      if ($isActive) {
        $liClasses[] = $liActiveClass;
      }

      if ($page->hasPages()) {
        $liClasses[] = 'dropdown';
      }

      // Add CSS class from page to <li>
      if ($addClassToListItem && $page->getClass()) {
        $liClasses[] = $page->getClass();
      }

      //var_dump($liClasses);

      $liClass = empty($liClasses) ? '' : ' class="' . $escaper(implode(' ', $liClasses)) . '"';

      if ($page instanceOf \Zend\Navigation\Page\Uri && $page->divider) {
        $html.= '<li class="divider">';
      } else {
        $html .= $myIndent . '<li' . $liClass . '>' . $this->htmlify($page, $escapeLabels, $addClassToListItem);
      }

      // store as previous depth for next iteration
      $prevDepth = $depth;

    }

    if ($html) {

      // done iterating container; close open ul/li tags
      for ($i = $prevDepth+1; $i > 0; $i--) {

        $myIndent = $indent . str_repeat('  ', $i-1);
        $html .= $myIndent . '</li>' . PHP_EOL . $myIndent . '</ul>' . PHP_EOL;

      }

      $html = rtrim($html, PHP_EOL);

    }

    return $html;

  }

  public function htmlify(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false)
  {

    if ($page instanceOf \Zend\Navigation\Page\Uri && $page->dropdown) {
      
      $dropdown = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
      $dropdown.= $this->translate($page->getLabel(), $page->getTextDomain());
      $dropdown.= ' <span class="caret"></span></a>' . PHP_EOL;

      return $dropdown;

    }

    // get attribs for element
    $attribs = array(
      'id'     => $page->getId(),
      'title'  => $this->translate($page->getTitle(), $page->getTextDomain()),
    );

    if ($addClassToListItem === false) {
      $attribs['class'] = $page->getClass();
    }

    // does page have a href?
    $href = $page->getHref();

    if ($href) {
      $element = 'a';
      $attribs['href'] = $href;
      $attribs['target'] = $page->getTarget();
    } else {
      $element = 'span';
    }

    $html  = '<' . $element . $this->htmlAttribs($attribs) . '>';
    $label = $this->translate($page->getLabel(), $page->getTextDomain());

    if ($escapeLabel === true) {

      /** @var \Zend\View\Helper\EscapeHtml $escaper */
      $escaper = $this->view->plugin('escapeHtml');
      $html .= $escaper($label);

    } else {
      $html .= $label;
    }

    $html .= '</' . $element . '>';

    return $html;

  }


  /**
   * Normalizes given render options
   *
   * @param  array $options  [optional] options to normalize
   * @return array
   */
  protected function normalizeOptions(array $options = array())
  {

    if (isset($options['indent'])) {
      $options['indent'] = $this->getWhitespace($options['indent']);
    } else {
      $options['indent'] = $this->getIndent();
    }

    if (isset($options['ulClass']) && $options['ulClass'] !== null) {
      $options['ulClass'] = (string) $options['ulClass'];
    } else {
      $options['ulClass'] = $this->getUlClass();
    }

    if (!isset($options['escapeLabels'])) {
      $options['escapeLabels'] = $this->escapeLabels;
    }

    if (!isset($options['addClassToListItem'])) {
      $options['addClassToListItem'] = $this->getAddClassToListItem();
    }

    if (isset($options['liActiveClass']) && $options['liActiveClass'] !== null) {
      $options['liActiveClass'] = (string) $options['liActiveClass'];
    } else {
      $options['liActiveClass'] = $this->getLiActiveClass();
    }

    return $options;

  }

  public function setUlClass($ulClass)
  {

    if (is_string($ulClass)) {
      $this->ulClass = $ulClass;
    }

    return $this;

  }

  public function getUlClass()
  {
    return $this->ulClass;
  }

  public function setAddClassToListItem($flag = true)
  {
    $this->addClassToListItem = (bool) $flag;
    return $this;
  }

  public function getAddClassToListItem()
  {
    return $this->addClassToListItem;
  }

  public function setLiActiveClass($liActiveClass)
  {

    if (is_string($liActiveClass)) {
      $this->liActiveClass = $liActiveClass;
    }

    return $this;

  }

  public function getLiActiveClass()
  {
    return $this->liActiveClass;
  }

}
