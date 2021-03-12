<?php

namespace Drupal\simple_page_manager;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Provides a view builder for pages.
 */
class PageViewBuilder implements EntityViewBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = [
      '#cache' => [
        'tags' => Cache::mergeTags($this->getCacheTags(), $entity->getCacheTags()),
        'contexts' => $entity->getCacheContexts(),
        'max-age' => $entity->getCacheMaxAge(),
      ],
    ];

    foreach ($entity->getSections() as $delta => $section) {
      $build[$delta] = $section->toRenderArray([]);
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function viewMultiple(array $entities = [], $view_mode = 'full', $langcode = NULL) {
    $build = [];
    foreach ($entities as $key => $entity) {
      $build[$key] = $this->view($entity, $view_mode, $langcode);
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return ['page_view'];
  }

  /**
   * {@inheritdoc}
   */
  public function resetCache(array $entities = NULL) {
    // If no set of specific entities is provided, invalidate the entity view
    // builder's cache tag. This will invalidate all entities rendered by this
    // view builder.
    // Otherwise, if a set of specific entities is provided, invalidate those
    // specific entities only, plus their list cache tags, because any lists in
    // which these entities are rendered, must be invalidated as well. However,
    // even in this case, we might invalidate more cache items than necessary.
    // When we have a way to invalidate only those cache items that have both
    // the individual entity's cache tag and the view builder's cache tag, we'll
    // be able to optimize this further.
    if (isset($entities)) {
      $tags = [];
      foreach ($entities as $entity) {
        $tags = Cache::mergeTags($tags, $entity->getCacheTags());
        $tags = Cache::mergeTags($tags, $entity->getEntityType()->getListCacheTags());
      }
      Cache::invalidateTags($tags);
    }
    else {
      Cache::invalidateTags($this->getCacheTags());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode) {
    throw new \LogicException();
  }

  /**
   * {@inheritdoc}
   */
  public function viewField(FieldItemListInterface $items, $display_options = []) {
    throw new \LogicException();
  }

  /**
   * {@inheritdoc}
   */
  public function viewFieldItem(FieldItemInterface $item, $display_options = []) {
    throw new \LogicException();
  }

}
