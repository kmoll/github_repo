<?php

/**
 * @file
 * Contains \Drupal\github_repo\Form\GithubRepoForm.
 */

namespace Drupal\github_repo\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Github repo edit forms.
 *
 * @ingroup github_repo
 */
class GithubRepoForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\github_repo\Entity\GithubRepo */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Github repo.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Github repo.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.github_repo.edit_form', ['github_repo' => $entity->id()]);
  }

}
