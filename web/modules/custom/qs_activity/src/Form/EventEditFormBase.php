<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * EventEditFormBase class.
 */
abstract class EventEditFormBase extends FormBasic {

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->currentUser  = $this->getCurrentUser();
    $this->acl          = $this->getAcl();
    $this->termStorage  = $this->getTermStorage();
    $this->nodeStorage  = $this->getNodeStorage();
    $this->eventManager = $this->getEventManager();
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $event
   *   Run access checks for this node.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account, NodeInterface $event) {
    $access = AccessResult::forbidden();

    // Get the related activity.
    $activity = $event->field_activity->entity;

    if ($activity && $this->acl->hasWriteAccessEvent($activity)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $event = NULL) {
    $form = parent::buildForm($form, $form_state);

    // Save the event for submisson.
    $form['event'] = [
      '#type'  => 'hidden',
      '#value' => $event->id(),
    ];

    return $form;
  }

}
