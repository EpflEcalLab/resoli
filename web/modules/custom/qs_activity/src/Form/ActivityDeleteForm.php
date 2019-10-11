<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Url;

/**
 * ActivityDeleteForm class.
 */
class ActivityDeleteForm extends ActivityEditFormBase {

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
    return 'qs_activity_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $activity = NULL) {
    $form = parent::buildForm($form, $form_state, $activity);

    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    $form['#attributes'] = [
      'title'       => $activity->title->value,
      'description' => $this->t('qs_activity.activities.form.delete.warning'),
      'icon'        => 'trash',
      'theme'       => 'danger',
    ];

    $form['#floating_buttons'][] = [
      'label' => $this->t('qs.activity.delete'),
      'icon' => 'trash',
      'active' => TRUE,
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
      '#url' => Url::fromRoute('qs_activity.activities.dashboard', ['activity' => $activity->id()]),
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
    $activity = $this->nodeStorage->load($form_state->get('activity'));

    // Assert the activity has no event.
    $events = $this->eventManager->getAll($activity);
    if (!empty($events)) {
      $form_state->setError($form, $this->t("qs_activity.activities.form.delete.error.has_events @activity", ['@activity' => $activity->toLink($activity->getTitle())->toString()]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->get('activity'));
    $community = $activity->field_community->entity;

    drupal_set_message($this->t("qs_activity.activities.form.delete.success @activity", [
      '@activity' => $activity->getTitle(),
    ]));

    $form_state->setRedirect('qs_activity.collection.themes', ['community' => $community->id()], []);

    // Delete the activity.
    $activity = $activity->delete();
  }

}
