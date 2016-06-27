<?php

namespace Drupal\github_repo\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Plugin implementation of the 'github_user' field type.
 *
 * @FieldType(
 *   id = "github_username",
 *   label = @Translation("Github username"),
 *   description = @Translation("Stores a github user name."),
 *   category = @Translation("Custom"),
 *   default_widget = "gihtub_username_default",
 *   default_formatter = "github_username_default"
 * )
 */
class GithubUsernameItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'char',
          'length' => 255,
          'not null' => FALSE,
        ),
      ),
      'indexes' => array(
        'value' => array('value'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Github username'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();
    $constraints[] = $constraint_manager->create('ComplexData', array(
      'value' => array(
        'Length' => array(
          'max' => 255,
          'maxMessage' => t('%name: github username may not be longer than @max characters.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => 255)),
        ),
      ),
    ));
    return $constraints;
  }

}
