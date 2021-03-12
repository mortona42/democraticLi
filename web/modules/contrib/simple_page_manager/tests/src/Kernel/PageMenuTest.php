<?php

namespace Drupal\Tests\simple_page_manager\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\simple_page_manager\Entity\Page;
use Drupal\system\Entity\Menu;

/**
 * Tests page menu.
 *
 * @group simple_page_manager
 */
class PageMenuTest extends EntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['simple_page_manager'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('page');
  }

  /**
   * Tests deleting a menu.
   */
  public function testMenuDeletion() {
    $menu = Menu::create([
      'id' => 'llama',
      'label' => 'Llama',
    ]);

    $menu->save();

    $page = Page::create([
      'label' => 'Page',
      'id' => 'page',
      'path' => '/page',
      'menu' => [
        'title' => 'Menu link',
        'description' => '',
        'weight' => 0,
        'enabled' => TRUE,
        'menu_name' => 'llama',
        'parent' => '',
        'expanded' => FALSE,
      ],
    ]);

    $page->save();

    $menu->delete();

    $page = Page::load('page');
    $this->assertNotNull($page);
    $this->assertSame(FALSE, $page->getMenu()['enabled']);
  }

}
