<?php

namespace Drupal\simple_page_manager\Plugin\Menu;

use Drupal\Core\Menu\MenuLinkBase;
use Drupal\simple_page_manager\Entity\Page;

/**
 * Provides menu links for pages.
 */
class PageMenuLink extends MenuLinkBase {

  /**
   * The page entity.
   *
   * @var \Drupal\simple_page_manager\Entity\Page
   */
  protected $page;

  /**
   * Gets the page entity.
   *
   * @return \Drupal\simple_page_manager\Entity\Page
   *   The page entity.
   */
  protected function getPage() {
    if (empty($this->page)) {
      $metadata = $this->getMetaData();
      $page = Page::load($metadata['entity_id']);
      $this->page = $page;
    }
    return $this->page;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->pluginDefinition['title'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function updateLink(array $new_definition_values, $persist) {
    // Update the definition.
    $this->pluginDefinition = $new_definition_values + $this->pluginDefinition;
    if ($persist) {
      $page = $this->getPage();
      $menu = $page->getMenu();

      $changed = FALSE;
      foreach ($new_definition_values as $key => $new_definition_value) {
        if (empty($menu[$key]) || $menu[$key] !== $new_definition_value) {
          $menu[$key] = $new_definition_value;
          $changed = TRUE;
        }
      }
      if ($changed) {
        $page->set('menu', $menu);
        $page->save();
      }
    }
    return $this->pluginDefinition;
  }

}
