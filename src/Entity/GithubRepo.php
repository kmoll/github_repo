<?php

/**
 * @file
 * Contains \Drupal\github_repo\Entity\GithubRepo.
 */

namespace Drupal\github_repo\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\link\LinkItemInterface;
use Drupal\github_repo\GithubRepoInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Github repo entity.
 *
 * @ingroup github_repo
 *
 * @ContentEntityType(
 *   id = "github_repo",
 *   label = @Translation("Github repo"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\github_repo\GithubRepoListBuilder",
 *     "views_data" = "Drupal\github_repo\Entity\GithubRepoViewsData",
 *     "form" = {
 *       "default" = "Drupal\github_repo\Form\GithubRepoForm",
 *       "add" = "Drupal\github_repo\Form\GithubRepoForm",
 *       "edit" = "Drupal\github_repo\Form\GithubRepoForm",
 *       "delete" = "Drupal\github_repo\Form\GithubRepoDeleteForm",
 *     },
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler",
 *     "access" = "Drupal\github_repo\GithubRepoAccessControlHandler",
 *   },
 *   base_table = "github_repo",
 *   data_table = "github_repo_field_data",
 *   revision_table = "github_repo_revision",
 *   revision_data_table = "github_repo_field_revision",
 *   admin_permission = "administer github repo entities",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "uid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/github_repo/{github_repo}",
 *     "add-form" = "/admin/structure/github_repo/add",
 *     "edit-form" = "/admin/structure/github_repo/{github_repo}/edit",
 *     "delete-form" = "/admin/structure/github_repo/{github_repo}/delete",
 *     "collection" = "/admin/structure/github_repo",
 *   },
 *   field_ui_base_route = "github_repo.settings"
 * )
 */
class GithubRepo extends ContentEntityBase implements GithubRepoInterface {
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the node owner the
    // revision author.
    if (!$this->getRevisionAuthor()) {
      $this->setRevisionAuthorId($this->getOwnerId());
    }

  }

  /**
   * {@inheritdoc}
   */
  public function preSaveRevision(EntityStorageInterface $storage, \stdClass $record) {
    parent::preSaveRevision($storage, $record);

    if (!$this->isNewRevision() && isset($this->original) && (!isset($record->revision_log) || $record->revision_log === '')) {
      // If we are updating an existing node without adding a new revision, we
      // need to make sure $entity->revision_log is reset whenever it is empty.
      // Therefore, this code allows us to avoid clobbering an existing log
      // entry with an empty one.
      $record->revision_log = $this->original->revision_log->value;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime() {
    return $this->get('revision_timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($timestamp) {
    $this->set('revision_timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionAuthor() {
    return $this->get('revision_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionAuthorId($uid) {
    $this->set('revision_uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Github repo entity.'))
      ->setReadOnly(TRUE);
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Github repo entity.'))
      ->setReadOnly(TRUE);
    $fields['vid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Revision ID'))
      ->setDescription(t('The node revision ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The username of the content author.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\github_repo\Entity\GithubRepo::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);


    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Github repo entity.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Github repo is published.'))
      ->setDefaultValue(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Github repo entity.'))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Revision timestamp'))
      ->setDescription(t('The time that the current revision was created.'))
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Revision user ID'))
      ->setDescription(t('The user ID of the author of the current revision.'))
      ->setSetting('target_type', 'user')
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_log'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Revision log message'))
      ->setDescription(t('Briefly describe the changes you have made.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('form', array(
        'type' => 'string_textarea',
        'weight' => 25,
        'settings' => array(
          'rows' => 4,
        ),
      ));

    $fields['repo_id']= BaseFieldDefinition::create('integer')
      ->setLabel(t('Github Repo ID'))
      ->setDescription(t('The ID of the Github repo (The gitbhub id).'))
      ->setReadOnly(TRUE);

    $fields['repo_full_name']  = BaseFieldDefinition::create('string')
      ->setLabel(t('Repo Name'))
      ->setDescription(t('The Full name of the github repo.'))
      ->setSettings(array(
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['repo_description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Repo Description'))
      ->setDescription(t('A description of the repo.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'text_textfield',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['repo_private'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Private Repo'))
      ->setDescription(t('A boolean indicating whether the Github repo is private.'))
      ->setDefaultValue(FALSE);

    $fields['repo_url'] = BaseFieldDefinition::create('link')
      ->setLabel(t('Repo URL'))
      ->setDescription(t('Github repo URL.'))
      ->setRequired(TRUE)
      ->setSettings(array(
        'link_type' => LinkItemInterface::LINK_GENERIC,
        'title' => DRUPAL_DISABLED,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'link_default',
        'weight' => -2,
      ));

    $fields['repo_created'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Repo Created'))
      ->setDescription(t('The time that the Github repo was created.'));

    $fields['repo_updated'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Repo Created'))
      ->setDescription(t('The time that the Github repo was updated.'));

    $fields['repo_pushed'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Repo Created'))
      ->setDescription(t('The time that the Github repo was pushed.'));

    $fields['repo_star_count']= BaseFieldDefinition::create('integer')
      ->setLabel(t('Github Repo ID'))
      ->setDescription(t('The number of stars for the repo.'));

    $fields['repo_watcher_count']= BaseFieldDefinition::create('integer')
      ->setLabel(t('Github Repo ID'))
      ->setDescription(t('The number of watchers for the repo.'));

    $fields['repo_forks_count']= BaseFieldDefinition::create('integer')
      ->setLabel(t('Github Repo ID'))
      ->setDescription(t('The number of forks for the repo.'));

    return $fields;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return array(\Drupal::currentUser()->id());
  }

}
