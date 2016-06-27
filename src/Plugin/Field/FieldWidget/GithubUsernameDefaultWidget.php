<?php

namespace Drupal\github_repo\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'github_username_default' widget.
 *
 * @FieldWidget(
 *   id = "ihtub_username_default",
 *   label = @Translation("Github username field."),
 *   field_types = {
 *     "github_username"
 *   }
 * )
 */
class GithubUsernameDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $countries = \Drupal::service('country_manager')->getList();
    $element['value'] = $element + array(
      '#type' => 'testfield',
      '#empty_value' => '',
      '#default_value' => (isset($items[$delta]->value)) ? $items[$delta]->value : NULL,
      '#description' => t('Enter your github username'),
    );

    return $element;
  }

}
