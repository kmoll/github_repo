<?php

/**
 * @file
 * Contains \Drupal\github_repo\Entity\GithubRepo.
 */

namespace Drupal\github_repo\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Github repo entities.
 */
class GithubRepoViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['github_repo']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Github repo'),
      'help' => $this->t('The Github repo ID.'),
    );

    return $data;
  }

}
