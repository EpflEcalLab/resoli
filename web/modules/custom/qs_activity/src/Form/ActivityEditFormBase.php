<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_site\Form\InlineErrorFormTrait;

/**
 * ActivityEditFormBase class.
 */
abstract class ActivityEditFormBase extends FormBase {
  use InlineErrorFormTrait;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected $termStorage;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, ActivityManager $activity_manager) {
    $this->acl             = $acl;
    $this->termStorage     = $entity_type_manager->getStorage('taxonomy_term');
    $this->nodeStorage     = $entity_type_manager->getStorage('node');
    $this->activityManager = $activity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
    $container->get('qs_acl.access_control'),
    $container->get('entity_type.manager'),
    $container->get('qs_activity.activity_manager')
    );
  }

  /**
   * Checks access.
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

    return $form;
  }

}
