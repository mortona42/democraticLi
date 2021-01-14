<?php
namespace Drupal\title_field_for_manage_display\Tests;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests Title Field.
 *
 * @group Title field for manage display
 */
class TitleFieldTests extends BrowserTestBase {

  /**
   * The installation profile to use with this test.
   *
   * @var string
   */
  protected $profile = 'standard';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('title_field_for_manage_display');

  /**
   * A user with the 'Administer Title' permission.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create admin user.
    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
      'create page content',
    ]);
  }

  /**
   * Tests.
   */
  function testTitle() {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet("user");
  }
}
