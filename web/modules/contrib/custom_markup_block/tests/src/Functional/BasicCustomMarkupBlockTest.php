<?php

namespace Drupal\Tests\custom_markup_block\Functional;

/**
 * Tests the basic functionality of the Custom Markup Block.
 *
 * @group custom_markup_block
 */
class BasicCustomMarkupBlockTest extends CustomMarkupBlockTestBase {

  /**
   * Tests that the content block is created on view.
   */
  public function testBlockView() {
    // Place a new custom markup block.
    $random_content = $this->randomString(64);
    $this->drupalPlaceBlock('custom_markup', [
      'region' => 'header',
      'id' => 'custom_markup1',
      'markup' => ['value' => $random_content],
    ]);

    // Gets the home page.
    $this->drupalGet('<front>');

    // Check that the custom block is rendered.
    $this->assertRaw($random_content);
  }

}
