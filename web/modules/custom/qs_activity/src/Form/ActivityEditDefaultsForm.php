<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;

/**
 * ActivityEditDefaultsForm class.
 */
class ActivityEditDefaultsForm extends ActivityEditFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_edit_defaults_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $activity = NULL) {

    $form = parent::buildForm($form, $form_state, $activity);

    $form['body'] = [
      '#title'         => $this->t('qs_activity.activities.form.edit.defaults.body'),
      '#placeholder'   => $this->t('qs_activity.activities.form.edit.defaults.body.placeholder'),
      '#type'          => 'textarea',
      '#default_value' => $activity->body->value,
    ];

    $form['contact_phone'] = [
      '#title'         => $this->t('qs_activity.activities.form.edit.defaults.contact_phone'),
      '#placeholder'   => $this->t('qs_activity.activities.form.edit.defaults.contact_phone.placeholder'),
      '#type'          => 'textfield',
      '#default_value' => $activity->field_contact_phone->value,
    ];

    $form['contact_mail'] = [
      '#title'         => $this->t('qs_activity.activities.form.edit.defaults.contact_mail'),
      '#placeholder'   => $this->t('qs_activity.activities.form.edit.defaults.contact_mail.placeholder'),
      '#type'          => 'textfield',
      '#default_value' => $activity->field_contact_mail->value,
    ];

    $form['venue'] = [
      '#attributes' => [
        'google-autocomplete'     => TRUE,
        'google-input-lat' => 'edit-latitude',
        'google-input-lng' => 'edit-longitude',
      ],
      '#title'         => $this->t('qs_activity.activities.form.edit.defaults.venue'),
      '#type'          => 'textfield',
      '#default_value' => $activity->field_venue->value,
    ];
    $form['#attached']['library'][] = 'quartiers_solidaires/google-place-autocomplete';

    // Save the community for submisson.
    $form['latitude'] = [
      '#type'  => 'hidden',
      '#default_value' => $activity->field_venue_lat->value,
    ];
    $form['longitude'] = [
      '#type'  => 'hidden',
      '#default_value' => $activity->field_venue_long->value,
    ];

    $form['contribution'] = [
      '#title' => $this->t('qs_activity.activities.form.edit.defaults.contribution'),
      '#type'  => 'textfield',
      '#default_value' => $activity->field_contribution->value,
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('qs_activity.activities.form.edit.defaults.submit'),
    ];

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
    $activity = $this->nodeStorage->load($form_state->getValue('activity'));

    $fields = [
      'body'                => $form_state->getValue('body'),
      'field_contact_phone' => $form_state->getValue('contact_phone'),
      'field_contact_mail'  => $form_state->getValue('contact_mail'),
      'field_venue'         => $form_state->getValue('venue'),
      'field_venue_lat'     => $form_state->getValue('latitude'),
      'field_venue_long'    => $form_state->getValue('longitude'),
      'field_contribution'  => $form_state->getValue('contribution'),
    ];

    // Update the activity.
    $activity = $this->activityManager->update($activity, $fields);

    drupal_set_message($this->t("qs_activity.activities.form.edit.defaults.success @activity", [
      '@activity' => $activity->getTitle(),
    ]));

    $form_state->setRedirect('qs_activity.activities.form.edit', ['activity' => $activity->id()], []);
  }

}
