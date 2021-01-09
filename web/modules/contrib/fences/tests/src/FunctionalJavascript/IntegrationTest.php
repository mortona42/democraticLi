<?php

namespace Drupal\Tests\fences\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\fences\Traits\StripWhitespaceTrait;

/**
 * A fences integration test.
 *
 * @group fences
 */
class IntegrationTest extends WebDriverTestBase {

  use StripWhitespaceTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['node', 'field', 'field_ui', 'fences'];

  /**
   * An admin user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * A node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->drupalCreateContentType(['type' => 'article', 'name' => 'Article']);
    $this->node = $this->drupalCreateNode([
      'title' => $this->randomString(),
      'type' => 'article',
      'body' => 'Body field value.',
    ]);
    $this->adminUser = $this->drupalCreateUser(['access content', 'administer node display']);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test the basic settings.
   */
  public function testBasicSettings() {
    $session = $this->assertSession();
    $manage_display = '/admin/structure/types/manage/article/display';
    $this->drupalGet($manage_display);

    $this->submitForm([], 'body_settings_edit');
    $session->assertWaitOnAjaxRequest();

    $this->submitForm([
      'fields[body][label]' => 'above',
      'fields[body][settings_edit_form][third_party_settings][fences][fences_field_tag]' => 'article',
      'fields[body][settings_edit_form][third_party_settings][fences][fences_field_classes]' => 'my-field-class',
      'fields[body][settings_edit_form][third_party_settings][fences][fences_field_item_tag]' => 'code',
      'fields[body][settings_edit_form][third_party_settings][fences][fences_field_item_classes]' => 'my-field-item-class',
      'fields[body][settings_edit_form][third_party_settings][fences][fences_label_tag]' => 'h2',
      'fields[body][settings_edit_form][third_party_settings][fences][fences_label_classes]' => 'my-label-class',
    ], 'Update');
    $session->assertWaitOnAjaxRequest();

    $this->drupalPostForm(NULL, [], 'Save');

    $page = $this->drupalGet('/node/' . $this->node->id());
    $article = $session->elementExists('css', '.field--name-body');
    $this->assertTrue($article->hasClass('my-field-class'), 'Custom field class is present.');
    $label = $session->elementExists('css', 'h2.my-label-class', $article);
    $this->assertSame($label->getText(), 'Body', 'Field label is found in expected HTML element.');
    $body = $session->elementExists('css', 'code.my-field-item-class > p', $article);
    $this->assertSame($body->getText(), 'Body field value.', 'Field text is found in expected HTML element.');
  }

}
