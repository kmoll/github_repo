<?php

namespace Drupal\github_repo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;

/**
 * Display Repos.
 *
 * @Block(
 *   id = "github_repo_display_block",
 *   admin_label = @Translation("Github Repo Display"),
 *   category = @Translation("Blocks")
 * )
 */
class GithubRepoDisplayBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the latest 5 repos.
    $ids = \Drupal::entityQuery('github_repo')
      ->sort('created', 'DESC')
      ->range(0, 5)
      ->execute();

    $entities = \Drupal::entityTypeManager()->getStorage('github_repo')->loadMultiple($ids);

    $header = array(
      'name' => t('Name'),
      'watchers' => t('Watchers'),
      'stars' => t('Stars'),
      'forks' => t('Forks'),
    );

    foreach ($entities as $entity) {

      $url = $entity->toUrl('canonical');
      $rows[] = array(
        'name' => Link::fromTextAndUrl($entity->repo_full_name->value, $url),
        'watchers' => $entity->repo_watcher_count->value,
        'stars' => $entity->repo_star_count->value,
        'forks' => $entity->repo_forks_count->value,
      );
    }

    $build['display_table'] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    );

    return $build;

  }

}
