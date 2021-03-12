<?php

namespace Drupal\simple_page_manager\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides menu links for Simple Page Manager.
 *
 * @see \Drupal\simple_page_manager\Plugin\Menu\PageMenuLink
 */
class PageMenuLink extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The page storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $pageStorage;

  /**
   * Constructs a PageMenuLink instance.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $page_storage
   *   The page storage.
   */
  public function __construct(EntityStorageInterface $page_storage) {
    $this->pageStorage = $page_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')->getStorage('page')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $links = [];

    $pages = $this->pageStorage->loadMultiple();

    foreach ($pages as $page_id => $page) {
      $menu = $page->getMenu();

      if ($menu['enabled']) {
        $link_id = "page.${page_id}";
        $links[$link_id] = $menu + $base_plugin_definition;
        $links[$link_id]['route_name'] = "entity.page.${page_id}.canonical";
        $links[$link_id]['metadata'] = ['entity_id' => $page_id];
      }
    }

    return $links;
  }

}
