<?php

namespace Drupal\github_repo;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Github repo entity.
 *
 * @see \Drupal\github_repo\Entity\GithubRepo.
 */
class GithubRepoAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\github_repo\GithubRepoInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished github repo entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published github repo entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit github repo entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete github repo entities');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add github repo entities');
  }

}
