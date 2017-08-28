<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;

/**
 * ActivityEditInfoForm class.
 */
class ActivityEditInfoForm extends ActivityEditFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_edit_info_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $activity = NULL) {

    $form = parent::buildForm($form, $form_state, $activity);

    $form['step-1'] = [
      '#type' => 'fieldset',
    ];

    $form['step-1']['title'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.edit_info_form.title'),
      '#placeholder'   => $this->t('qs_activity.edit_info_form.title.placeholder'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $activity->getTitle(),
    ];

    $form['step-2'] = [
      '#type'  => 'fieldset',
    ];

    // Get all themes for options.
    $themes = $this->termStorage->loadTree('themes', 0, NULL, TRUE);
    $options = [];
    foreach ($themes as $theme) {
      $options[$theme->id()] = $theme->getName();
    }
    $form['step-2']['theme'] = [
      '#attributes' => [
        'required' => TRUE,
        'title'    => $this->t('qs_activity.edit_info_form.theme'),
      ],
      '#type'          => 'radios',
      '#required'      => FALSE,
      '#options'       => $options,
      '#default_value' => $activity->field_theme->target_id,
    ];

    $form['step-2']['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('qs_activity.edit_info_form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the title is valid.
    if (!$form_state->getValue('title') || empty($form_state->getValue('title'))) {
      $form_state->setErrorByName('[step-1][title]', $this->t('qs_activity.form.error.empty @fieldname', ['@fieldname' => $form['step-1']['title']['#title']]));
    }

    // Assert the theme is valid.
    if (!$form_state->getValue('theme') || empty($form_state->getValue('theme'))) {
      $form_state->setErrorByName('[step-2][theme]', $this->t('qs_activity.form.error.empty @fieldname', ['@fieldname' => $form['step-2']['theme']['#attributes']['title']]));
    }

    // Add inline errors.
    $this->applyErrorsInline($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->getValue('activity'));

    $fields = [
      'title'  => $form_state->getValue('title'),
      'field_theme' => [$form_state->getValue('theme')],
    ];

    // Create the new activity.
    $activity = $this->activityManager->update($activity, $fields);

    drupal_set_message($this->t("qs_activity.edit_info_form.success @activity", [
      '@activity' => $activity->getTitle(),
    ]));

    $form_state->setRedirect('qs_activity.activities.form.edit', ['activity' => $activity->id()], []);
  }

}
