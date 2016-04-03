<?php

/**
 * @file
 * Contains github_repo.page.inc..
 *
 * Page callback for Github repo entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Github repo templates.
 *
 * Default template: github_repo.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_github_repo(array &$variables) {
  // Fetch GithubRepo Entity Object.
  $github_repo = $variables['elements']['#github_repo'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
