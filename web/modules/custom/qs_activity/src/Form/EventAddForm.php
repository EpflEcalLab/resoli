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
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

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
    $this->termStorage  = $entity_type_manager->getStorage('taxonomy_term');
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
   * Checks access for creating file in the given rubric.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $activity
   *   Run access checks for this taxonomy.
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
    $form['event'] = [
      '#type'  => 'hidden',
      '#value' => $activity->id(),
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('qs_activity.add_form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Check date is in the futur.
    // Check hours are realistic.
    // Add inline errors.
    $this->applyErrorsInline($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->termStorage->load($form_state->getValue('activity'));

    // // Create the new event.
    // $activity = $this->eventManager->create($form_state->getValue('title'), $themes, $authorizations, $community);.
    // drupal_set_message($this->t("qs_activity.add_form.success @activity", [
    //   '@activity' => $activity->getTitle(),
    // ]));
    $form_state->setRedirect('entity.node.canonical', ['node' => $activity->id()], []);
  }

}
