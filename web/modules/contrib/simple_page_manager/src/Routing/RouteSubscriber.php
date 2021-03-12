<?php

namespace Drupal\simple_page_manager\Routing;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\simple_page_manager\Entity\Page;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Subscriber for simple page manager routes.
 *
 * @internal
 *   Tagged services are internal.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The page storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $pageStorage;

  /**
   * RouteSubscriber constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->pageStorage = $entity_type_manager->getStorage('page');
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    /** @var \Drupal\simple_page_manager\Entity\Page[] $pages */
    $pages = $this->pageStorage->loadMultiple();

    foreach ($pages as $entity_id => $entity) {
      $path = $entity->getPath();

      $id = $entity->id();

      $route = new Route($path);
      $route
        ->addDefaults([
          '_entity_view' => 'page.full',
          '_title_callback' => '\Drupal\Core\Entity\Controller\EntityController::title',
          'page' => $entity->id(),
        ])
        ->setRequirement('_entity_access', 'page.view')
        ->setOption('parameters', [
          'page' => ['type' => 'entity:page'],
        ]);

      $collection->add("entity.page.${id}.canonical", $route);
    }
  }

}
