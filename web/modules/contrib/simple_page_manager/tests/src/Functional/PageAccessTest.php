<?php

namespace Drupal\Tests\simple_page_manager\Functional;

/**
 * Tests page access.
 *
 * @group simple_page_manager
 */
class PageAccessTest extends PageTestBase {

  /**
   * Tests none access.
   */
  public function testNoneAccess() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
      'access[type]' => 'none',
    ]);

    $this->drupalGet('/page');
    $assert_session->statusCodeEquals(200);
    $assert_session->pageTextContains('Page');
  }

  /**
   * Tests permission access.
   */
  public function testPermissionAccess() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
      'access[type]' => 'permission',
      'access[permission]' => 'administer pages',
    ]);

    $this->drupalLogout();

    $this->drupalGet('/page');
    $assert_session->statusCodeEquals(403);

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
    ]));

    $this->drupalGet('/page');
    $assert_session->statusCodeEquals(200);
    $assert_session->pageTextContains('Page');

  }

  /**
   * Tests role access.
   */
  public function testRoleAccess() {
    $role = $this->drupalCreateRole(['access content'], 'tester');

    $assert_session = $this->assertSession();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
      'access[type]' => 'role',
      'access[role]' => $role,
    ]);

    $this->drupalLogout();

    $user = $this->drupalCreateUser();
    $user->addRole($role);
    $user->save();

    $this->drupalLogin($user);

    $this->drupalGet('/page');
    $assert_session->statusCodeEquals(200);
    $assert_session->pageTextContains('Page');

    $this->drupalLogout();

    $this->drupalGet('/page');
    $assert_session->statusCodeEquals(403);
  }

  /**
   * Tests role access type missing a selected role.
   */
  public function testRoleAccessTypeMissingASelectedRole() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
    ]));

    $this->createPage([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
      'access[type]' => 'role',
    ]);

    $assert_session->pageTextContains('You must select a role when using the Role access type.');
  }

}
