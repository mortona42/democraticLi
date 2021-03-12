<?php

namespace Drupal\Tests\simple_page_manager\Functional;

/**
 * Tests the Simple Page Manager UI.
 *
 * @group simple_page_manager
 */
class PageTest extends PageTestBase {

  /**
   * Tests creating a page.
   */
  public function testCreatePage() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
    ]);

    $this->drupalGet('/page');
    $assert_session->statusCodeEquals(200);
    $assert_session->pageTextContains('Page');
  }

  /**
   * Tests page cache.
   */
  public function testPageCache() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
    ]);

    $this->drupalGet('/page');
    $assert_session->responseHeaderContains('X-Drupal-Cache-Tags', 'config:simple_page_manager.page.page');

    $this->drupalGet('/page');
    $assert_session->responseHeaderContains('X-Drupal-Dynamic-Cache', 'HIT');
  }

  /**
   * Tests deleting a page.
   */
  public function testDeletePage() {
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
    ]);

    $this->drupalGet('admin/structure/pages/manage/page/delete');
    $page->pressButton('Delete');

    $this->drupalGet('/page');
    $assert_session->statusCodeEquals(404);
  }

  /**
   * Tests the page listing.
   */
  public function testPageList() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
    ]);

    $this->drupalGet('/admin/structure/pages');

    $assert_session->linkExists('Layout');
    $assert_session->linkExists('Edit');
    $assert_session->linkExists('Delete');

    $this->clickLink('Page');
    $assert_session->addressEquals('/page');
    $assert_session->statusCodeEquals(200);
  }

}
