<?php

namespace Drupal\Tests\simple_page_manager_linkit\Kernel\Matchers;

use Drupal\simple_page_manager\Entity\Page;
use Drupal\Tests\linkit\Kernel\LinkitKernelTestBase;

/**
 * Tests page matcher.
 *
 * @group simple_page_manager_linkit
 */
class PageMatcherTest extends LinkitKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'simple_page_manager',
    'simple_page_manager_linkit'
  ];

  /**
   * The matcher manager.
   *
   * @var \Drupal\linkit\MatcherManager
   */
  protected $manager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('page');

    $this->manager = $this->container->get('plugin.manager.linkit.matcher');
  }

  /**
   * Tests page matcher.
   */
  public function testPageMatcherWidthDefaultConfiguration() {
    /** @var \Drupal\linkit\MatcherInterface $plugin */
    $plugin = $this->manager->createInstance('page', []);

    $this->createPage('Page 1');
    $this->createPage('Page 2');
    $this->createPage('Page 3');
    $this->createPage('Page 4');

    $suggestions = $plugin->execute('Page');
    $this->assertEquals(4, count($suggestions->getSuggestions()), 'Correct number of suggestions');
  }

  /**
   * Create a page.
   *
   * @param string $label
   *   The page label.
   *
   * @return \Drupal\simple_page_manager\Entity\Page
   *   The created page.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createPage($label) {
    $page = Page::create([
      'label' => $label,
      'id' => $this->randomMachineName(),
      'path' => '/' . $this->randomString(),
    ]);

    $page->save();
  }

}
