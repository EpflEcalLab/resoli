<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;

/**
 * Activity form to update default values.
 */
class ActivityEditDefaultsForm extends ActivityEditFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?NodeInterface $activity = NULL) {
    $form = parent::buildForm($form, $form_state, $activity);

    $form['#theme_wrappers'] = [
      'form__modal',
    ];
    $form['#attributes'] = [
      'title' => $activity->title->value,
      'description' => $this->t('qs.activity.edit_defaults'),
      'novalidate' => 'novalidate',
      'theme' => 'primary',
    ];

    $form['#floating_buttons'][] = [
      'label' => $this->t('qs.activity.edit_default_values'),
      'icon' => 'activities',
      'active' => TRUE,
    ];

    $form['group'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => [
          'mb-5',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
      ],
    ];

    $form['group']['title'] = [
      '#title' => $this->t('qs_activity.activities.form.edit.defaults.titlefield'),
      '#placeholder' => $this->t('qs_activity.activities.form.edit.defaults.titlefield.placeholder'),
      '#type' => 'textfield',
      '#default_value' => $activity->field_default_title->value,
    ];

    $form['group']['body'] = [
      '#title' => $this->t('qs_activity.activities.form.edit.defaults.body'),
      '#placeholder' => $this->t('qs_activity.activities.form.edit.defaults.body.placeholder'),
      '#type' => 'textarea',
      '#default_value' => $activity->body->value,
    ];

    $form['group']['venue'] = [
      '#attributes' => [
        'google-autocomplete' => TRUE,
        'google-input-lat' => 'edit-latitude',
        'google-input-lng' => 'edit-longitude',
      ],
      '#title' => $this->t('qs_activity.activities.form.edit.defaults.venue'),
      '#placeholder' => $this->t('qs_activity.activities.form.edit.defaults.venue.placeholder'),
      '#type' => 'textfield',
      '#default_value' => $activity->field_venue->value,
    ];
    $form['group']['#attached']['library'][] = 'quartiers_solidaires/google-place-autocomplete';

    // Hidden fields which will be updated via Javascript.
    $form['group']['latitude'] = [
      '#type' => 'hidden',
      '#default_value' => $activity->field_venue_lat->value,
    ];
    $form['group']['longitude'] = [
      '#type' => 'hidden',
      '#default_value' => $activity->field_venue_long->value,
    ];

    $form['group']['contribution'] = [
      '#title' => $this->t('qs_activity.activities.form.edit.defaults.contribution'),
      '#placeholder' => $this->t('qs_activity.activities.form.edit.defaults.contribution.placeholder'),
      '#type' => 'textfield',
      '#default_value' => $activity->field_contribution->value,
    ];

    $form['group']['contact_name'] = [
      '#title' => $this->t('qs_activity.activities.form.edit.defaults.contact_name'),
      '#placeholder' => $this->t('qs_activity.activities.form.edit.defaults.contact_name.placeholder'),
      '#type' => 'textfield',
      '#default_value' => $activity->field_contact_name->value,
    ];

    $form['group']['contact_phone'] = [
      '#title' => $this->t('qs_activity.activities.form.edit.defaults.contact_phone'),
      '#placeholder' => $this->t('qs_activity.activities.form.edit.defaults.contact_phone.placeholder'),
      '#type' => 'tel',
      '#default_value' => $activity->field_contact_phone->value,
    ];

    $form['group']['contact_mail'] = [
      '#title' => $this->t('qs_activity.activities.form.edit.defaults.contact_mail'),
      '#placeholder' => $this->t('qs_activity.activities.form.edit.defaults.contact_mail.placeholder'),
      '#type' => 'email',
      // Skip drupal email validation.
      '#validated' => TRUE,
      '#default_value' => $activity->field_contact_mail->value,
      '#attributes' => [
        'required' => TRUE,
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => [
        'icon' => 'check',
        'modal' => TRUE,
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
        ],
      ],
      '#value' => $this->t('qs.form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_edit_defaults_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->get('activity'));

    $fields = [
      'field_default_title' => $form_state->getValue('title'),
      'body' => $form_state->getValue('body'),
      'field_contact_name' => $form_state->getValue('contact_name'),
      'field_contact_phone' => $form_state->getValue('contact_phone'),
      'field_contact_mail' => $form_state->getValue('contact_mail'),
      'field_venue' => $form_state->getValue('venue'),
      'field_venue_lat' => $form_state->getValue('latitude'),
      'field_venue_long' => $form_state->getValue('longitude'),
      'field_contribution' => $form_state->getValue('contribution'),
    ];

    // Update the activity.
    $activity = $this->activityManager->update($activity, $fields);

    $this->messenger()->addMessage($this->t('qs_activity.activities.form.edit.defaults.success @activity', [
      '@activity' => $activity->getTitle(),
    ]));

    $form_state->setRedirect('qs_activity.activities.dashboard', ['activity' => $activity->id()], []);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the email is valid.
    if (!$form_state->getValue('contact_mail') || empty($form_state->getValue('contact_mail'))) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.empty @mail', ['@mail' => $form['group']['contact_mail']['#title']]));
    }

    // Assert the mail is valid.
    if (!$form_state->getValue('contact_mail') || !filter_var($form_state->getValue('contact_mail'), \FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.mail.malformed'));
    }
  }

}
