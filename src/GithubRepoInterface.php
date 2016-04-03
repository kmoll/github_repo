<?php

/**
 * @file
 * Contains \Drupal\github_repo\GithubRepoInterface.
 */

namespace Drupal\github_repo;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Github repo entities.
 *
 * @ingroup github_repo
 */
interface GithubRepoInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Github repo name.
   *
   * @return string
   *   Name of the Github repo.
   */
  public function getName();

  /**
   * Sets the Github repo name.
   *
   * @param string $name
   *   The Github repo name.
   *
   * @return \Drupal\github_repo\GithubRepoInterface
   *   The called Github repo entity.
   */
  public function setName($name);

  /**
   * Gets the Github repo creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Github repo.
   */
  public function getCreatedTime();

  /**
   * Sets the Github repo creation timestamp.
   *
   * @param int $timestamp
   *   The Github repo creation timestamp.
   *
   * @return \Drupal\github_repo\GithubRepoInterface
   *   The called Github repo entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Github repo published status indicator.
   *
   * Unpublished Github repo are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Github repo is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Github repo.
   *
   * @param bool $published
   *   TRUE to set this Github repo to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\github_repo\GithubRepoInterface
   *   The called Github repo entity.
   */
  public function setPublished($published);

}
