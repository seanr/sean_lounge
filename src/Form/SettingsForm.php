<?php

namespace Drupal\spotify_api\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Configure Spotify API settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spotify_api_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['spotify_api.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Spotify API client ID'),
      '#default_value' => $this->config('spotify_api.settings')->get('client_id'),
      '#description' => $this->t('The client ID for the Spotify API. Create an app at the %link to get one.', ['%link' => Link::fromTextAndUrl($this->t('Spotify Developer Dashboard'), Url::fromUri('https://developer.spotify.com/dashboard/applications'))->toString()]),
    ];
    $form['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Spotify API client secret'),
      '#default_value' => $this->config('spotify_api.settings')->get('client_secret'),
      '#description' => $this->t('The client secret for the Spotify API.'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('client_id'))) {
      $form_state->setErrorByName('client_id', $this->t('The client ID is required.'));
    }
    if (empty($form_state->getValue('client_secret'))) {
      $form_state->setErrorByName('client_secret', $this->t('The client secret is required.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('spotify_api.settings')
      ->set('client_id', $form_state->getValue('client_id'))
      ->set('client_secret', $form_state->getValue('client_secret'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
