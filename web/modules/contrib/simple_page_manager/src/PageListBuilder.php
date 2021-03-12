<?php

namespace Drupal\simple_page_manager;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a listing of pages.
 */
class PageListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Name');
    $header['path'] = $this->t('Path');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['title'] = [
      'data' => Link::fromTextAndUrl($entity->label(), $entity->toUrl('canonical')),
      'class' => ['menu-label'],
    ];

    $row['path'] = [
      'data' => $entity->getPath(),
    ];

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = $this->t('No pages available. <a href=":url">Add page</a>.', [
      ':url' => Url::fromRoute('entity.page.add_form')->toString(),
    ]);
    return $build;
  }

}
