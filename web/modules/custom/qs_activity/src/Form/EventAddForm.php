<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to create event on activity.
 */
class EventAddForm extends FormBasic {

  /**
   * The Badge Manager.
   *
   * @var \Drupal\qs_badge\Service\BadgeManager
   */
  protected $badgeManager;

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  protected $subscriptionManager;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->acl = $this->getAcl();
    $this->nodeStorage = $this->getNodeStorage();
    $this->eventManager = $this->getEventManager();
    $this->subscriptionManager = $this->getSubscriptionManager();
    $this->badgeManager = $this->getBadgeManager();
  }

  /**
   * Checks access for creating file in the given community.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $activity
   *   Run access checks for this node.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
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
  public function buildForm(array $form, FormStateInterface $form_state, ?NodeInterface $activity = NULL) {
    $form = parent::buildForm($form, $form_state);

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'theme' => 'secondary',
    ];
    $form['#attached']['library'][] = 'qs_site/unload';

    $form['#floating_buttons'][] = [
      'label' => $this->t('qs_activity.floating.add.event'),
      'icon' => 'plus',
      'active' => TRUE,
    ];

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__modal__multistep',
    ];

    // Save the activity for submission.
    $form_state->set('activity', $activity->id());

    $form['event']['step-1'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_activity.events.form.step1.description @activity', [
        '@activity' => $activity->getTitle(),
      ]),
      '#attributes' => [
        'data-step' => $this->t('qs_activity.events.form.step1'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['event']['step-1']['title'] = [
      '#attributes' => [
        'required' => TRUE,
        'icon' => 'theme_' . $activity->field_theme->entity->field_icon->value,
      ],
      '#title' => $this->t('qs_activity.events.form.add.title'),
      '#placeholder' => $this->t('qs_activity.events.form.add.title.placeholder'),
      '#type' => 'textfield',
      '#required' => FALSE,
      '#default_value' => $activity->field_default_title->value,
    ];

    $form['event']['step-1']['date_fieldset'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => [
          'flex-wrap',
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
        'class' => [
          'flex-grow',
          'px-3',
          'mb-2',
        ],
        'icon' => 'calendar',
      ],
      '#title' => $this->t('qs_activity.events.form.add.date'),
      '#type' => 'date',
      '#default_value' => $now->format('d.m.Y'),
      '#size' => 8,
      '#date_date_format' => 'd.m.Y',
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
      '#attributes' => [
        'type' => 'time',
        'required' => TRUE,
        'class' => [
          'flex-grow',
          'px-3',
        ],
        'icon' => 'watch',
      ],
      '#title' => $this->t('qs_activity.events.form.add.start_at'),
      '#type' => 'date',
      '#required' => FALSE,
      '#default_value' => $now->format('H:i'),
      '#size' => 5,
    ];

    $form['event']['step-1']['date_fieldset']['time_fieldset']['end_at'] = [
      '#attributes' => [
        'type' => 'time',
        'required' => TRUE,
        'class' => [
          'flex-grow',
          'px-3',
        ],
      ],
      '#title' => $this->t('qs_activity.events.form.add.end_at'),
      '#type' => 'date',
      '#required' => FALSE,
      '#default_value' => $now->modify('+1 hour')->format('H:i'),
      '#size' => 5,
    ];

    $form['event']['step-2'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_activity.events.form.step2.description @activity', [
        '@activity' => $activity->getTitle(),
      ]),
      '#attributes' => [
        'data-step' => $this->t('qs_activity.events.form.step2'),
      ],
      '#theme_wrappers' => [
        'container__center__wide',
        'fieldset__step',
      ],
    ];

    $form['event']['step-2']['body'] = [
      '#attributes' => ['required' => TRUE],
      '#title' => $this->t('qs_activity.events.form.add.body'),
      '#placeholder' => $this->t('qs_activity.events.form.add.body.placeholder'),
      '#type' => 'textarea',
      '#required' => FALSE,
      '#default_value' => $activity->body->value,
    ];

    $form['event']['step-2']['venue'] = [
      '#attributes' => [
        'google-autocomplete' => TRUE,
        'google-input-lat' => 'edit-latitude',
        'google-input-lng' => 'edit-longitude',
      ],
      '#title' => $this->t('qs_activity.events.form.add.venue'),
      '#placeholder' => $this->t('qs_activity.events.form.add.venue.placeholder'),
      '#type' => 'textfield',
      '#default_value' => $activity->field_venue->value,
    ];
    $form['#attached']['library'][] = 'quartiers_solidaires/google-place-autocomplete';

    // Hidden fields which will be updated via Javascript.
    $form['event']['step-2']['latitude'] = [
      '#type' => 'hidden',
      '#default_value' => $activity->field_venue_lat->value ? $activity->field_venue_lat->value : NULL,
    ];
    $form['event']['step-2']['longitude'] = [
      '#type' => 'hidden',
      '#default_value' => $activity->field_venue_long->value ? $activity->field_venue_long->value : NULL,
    ];

    $form['event']['step-2']['contact_name'] = [
      '#title' => $this->t('qs_activity.events.form.edit.contact_name'),
      '#placeholder' => $this->t('qs_activity.events.form.edit.contact_name.placeholder'),
      '#type' => 'textfield',
      '#default_value' => $activity->field_contact_name->value,
    ];

    $form['event']['step-2']['contact_phone'] = [
      '#title' => $this->t('qs_activity.events.form.edit.contact_phone'),
      '#placeholder' => $this->t('qs_activity.events.form.edit.contact_phone.placeholder'),
      '#type' => 'tel',
      '#default_value' => $activity->field_contact_phone->value,
    ];

    $form['event']['step-2']['contact_mail'] = [
      '#title' => $this->t('qs_activity.events.form.edit.contact_mail'),
      '#placeholder' => $this->t('qs_activity.events.form.edit.contact_mail.placeholder'),
      '#type' => 'email',
      // Skip drupal email validation.
      '#validated' => TRUE,
      '#default_value' => $activity->field_contact_mail->value,
    ];

    $form['event']['step-2']['has_contribution'] = [
      '#type' => 'radios',
      '#options' => [0 => $this->t('qs.form.no'), 1 => $this->t('qs.form.yes')],
      '#required' => FALSE,
      '#default_value' => !empty($activity->field_contribution->value) ? 1 : 0,
      '#attributes' => [
        'title' => $this->t('qs_activity.events.form.add.has_contribution'),
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
      '#attributes' => ['required' => TRUE],
      '#title' => $this->t('qs_activity.events.form.add.contribution'),
      '#placeholder' => $this->t('qs_activity.events.form.add.contribution.placeholder'),
      '#type' => 'textfield',
      '#default_value' => $activity->field_contribution->value,
      '#required' => FALSE,
      '#states' => [
        'visible' => [
          ':input[name="has_contribution"]' => ['value' => '1'],
        ],
      ],
    ];

    // Fix padding bottom which is broken sometimes & overflow with submit.
    $form['event']['step-2']['spacer'] = [
      '#markup' => '<div class="mb-5"></div>',
    ];

    $form['event']['step-2']['actions']['submit'] = [
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
    return 'qs_activity_event_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->get('activity'));

    // Format dates.
    $date = new DrupalDateTime($form_state->getValue('date'));
    $formatted_date = $date->format('d.m.Y');
    $start_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('start_at') . ':00');
    $end_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('end_at') . ':00');

    // Prepare data.
    $data['title'] = $form_state->getValue('title');
    $data['body'] = $form_state->getValue('body');
    $data['contact_name'] = $form_state->getValue('contact_name');
    $data['contact_mail'] = $form_state->getValue('contact_mail');
    $data['contact_phone'] = $form_state->getValue('contact_phone');
    $data['contribution'] = $form_state->getValue('has_contribution') ? $form_state->getValue('contribution') : NULL;
    $data['venue'] = $form_state->getValue('venue');
    $data['venue_lat'] = $form_state->getValue('latitude');
    $data['venue_long'] = $form_state->getValue('longitude');

    // Create the new event.
    $event = $this->eventManager->create($activity, $start_at, $end_at, $data);
    $this->messenger()->addMessage($this->t('qs_activity.events.form.add.success'));
    $form_state->setRedirect('entity.node.canonical', ['node' => $activity->id()], ['fragment' => 'card' . $event->id()]);

    // Get the current user activitiy's privilege to this event.
    $privileges_by_events = $this->badgeManager->getPrivilegesByEvents([$event]);
    $privileges = reset($privileges_by_events);

    // According the current user roles to the event,
    // If it's activity_maintainers and not activity_organizers, then
    // subscribe it to this new event.
    if ($privileges && \in_array('activity_maintainers', $privileges, TRUE) && !\in_array('activity_organizers', $privileges, TRUE)) {
      // By default, subscribe every activity_maintainers (co-organizers) to
      // there events.
      $subscription = $this->subscriptionManager->request($event, NULL, FALSE);
      $this->subscriptionManager->confirm($subscription);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the title is valid.
    if (!$form_state->getValue('title') || empty($form_state->getValue('title'))) {
      $form_state->setErrorByName('title', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['title']['#title']]));
    }

    // Assert the mail is valid - only when filled.
    if ($form_state->getValue('contact_mail') && !filter_var($form_state->getValue('contact_mail'), \FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('contact_mail', $this->t('qs.form.error.mail.malformed'));
    }

    // Date validation
    // ===============.
    // Assert the date is valid.
    if (!$form_state->getValue('date') || empty($form_state->getValue('date'))) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['date_fieldset']['date']['#title']]));
    }

    // Assert the start is valid.
    if (!$form_state->getValue('start_at') || empty($form_state->getValue('start_at'))) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['date_fieldset']['time_fieldset']['start_at']['#title']]));
    }

    // Assert the end is valid.
    if (!$form_state->getValue('end_at') || empty($form_state->getValue('end_at'))) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['date_fieldset']['time_fieldset']['end_at']['#title']]));
    }

    // Assert the start is formatted as requested.
    if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $form_state->getValue('start_at'))) {
      $form_state->setErrorByName('form', $this->t('qs_activity.events.form.add.error.hours.malformed @fieldname', ['@fieldname' => $form['event']['step-1']['date_fieldset']['time_fieldset']['start_at']['#title']]));
    }

    // Assert the end is formatted as requested.
    if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $form_state->getValue('end_at'))) {
      $form_state->setErrorByName('form', $this->t('qs_activity.events.form.add.error.hours.malformed @fieldname', ['@fieldname' => $form['event']['step-1']['date_fieldset']['time_fieldset']['end_at']['#title']]));
    }

    $date = new DrupalDateTime($form_state->getValue('date'));
    $formatted_date = $date->format('d.m.Y');

    try {
      $start_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('start_at') . ':00');
      $end_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $formatted_date . ' ' . $form_state->getValue('end_at') . ':00');
    }
    catch (\Exception $e) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.something_went_wrong'));

      return;
    }

    // Assert the date is formatted as requested.
    if (!$this->validateDate($formatted_date, 'd.m.Y')) {
      $form_state->setErrorByName('form', $this->t('qs_activity.form.error.date_format_invalid @fieldname', ['@fieldname' => $form['event']['step-1']['date_fieldset']['date']['#title']]));
    }

    // Check hours are realistic.
    if ($start_at >= $end_at) {
      $form_state->setErrorByName('form', $this->t('qs_activity.events.form.add.error.hours.inconsistency @fieldname', ['@fieldname' => $form['event']['step-1']['date_fieldset']['time_fieldset']['start_at']['#title']]));
    }
  }

}
