<?php

/**
 * @file
 * Contains \Drupal\github_repo\GithubRepoListBuilder.
 */

namespace Drupal\github_repo;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Github repo entities.
 *
 * @ingroup github_repo
 */
class GithubRepoListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Github repo ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\github_repo\Entity\GithubRepo */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.github_repo.edit_form', array(
          'github_repo' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
