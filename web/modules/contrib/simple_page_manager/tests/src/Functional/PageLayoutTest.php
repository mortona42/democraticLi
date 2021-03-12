<?php

namespace Drupal\Tests\simple_page_manager\Functional;

/**
 * Tests the Layout Builder UI for a page.
 *
 * @group simple_page_manager
 */
class PageLayoutTest extends PageTestBase {

  /**
   * Tests Layout Builder.
   */
  public function testLayoutBuilder() {
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
      'configure any layout',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
    ]);

    $this->drupalGet('/admin/structure/pages');
    $this->clickLink('Layout');

    // Add a new section.
    $this->clickLink('Add section');
    $assert_session->linkExists('Two column');
    $this->clickLink('Two column');
    $assert_session->buttonExists('Add section');
    $page->pressButton('Add section');

    // Add block.
    $this->clickLink('Add block');
    $this->clickLink('Powered by Drupal');
    $page->pressButton('Add block');

    $assert_session->pageTextContains('Powered by Drupal');

    $page->pressButton('Save layout');
    $assert_session->addressEquals('/page');

    $assert_session->pageTextContains('Page');
    $assert_session->pageTextContains('Powered by Drupal');
  }

}
