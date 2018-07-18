<?php

namespace Drupal\qs_auth\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'qs_auth.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_auth_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('qs_auth.settings');

    $form['demo_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Demo mode'),
      '#description' => $this->t('Will disable the logout buttons.'),
      '#default_value' => $config->get('demo_mode'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('qs_auth.settings')
      ->set('demo_mode', $form_state->getValue('demo_mode'))
      ->save();
  }

}
