<?php

namespace Drupal\github_repo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\github_repo\Entity\GithubRepo;

/**
 * Provides form ot import Github repos.
 */
class GithubRepoImportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'github_repo_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['github_username'] = array(
      '#type' => 'textfield',
      '#title' => t('Github Username'),
      '#description' => t('Enter the user name of the repos to import.'),
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $username = $form_state->getValue('github_username');

    // Get the client service.
    $client = \Drupal::service('http_client');
    // Generate a Github URL.
    $url = sprintf('https://api.github.com/users/%s/repos', $username);
    // Make the request using the client service object.
    $request = $client->get($url);
    // Decode the JSON response.
    $repos   = json_decode((string) $request->getBody());

    foreach ($repos as $repo) {
      $values = array(
        'name' => $repo->name,
        'repo_id' => $repo->id,
        'repo_full_name' => $repo->full_name,
        'repo_description' => $repo->description,
      // Only public repos pulled.
        'repo_private' => FALSE,
        'repo_url' => $repo->html_url,
        'repo_created' => strtotime($repo->created_at),
        'repo_updated' => strtotime($repo->updated_at),
        'repo_pushed' => strtotime($repo->pushed_at),
        'repo_star_count' => $repo->stargazers_count,
        'repo_watcher_count' => $repo->watchers_count,
        'repo_forks_count' => $repo->forks_count,
      );

      GithubRepo::create($values)->save();
    }
  }

}
