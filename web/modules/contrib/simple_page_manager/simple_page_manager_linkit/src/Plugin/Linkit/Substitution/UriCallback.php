<?php

namespace Drupal\simple_page_manager_linkit\Plugin\Linkit\Substitution;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\linkit\SubstitutionInterface;

/**
 * A substitution plugin for the canonical URL of an entity using uri_callback.
 *
 * @Substitution(
 *   id = "uri_callback",
 *   label = @Translation("URI Callback"),
 * )
 */
class UriCallback extends PluginBase implements SubstitutionInterface {

  /**
   * {@inheritdoc}
   */
  public function getUrl(EntityInterface $entity) {
    return $entity->toUrl('canonical')->toString(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(EntityTypeInterface $entity_type) {
    return $entity_type->getUriCallback() !== NULL;
  }

}
