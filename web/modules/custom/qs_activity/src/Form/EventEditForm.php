<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * EventEditForm class.
 */
class EventEditForm extends EventEditFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_event_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $event = NULL) {
    $form = parent::buildForm($form, $form_state, $event);

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#title'] = $this->t('qs_activity.events.form.edit.title_form');
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'class' => [
        'modal-body',
      ],
      'bg' => 'secondary',
    ];

    $form['#theme_wrappers'] = [
      'form__fullpage',
    ];

    $form['title'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.events.form.edit.title'),
      '#placeholder'   => $this->t('qs_activity.events.form.edit.title.placeholder'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $event->title->value,
    ];

    // Load date from UTC in Drupal 8 to curent loggedin user Timezone.
    $start_at = $event->field_start_at->date;
    $start_at->setTimezone(new \DateTimeZone($this->currentUser->getTimezone()));
    $end_at = $event->field_end_at->date;
    $end_at->setTimezone(new \DateTimeZone($this->currentUser->getTimezone()));

    $form['date_fieldset'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => [
          'flex-wrap',
          'row',
        ],
      ],
      '#theme_wrappers' => [
        'container__date',
      ],
    ];

    $form['date_fieldset']['date'] = [
      '#attributes' => [
        'type' => 'date',
        'required' => TRUE,
        'class'          => [
          'flex-grow',
          'px-3',
          'mb-2',
        ],
        'icon' => 'calendar',
      ],
      '#title'         => $this->t('qs_activity.events.form.edit.date'),
      '#type'          => 'date',
      '#required'      => FALSE,
      '#default_value' => $start_at->format('Y-m-d'),
      '#size'          => 8,
    ];

    $form['date_fieldset']['time_fieldset'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => [
          'flex-grow',
          'flex-wrap',
          'mb-3',
        ],
      ],
      '#theme_wrappers' => [
        'container__date',
      ],
    ];

    $form['date_fieldset']['time_fieldset']['start_at'] = [
      '#attributes'    => [
        'type' => 'time',
        'required' => TRUE,
        'class' => [
          'flex-grow',
          'px-3',
        ],
        'icon' => 'watch',
      ],
      '#title'         => $this->t('qs_activity.events.form.edit.start_at'),
      '#type'          => 'date',
      '#required'      => FALSE,
      '#default_value' => $start_at->format('H:i'),
      '#size'          => 5,
    ];

    $form['date_fieldset']['time_fieldset']['end_at'] = [
      '#attributes'    => [
        'type' => 'time',
        'required' => TRUE,
        'class' => [
          'flex-grow',
          'px-3',
        ],
      ],
      '#title'         => $this->t('qs_activity.events.form.edit.end_at'),
      '#type'          => 'date',
      '#required'      => FALSE,
      '#default_value' => $end_at->format('H:i'),
      '#size'          => 5,
    ];

    $form['body'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.events.form.edit.body'),
      '#placeholder'   => $this->t('qs_activity.events.form.edit.body.placeholder'),
      '#type'          => 'textarea',
      '#required'      => FALSE,
      '#default_value' => $event->body->value,
    ];

    $form['venue'] = [
      '#attributes' => [
        'google-autocomplete' => TRUE,
        'google-input-lat' => 'edit-latitude',
        'google-input-lng' => 'edit-longitude',
      ],
      '#title'         => $this->t('qs_activity.events.form.edit.venue'),
      '#type'          => 'textfield',
      '#default_value' => $event->field_venue->value,
    ];
    $form['#attached']['library'][] = 'quartiers_solidaires/google-place-autocomplete';

    $form['latitude'] = [
      '#type'  => 'hidden',
      '#default_value' => $event->field_venue_lat->value,
    ];
    $form['longitude'] = [
      '#type'  => 'hidden',
      '#default_value' => $event->field_venue_long->value,
    ];

    $form['has_contribution'] = [
      '#type'        => 'radios',
      '#options'     => [0 => $this->t('qs.form.no'), 1 => $this->t('qs.form.yes')],
      '#required'      => FALSE,
      '#default_value' => 0,
      '#attributes' => [
        'title'   => $this->t('qs_activity.events.form.add.has_contribution'),
        'no_form_group' => TRUE,
        'data-toggle' => 'buttons',
        'color' => 'secondary',
        'variant' => 'button',
        'no_block' => TRUE,
        'class' => [
          'mb-2',
        ],
      ],
      '#theme_wrappers' => [
        'input__button_group',
      ],
    ];

    $form['contribution'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.events.form.edit.contribution'),
      '#type'          => 'textfield',
      '#default_value' => $event->field_contribution->value,
      '#required'    => FALSE,
      '#states' => [
        'visible' => [
          ':input[name="has_contribution"]' => ['value' => 1],
        ],
      ],
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
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
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the title is valid.
    if (!$form_state->getValue('title') || empty($form_state->getValue('title'))) {
      $form_state->setErrorByName('[title]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['title']['#title']]));
    }

    // Date validation
    // ===============.
    // Assert the date is valid.
    if (!$form_state->getValue('date') || empty($form_state->getValue('date'))) {
      $form_state->setErrorByName('[date_fieldset][date]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['date_fieldset']['date']['#title']]));
    }

    // Assert the start is valid.
    if (!$form_state->getValue('start_at') || empty($form_state->getValue('start_at'))) {
      $form_state->setErrorByName('[date_fieldset][time_fieldset][start_at]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['date_fieldset']['time_fieldset']['start_at']['#title']]));
    }

    // Assert the end is valid.
    if (!$form_state->getValue('end_at') || empty($form_state->getValue('end_at'))) {
      $form_state->setErrorByName('[date_fieldset][time_fieldset][end_at]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['date_fieldset']['time_fieldset']['end_at']['#title']]));
    }

    // Assert the start is formatted as requested.
    if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $form_state->getValue('start_at'))) {
      $form_state->setErrorByName('[date_fieldset][time_fieldset][start_at]', $this->t('qs_activity.events.form.add.error.hours.malformed @fieldname', ['@fieldname' => $form['date_fieldset']['time_fieldset']['start_at']['#title']]));
    }

    // Assert the end is formatted as requested.
    if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $form_state->getValue('end_at'))) {
      $form_state->setErrorByName('[date_fieldset][time_fieldset][end_at]', $this->t('qs_activity.events.form.add.error.hours.malformed @fieldname', ['@fieldname' => $form['date_fieldset']['time_fieldset']['end_at']['#title']]));
    }

    $now = new DrupalDateTime();
    $date = new DrupalDateTime($form_state->getValue('date'));
    $formatted_date = $date->format('d.m.Y');
    try {
      $start_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('start_at') . ':00');
      $end_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('end_at') . ':00');
    }
    catch (\Exception $e) {
      $form_state->setErrorByName('[event]', $this->t('qs.form.error.something_went_wrong'));
      return;
    }

    // Assert the date is formatted as requested.
    if (!$this->validateDate($formatted_date, 'd.m.Y')) {
      $form_state->setErrorByName('[date_fieldset][date]', $this->t('qs_activity.form.error.date_format_invalid @fieldname', ['@fieldname' => $form['date_fieldset']['date']['#title']]));
    }
    // Assert the date is in the future.
    elseif ($date < $now) {
      $form_state->setErrorByName('[date_fieldset][date]', $this->t('qs_activity.form.error.date_past @fieldname', ['@fieldname' => $form['date_fieldset']['date']['#title']]));
    }

    // Check hours are realistic.
    if ($start_at >= $end_at) {
      $form_state->setErrorByName('[date_fieldset][time_fieldset][start_at]', $this->t('qs_activity.events.form.add.error.hours.inconsistency @fieldname', ['@fieldname' => $form['date_fieldset']['time_fieldset']['start_at']['#title']]));
    }

    // Add inline errors.
    $this->applyErrorsInline($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $event = $this->nodeStorage->load($form_state->getValue('event'));

    // Format dates.
    $date = new DrupalDateTime($form_state->getValue('date'));
    $formatted_date = $date->format('d.m.Y');
    $start_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('start_at') . ':00');
    $end_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('end_at') . ':00');

    // Prepare fields.
    $fields['title']               = $form_state->getValue('title');
    $fields['body']                = $form_state->getValue('body');
    $fields['field_contact_mail']  = $form_state->getValue('contact_mail');
    $fields['field_contact_phone'] = $form_state->getValue('contact_phone');
    $fields['field_contribution']  = $form_state->getValue('contribution');
    $fields['field_venue']         = $form_state->getValue('venue');
    $fields['field_venue_lat']     = $form_state->getValue('latitude');
    $fields['field_venue_long']    = $form_state->getValue('longitude');

    // Update new event.
    $this->eventManager->update($event, $start_at, $end_at, $fields);

    drupal_set_message($this->t('qs_activity.events.form.edit.success @event', [
      '@event' => $event->getTitle(),
    ]));

    $form_state->setRedirect('qs_activity.events.form.edit', ['event' => $event->id()], []);
  }

}
