<?php

namespace Drupal\simple_page_manager\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\layout_builder\SectionListInterface;
use Drupal\layout_builder\SectionStorage\SectionStorageTrait;
use Drupal\system\Entity\Menu;

/**
 * Defines the Page configuration entity.
 *
 * @ConfigEntityType(
 *   id = "page",
 *   label = @Translation("Page"),
 *   label_collection = @Translation("Pages"),
 *   label_singular = @Translation("page"),
 *   label_plural = @Translation("pages"),
 *   label_count = @PluralTranslation(
 *     singular = "@count page",
 *     plural = "@count pages"
 *   ),
 *   handlers = {
 *     "storage" = "Drupal\simple_page_manager\PageStorage",
 *     "view_builder" = "Drupal\simple_page_manager\PageViewBuilder",
 *     "access" = "Drupal\simple_page_manager\PageAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\simple_page_manager\PageForm",
 *       "edit" = "Drupal\simple_page_manager\PageForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *       "layout_builder" = "Drupal\simple_page_manager\LayoutBuilderForm",
 *     },
 *     "list_builder" = "Drupal\simple_page_manager\PageListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     }
 *   },
 *   admin_permission = "administer pages",
 *   uri_callback = "page_uri",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "path",
 *     "sections",
 *     "menu",
 *     "access",
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/pages/add",
 *     "edit-form" = "/admin/structure/pages/manage/{page}",
 *     "delete-form" = "/admin/structure/pages/manage/{page}/delete",
 *     "collection" = "/admin/structure/pages",
 *   },
 * )
 */
class Page extends ConfigEntityBase implements SectionListInterface {

  use SectionStorageTrait;

  /**
   * The machine name of this page.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the page.
   *
   * @var string
   */
  protected $label;

  /**
   * The path of the page.
   *
   * @var string
   */
  protected $path;

  /**
   * Sections.
   *
   * @var array
   */
  protected $sections = [];

  /**
   * Menu.
   *
   * @var array
   */
  protected $menu = [
    'title' => '',
    'description' => '',
    'weight' => 0,
    'enabled' => FALSE,
    'menu_name' => 'main',
    'parent' => '',
    'expanded' => FALSE,
  ];

  /**
   * Access.
   *
   * @var array
   */
  protected $access = [
    'type' => 'none',
    'options' => [],
  ];

  /**
   * Gets the path of the page.
   *
   * @return string
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * Gets the menu.
   *
   * @return array
   */
  public function getMenu() {
    return $this->menu;
  }

  /**
   * Gets the access.
   *
   * @return array
   */
  public function getAccess() {
    return $this->access;
  }

  /**
   * {@inheritdoc}
   */
  public function getSections() {
    return $this->sections;
  }

  /**
   * {@inheritdoc}
   */
  protected function setSections(array $sections) {
    $this->sections = array_values($sections);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    /** @var \Drupal\Core\Routing\RouteBuilderInterface $router_builder */
    $router_builder = \Drupal::service('router.builder');
    $router_builder->setRebuildNeeded();
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    /** @var \Drupal\Core\Routing\RouteBuilderInterface $router_builder */
    $router_builder = \Drupal::service('router.builder');
    $router_builder->setRebuildNeeded();
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();

    foreach ($this->getSections() as $delta => $section) {
      $this->calculatePluginDependencies($section->getLayout());
      foreach ($section->getComponents() as $uuid => $component) {
        $this->calculatePluginDependencies($component->getPlugin());
      }
    }

    $menu = $this->getMenu();
    if ($menu['enabled'] && ($menu_entity = Menu::load($menu['menu_name']))) {
      $this->addDependency($menu_entity->getConfigDependencyKey(), $menu_entity->getConfigDependencyName());
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function onDependencyRemoval(array $dependencies) {
    $changed = parent::onDependencyRemoval($dependencies);

    // Loop through all sections and determine if the removed dependencies are
    // used by their layout plugins.
    foreach ($this->getSections() as $delta => $section) {
      $layout_dependencies = $this->getPluginDependencies($section->getLayout());
      $layout_removed_dependencies = $this->getPluginRemovedDependencies($layout_dependencies, $dependencies);
      if ($layout_removed_dependencies) {
        // @todo Allow the plugins to react to their dependency removal in
        //   https://www.drupal.org/project/drupal/issues/2579743.
        $this->removeSection($delta);
        $changed = TRUE;
      }
      // If the section is not removed, loop through all components.
      else {
        foreach ($section->getComponents() as $uuid => $component) {
          $plugin_dependencies = $this->getPluginDependencies($component->getPlugin());
          $component_removed_dependencies = $this->getPluginRemovedDependencies($plugin_dependencies, $dependencies);
          if ($component_removed_dependencies) {
            // @todo Allow the plugins to react to their dependency removal in
            //   https://www.drupal.org/project/drupal/issues/2579743.
            $section->removeComponent($uuid);
            $changed = TRUE;
          }
        }
      }
    }

    $menu = $this->getMenu();
    if ($menu['enabled'] && ($menu_entity = Menu::load($menu['menu_name'])) && isset($dependencies[$menu_entity->getConfigDependencyKey()])) {
      $this->removeMenuLink();
      $changed = TRUE;
    }

    return $changed;
  }

  /**
   * Returns the plugin dependencies being removed.
   *
   * The function recursively computes the intersection between all plugin
   * dependencies and all removed dependencies.
   *
   * Note: The two arguments do not have the same structure.
   *
   * @param array[] $plugin_dependencies
   *   A list of dependencies having the same structure as the return value of
   *   ConfigEntityInterface::calculateDependencies().
   * @param array[] $removed_dependencies
   *   A list of dependencies having the same structure as the input argument of
   *   ConfigEntityInterface::onDependencyRemoval().
   *
   * @return array
   *   A recursively computed intersection.
   *
   * @see \Drupal\Core\Config\Entity\ConfigEntityInterface::calculateDependencies()
   * @see \Drupal\Core\Config\Entity\ConfigEntityInterface::onDependencyRemoval()
   */
  protected function getPluginRemovedDependencies(array $plugin_dependencies, array $removed_dependencies) {
    $intersect = [];
    foreach ($plugin_dependencies as $type => $dependencies) {
      if ($removed_dependencies[$type]) {
        // Config and content entities have the dependency names as keys while
        // module and theme dependencies are indexed arrays of dependency names.
        // @see \Drupal\Core\Config\ConfigManager::callOnDependencyRemoval()
        if (in_array($type, ['config', 'content'])) {
          $removed = array_intersect_key($removed_dependencies[$type], array_flip($dependencies));
        }
        else {
          $removed = array_values(array_intersect($removed_dependencies[$type], $dependencies));
        }
        if ($removed) {
          $intersect[$type] = $removed;
        }
      }
    }
    return $intersect;
  }

  /**
   * Remove the menu link.
   */
  public function removeMenuLink(): void {
    $this->menu = [
      'title' => '',
      'description' => '',
      'weight' => 0,
      'enabled' => FALSE,
      'menu_name' => 'main',
      'parent' => '',
      'expanded' => FALSE,
    ];
  }

}
