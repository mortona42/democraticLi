<?php

namespace Drupal\Tests\simple_page_manager_metatag\Functional;

use Drupal\simple_page_manager\Entity\Page;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\metatag\Functional\MetatagHelperTrait;

/**
 * Tests integration with Metatag.
 *
 * @group simple_page_manager
 */
class SimplePageManagerMetatagTest extends BrowserTestBase {

  use MetatagHelperTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'simple_page_manager_metatag',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The assert session object.
   *
   * @var \Drupal\Tests\WebAssert
   */
  protected $assertSession;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->assertSession = $this->assertSession();

    Page::create([
      'id' => 'page',
      'label' => 'Page',
      'path' => '/page',
    ])->save();

    \Drupal::service('router.builder')->rebuild();

    // Log in as user 1.
    $this->loginUser1();
  }

  /**
   * Tests integration.
   */
  public function testIntegration() {
    $this->drupalGet('/page');
    $this->assertSession->statusCodeEquals(200);

    // Confirm what the page title looks like by default.
    $this->assertSession->titleEquals('Page | Drupal');

    // Create the Metatag object through the UI to check the custom label.
    $edit = [
      'id' => 'page__page',
      'title' => 'My title',
    ];

    $this->drupalGet('/admin/config/search/metatag/add');
    $this->submitForm($edit, 'Save');

    $this->assertSession->pageTextContains('Page: Page');

    // Rebuild cache.
    drupal_flush_all_caches();

    $this->drupalGet('/page');
    $this->assertSession->statusCodeEquals(200);
    $this->assertSession->titleEquals('My title');
  }

}
