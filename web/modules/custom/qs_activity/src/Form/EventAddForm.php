<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * EventAddForm class.
 */
class EventAddForm extends FormBasic {

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->acl          = $this->getAcl();
    $this->nodeStorage  = $this->getNodeStorage();
    $this->eventManager = $this->getEventManager();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_event_add_form';
  }

  /**
   * Checks access for creating file in the given community.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $activity
   *   Run access checks for this node.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account, NodeInterface $activity) {
    $access = AccessResult::forbidden();
    if ($this->acl->hasWriteAccessEvent($activity)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $activity = NULL) {
    $form = parent::buildForm($form, $form_state);

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'class' => [
        'modal-body',
      ],
      'bg' => 'danger',
    ];

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__fullpage__multistep',
    ];

    // Save the activity for submisson.
    $form['activity'] = [
      '#type'  => 'hidden',
      '#value' => $activity->id(),
    ];

    $form['event']['step-1'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'data-step' => $this->t('qs_activity.events.form.step1'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['event']['step-1']['title'] = [
      '#attributes'    => [
        'required' => TRUE,
        'icon' => 'theme_' . $activity->field_theme->entity->field_icon->value,
      ],
      '#title'         => $this->t('qs_activity.events.form.add.title'),
      '#placeholder'   => $this->t('qs_activity.events.form.add.title.placeholder'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $activity->title->value,
    ];

    $form['event']['step-1']['date_fieldset'] = [
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

    $now = new DrupalDateTime();
    $form['event']['step-1']['date_fieldset']['date'] = [
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
      '#title'         => $this->t('qs_activity.events.form.add.date'),
      '#type'          => 'date',
      '#default_value' => $now->format('Y-m-d'),
      '#size'          => 8,
    ];

    $form['event']['step-1']['date_fieldset']['time_fieldset'] = [
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

    $form['event']['step-1']['date_fieldset']['time_fieldset']['start_at'] = [
      '#attributes'    => [
        'type' => 'time',
        'required' => TRUE,
        'class' => [
          'flex-grow',
          'px-3',
        ],
        'icon' => 'watch',
      ],
      '#title'         => $this->t('qs_activity.events.form.add.start_at'),
      '#type'          => 'date',
      '#required'      => FALSE,
      '#default_value' => $now->format('H:i'),
      '#size'          => 5,
    ];

    $form['event']['step-1']['date_fieldset']['time_fieldset']['end_at'] = [
      '#attributes'    => [
        'type' => 'time',
        'required' => TRUE,
        'class' => [
          'flex-grow',
          'px-3',
        ],
      ],
      '#title'         => $this->t('qs_activity.events.form.add.end_at'),
      '#type'          => 'date',
      '#required'      => FALSE,
      '#default_value' => $now->modify('+1 hour')->format('H:i'),
    ];

    $form['event']['step-2'] = [
      '#type'  => 'fieldset',
      '#attributes' => [
        'data-step' => $this->t('qs_activity.events.form.step2'),
      ],
      '#theme_wrappers' => [
        'container__center__wide',
        'fieldset__step',
      ],
    ];

    $form['event']['step-2']['body'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.events.form.add.body'),
      '#placeholder'   => $this->t('qs_activity.events.form.add.body.placeholder'),
      '#type'          => 'textarea',
      '#required'      => FALSE,
      '#default_value' => $activity->body->value,
    ];

    $form['event']['step-2']['venue'] = [
      '#attributes' => [
        'google-autocomplete' => TRUE,
        'google-input-lat' => 'edit-latitude',
        'google-input-lng' => 'edit-longitude',
      ],
      '#title'         => $this->t('qs_activity.events.form.add.venue'),
      '#type'          => 'textfield',
      '#default_value' => $activity->field_venue->value,
    ];
    $form['#attached']['library'][] = 'quartiers_solidaires/google-place-autocomplete';

    $form['event']['step-2']['latitude'] = [
      '#type'  => 'hidden',
      '#default_value' => $activity->field_venue_lat->value,
    ];
    $form['event']['step-2']['longitude'] = [
      '#type'  => 'hidden',
      '#default_value' => $activity->field_venue_long->value,
    ];

    $form['event']['step-2']['has_contribution'] = [
      '#type'    => 'radios',
      '#options' => [0 => $this->t('qs.form.no'), 1 => $this->t('qs.form.yes')],
      '#required'      => FALSE,
      '#default_value' => 0,
      '#attributes' => [
        'title'   => $this->t('qs_activity.events.form.add.has_contribution'),
        'no_form_group' => TRUE,
        'data-toggle' => 'buttons',
        'color' => 'danger',
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

    $form['event']['step-2']['contribution'] = [
      '#attributes'  => ['required' => TRUE],
      '#title'       => $this->t('qs_activity.events.form.add.contribution'),
      '#type'        => 'textfield',
      '#required'    => FALSE,
      '#states' => [
        'visible' => [
          ':input[name="has_contribution"]' => ['value' => 1],
        ],
      ],
    ];

    $form['event']['step-2']['actions']['submit'] = [
      '#type'  => 'submit',
      '#attributes' => [
        'icon' => 'check',
        'modal' => TRUE,
        'icon_left' => TRUE,
        'outline' => TRUE,
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
      $form_state->setErrorByName('[event][step-1][title]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['title']['#title']]));
    }

    // Date validation
    // ===============

    // Assert the date is valid.
    if (!$form_state->getValue('date') || empty($form_state->getValue('date'))) {
      $form_state->setErrorByName('[event][step-1][date]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['date']['#date']]));
    }

    $date = new DrupalDateTime($form_state->getValue('date'));
    $formatted_date = $date->format('d.m.Y');
    $start_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('start_at') . ':00');
    $end_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('end_at') . ':00');
    $now = new DrupalDateTime();

    // Assert the date is formatted as requested.
    if (!$this->validateDate($formatted_date, 'd.m.Y')) {
      $form_state->setErrorByName('[event][step-1][date]', $this->t('qs_activity.form.error.date_format_invalid'));
    }
    // Assert the date is in the future.
    elseif ($formatted_date < $now->format('d.m.Y')) {
      $form_state->setErrorByName('[event][step-1][date]', $this->t('qs_activity.form.error.date_past'));
    }

    // Assert the start is valid.
    if (!$form_state->getValue('start_at') || empty($form_state->getValue('start_at'))) {
      $form_state->setErrorByName('[event][step-1][start_at]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['start_at']['#title']]));
    }

    // Assert the start is formatted as requested.
    if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $form_state->getValue('start_at'))) {
      $form_state->setErrorByName('[event][step-1][start_at]', $this->t('qs_activity.events.form.add.error.hours.malformed @fieldname', ['@fieldname' => $form['event']['step-1']['start_at']['#title']]));
    }

    // Assert the end is valid.
    if (!$form_state->getValue('end_at') || empty($form_state->getValue('end_at'))) {
      $form_state->setErrorByName('[event][step-1][end_at]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['end_at']['#title']]));
    }

    // Assert the end is formatted as requested.
    if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $form_state->getValue('end_at'))) {
      $form_state->setErrorByName('[event][step-1][end_at]', $this->t('qs_activity.events.form.add.error.hours.malformed @fieldname', ['@fieldname' => $form['event']['step-1']['end_at']['#title']]));
    }

    // Check hours are realistic.
    if ($start_at >= $end_at) {
      $form_state->setErrorByName('[event][step-1][start_at]', $this->t('qs_activity.events.form.add.error.hours.inconsistency @fieldname', ['@fieldname' => $form['event']['step-1']['start_at']['#title']]));
    }

    // Add inline errors.
    $this->applyErrorsInline($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->getValue('activity'));

    // Format dates.
    $date = new DrupalDateTime($form_state->getValue('date'));
    $formatted_date = $date->format('d.m.Y');
    $start_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('start_at') . ':00');
    $end_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('end_at') . ':00');

    // Prepare data.
    $data['title'] = $form_state->getValue('title');
    $data['body'] = $form_state->getValue('body');
    $data['contact_mail'] = $form_state->getValue('contact_mail');
    $data['contact_phone'] = $form_state->getValue('contact_phone');
    $data['contribution'] = $form_state->getValue('contribution');
    $data['venue'] = $form_state->getValue('venue');
    $data['venue_lat'] = $form_state->getValue('venue_lat');
    $data['venue_long'] = $form_state->getValue('venue_long');

    // // Create the new event.
    $this->eventManager->create($activity, $start_at, $end_at, $data);
    drupal_set_message($this->t('qs_activity.events.form.add.success'));
    $form_state->setRedirect('entity.node.canonical', ['node' => $activity->id()], []);
  }

}
