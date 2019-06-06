<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Url;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * EventDeleteForm class.
 */
class EventDeleteForm extends EventEditFormBase {
  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->eventManager = $this->getEventManager();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_event_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $event = NULL) {
    $form = parent::buildForm($form, $form_state, $event);

    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    $form['#attributes'] = [
      'title'       => $event->title->value,
      'description' => $this->t('qs_activity.events.form.delete.warning'),
      'icon'        => 'trash',
      'theme'       => 'danger',
    ];

    $form['#floating_buttons'][] = [
      'label' => $this->t('qs.event.delete'),
      'icon' => 'trash',
      'active' => TRUE,
      'theme' => 'danger',
    ];

    $form['actions'] = [
      '#type' => 'fieldset',
      '#theme_wrappers' => [
        'container__center',
      ],
      '#attributes' => [
        'class' => [
          'text-center',
        ],
      ],
    ];

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('qs.form.cancel'),
      '#url' => Url::fromRoute('qs_activity.events.dashboard', ['event' => $event->id()]),
      '#attributes' => [
        'class' => [
          'btn btn-outline-danger btn-outline-invert',
        ],
      ],
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#attributes' => [
        'class' => [
          'text-danger',
        ],
        'icon' => 'trash',
        'icon_left' => TRUE,
        'white' => TRUE,
      ],
      '#value' => $this->t('qs.form.delete_submit'),
    ];

    // Remove unload script.
    $form['#attached']['library'] = [];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $event = $this->nodeStorage->load($form_state->get('event'));
    $now = new DrupalDateTime();

    // Assert the event has not started.
    if ($event->field_start_at->date <= $now) {
      $form_state->setError($form, $this->t("qs_activity.events.form.delete.error.is_past @event", ['@event' => $event->toLink($event->getTitle())->toString()]));
    }

    // TODO Assert the event has no subscriber(s).
    // Add inline errors.
    $this->applyErrorsInline($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $event = $this->nodeStorage->load($form_state->get('event'));
    $activity = $event->field_activity->entity;

    $this->eventManager->sendDeleted($event, $this->currentUser->getAccount());

    drupal_set_message($this->t("qs_event.events.form.delete.success @event @date_start @date_end @hour_start @hour_end", [
      '@event'      => $event->getTitle(),
      '@date_start' => $event->field_start_at->date->format('j F Y'),
      '@hour_start' => $event->field_start_at->date->format('H\hi'),
      '@date_end'   => $event->field_end_at->date->format('j F Y'),
      '@hour_end'   => $event->field_end_at->date->format('H\hi'),
    ]));

    $form_state->setRedirect('entity.node.canonical', ['node' => $activity->id()], []);

    // Delete the event.
    $event = $event->delete();
  }

}
