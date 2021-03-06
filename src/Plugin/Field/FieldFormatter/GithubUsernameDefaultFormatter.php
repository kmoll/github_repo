<?php

namespace Drupal\github_repo\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'country' formatter.
 *
 * @FieldFormatter(
 *   id = "github_username_default",
 *   module = "github_repo",
 *   label = @Translation("Github username"),
 *   field_types = {
 *     "github_username"
 *   }
 * )
 */
class GithubUsernameDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    foreach ($items as $delta => $item) {
      if (!empty($item->value)) {
        $link = 'https://github.com/' . $item->value;
        $markup = t('<a href=":link">@username</a>', array(':link' => $link, '@username' => $item->value));
        $elements[$delta] = array('#markup' => $markup);
      }
    }
    return $elements;
  }

}
