<?php

namespace Drupal\Tests\simple_page_manager\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test base for page testing.
 */
abstract class PageTestBase extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'views',
    'layout_builder',
    'block',
    'simple_page_manager',
    'menu_ui',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * Creates a page through the UI.
   *
   * @param array $page
   *   Page data.
   */
  protected function createPage(array $page) {
    $this->drupalGet('/admin/structure/pages');
    $this->clickLink('Add page');

    $this->submitForm($page, 'Save');
  }

}
