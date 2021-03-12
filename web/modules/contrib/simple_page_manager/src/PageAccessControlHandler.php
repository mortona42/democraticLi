<?php

namespace Drupal\simple_page_manager;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access control handler for pages.
 */
class PageAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($operation === 'view') {
      $access = $entity->getAccess();

      if ($access['type'] === 'none') {
        return AccessResult::allowed();
      }

      if ($access['type'] === 'role') {
        $role = $access['options']['role'];
        $cache_context = 'user.roles:' . $role;
        return AccessResult::allowedIf(in_array($role, $account->getRoles()))->addCacheContexts([$cache_context]);
      }

      if ($access['type'] === 'permission') {
        $permission = $access['options']['permission'];
        return AccessResult::allowedIfHasPermission($account, $permission);
      }
    }

    return parent::checkAccess($entity, $operation, $account);
  }

}
