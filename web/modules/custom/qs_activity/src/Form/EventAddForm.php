<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\qs_activity\Service\eventManager;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_site\Form\InlineErrorFormTrait;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * EventAddForm class.
 */
class EventAddForm extends FormBase {
  use InlineErrorFormTrait;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The node Storage.
   *
   * @var \Drupal\taxonomy\NodeStorageInterface
   */
  private $nodeStorage;

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\eventManager
   */
  protected $eventManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, eventManager $event_manager) {
    $this->acl          = $acl;
    $this->nodeStorage  = $entity_type_manager->getStorage('node');
    $this->eventManager = $event_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
    $container->get('qs_acl.access_control'),
    $container->get('entity_type.manager'),
    $container->get('qs_activity.event_manager')
    );
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
    if ($this->acl->hasWriteAccessActivity($activity)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $activity = NULL) {

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';

    // Save the community for submisson.
    $form['activity'] = [
      '#type'  => 'hidden',
      '#value' => $activity->id(),
    ];

    $form['event']['step-1'] = [
      '#type' => 'fieldset',
    ];

    $form['event']['step-1']['title'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.events.add_form.title'),
      '#placeholder'   => $this->t('qs_activity.events.add_form.title.placeholder'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $activity->title->value,
    ];

    $now = new DrupalDateTime();
    $form['event']['step-1']['date'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.events.add_form.date'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $now->format('d.m.Y'),
    ];

    $form['event']['step-1']['start_at'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.events.add_form.start_at'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $now->format('H:i'),
    ];

    $form['event']['step-1']['end_at'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.events.add_form.end_at'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $now->modify('+30 minutes')->format('H:i'),
    ];

    $form['event']['step-2'] = [
      '#type'  => 'fieldset',
    ];

    $form['event']['step-2']['body'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_activity.events.add_form.body'),
      '#placeholder'   => $this->t('qs_activity.events.add_form.body.placeholder'),
      '#type'          => 'textarea',
      '#required'      => FALSE,
      '#default_value' => $activity->body->value,
    ];

    $form['event']['step-2']['venue'] = [
      '#attributes' => [
        'required'              => TRUE,
        'google-autocomplete'     => TRUE,
        'google-input-lat' => 'edit-latitude',
        'google-input-lng' => 'edit-longitude',
      ],
      '#title'         => $this->t('qs_activity.events.add_form.venue'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $activity->field_venue->value,
    ];
    $form['#attached']['library'][] = 'quartiers_solidaires/google-place-autocomplete';

    // Save the community for submisson.
    $form['latitude'] = [
      '#type'  => 'hidden',
      '#value' => $activity->field_venue_lat->value,
    ];
    $form['longitude'] = [
      '#type'  => 'hidden',
      '#value' => $activity->field_venue_long->value,
    ];

    $form['event']['step-2']['has_contribution'] = [
      '#attributes'  => ['required' => TRUE],
      '#title'       => $this->t('qs_activity.events.add_form.has_contribution'),
      '#type'        => 'radios',
      '#options'     => [0 => $this->t('qs_activity.events.add_form.has_contribution.no'), 1 => $this->t('qs_activity.events.add_form.has_contribution.yes')],
      '#required'    => FALSE,
    ];

    $form['event']['step-2']['contribution'] = [
      '#attributes'  => ['required' => TRUE],
      '#title'       => $this->t('qs_activity.events.add_form.has_contribution'),
      '#type'        => 'textfield',
      '#required'    => FALSE,
    ];

    $form['event']['step-2']['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('qs_activity.events.add_form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the title is valid.
    if (!$form_state->getValue('title') || empty($form_state->getValue('title'))) {
      $form_state->setErrorByName('[event][step-1][title]', $this->t('qs_activity.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['title']['#title']]));
    }

    // Assert the date is valid.
    if (!$form_state->getValue('date') || empty($form_state->getValue('date'))) {
      $form_state->setErrorByName('[event][step-1][date]', $this->t('qs_activity.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['date']['#date']]));
    }

    // Assert the date is formatted as requested.
    $date = $form_state->getValue('date');
    $now = new DrupalDateTime();
    if (!$this->validateDate($date, 'd.m.Y') && !$this->validateDate($date, 'd.m.Y')) {
      $form_state->setErrorByName('[event][step-1][date]', $this->t('qs_activity.form.error.date_format_invalid'));

      // Assert the date is in the futur.
    }
    elseif ($date < $now) {
      $form_state->setErrorByName('[event][step-1][date]', $this->t('qs_activity.form.error.date_past'));
    }

    // Assert the start is valid.
    if (!$form_state->getValue('start_at') || empty($form_state->getValue('start_at'))) {
      $form_state->setErrorByName('[event][step-1][start_at]', $this->t('qs_activity.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['start_at']['#title']]));
    }

    // Assert the start is formatted as requested.
    if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $form_state->getValue('start_at'))) {
      $form_state->setErrorByName('[event][step-1][start_at]', $this->t('qs_activity.form.error.hours_format_invalid @fieldname', ['@fieldname' => $form['event']['step-1']['start_at']['#title']]));
    }

    // Assert the end is valid.
    if (!$form_state->getValue('end_at') || empty($form_state->getValue('end_at'))) {
      $form_state->setErrorByName('[event][step-1][end_at]', $this->t('qs_activity.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-1']['end_at']['#title']]));
    }

    // Assert the end is formatted as requested.
    if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $form_state->getValue('end_at'))) {
      $form_state->setErrorByName('[event][step-1][end_at]', $this->t('qs_activity.form.error.hours_format_invalid @fieldname', ['@fieldname' => $form['event']['step-1']['end_at']['#title']]));
    }

    // Check hours are realistic.
    $start_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $date . ' ' . $form_state->getValue('start_at') . ':00');
    $end_at = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $date . ' ' . $form_state->getValue('end_at') . ':00');
    if ($start_at >= $end_at) {
      $form_state->setErrorByName('[event][step-1][start_at]', $this->t('qs_activity.form.error.hours_inconsistency @fieldname', ['@fieldname' => $form['event']['step-1']['start_at']['#title']]));
    }

    // Assert the venue is valid.
    if (!$form_state->getValue('venue') || empty($form_state->getValue('venue'))) {
      $form_state->setErrorByName('[event][step-2][venue]', $this->t('qs_activity.form.error.empty @fieldname', ['@fieldname' => $form['event']['step-2']['venue']['#title']]));
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
    $date = $form_state->getValue('date');
    $date_start = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $date . ' ' . $form_state->getValue('start_at') . ':00');
    $date_end = DrupalDateTime::createFromFormat('d.m.Y H:i:s', $date . ' ' . $form_state->getValue('end_at') . ':00');

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
    $this->eventManager->create($activity, $date_start, $date_end, $data);
    drupal_set_message($this->t("qs_activity.events.add_form.success"));
    $form_state->setRedirect('entity.node.canonical', ['node' => $activity->id()], []);
  }

  /**
   * Shortest date&time validator for all formats.
   *
   * @param string $date
   *   The date to validate.
   * @param string $format
   *   The format to validate.
   *
   * @return bool
   *   Does the given date match the requested format or not.
   */
  private function validateDate($date, $format = 'Y-m-d H:i:s') {
    try {
      $d = DrupalDateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

}
