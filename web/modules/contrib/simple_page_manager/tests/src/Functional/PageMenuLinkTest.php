<?php

namespace Drupal\Tests\simple_page_manager\Functional;

/**
 * Tests page menu links.
 *
 * @group simple_page_manager
 */
class PageMenuLinkTest extends PageTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->drupalPlaceBlock('system_menu_block:main');
  }

  /**
   * Tests page menu link.
   */
  public function testPageMenuLink() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
      'administer menu',
      'access content',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
      'menu[enabled]' => TRUE,
      'menu[title]' => 'Page menu link',
      'menu[menu_parent]' => 'main:',
    ]);

    $this->drupalGet('');

    $assert_session->linkExists('Page menu link');
    $this->clickLink('Page menu link');

    $assert_session->addressEquals('/page');
    $assert_session->statusCodeEquals(200);
  }

}
