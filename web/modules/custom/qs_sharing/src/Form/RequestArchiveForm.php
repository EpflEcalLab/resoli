<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Manager\RequestManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to archive a request by the current logged-in user.
 */
class RequestArchiveForm extends FormBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The Offer Manager.
   *
   * @var \Drupal\qs_sharing\Manager\RequestManager
   */
  protected $requestManager;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, RequestManager $request_manager) {
    $this->acl = $acl;
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->requestManager = $request_manager;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $node
   *   Run access checks for this node.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $node) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasWriteAccessRequest($node) && $node->get('moderation_state')->value !== 'archived') {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
    if (!isset($options['node'])) {
      return $form;
    }

    // Needed to ensure the right offer is linked to the right form
    // https://drupal.stackexchange.com/a/276999
    $form_state->setRequestMethod('POST');
    $form_state->setCached(TRUE);

    /** @var \Drupal\node\NodeInterface $node */
    $node = $options['node'];

    // Save the request for later usage on submission.
    $form_state->set('node', $node->id());

    // Disable caching.
    $form['#cache']['max-age'] = 0;

    $form['#attributes'] = [
      'data-confirm' => 'true',
      'data-parent' => 'card' . $node->id(),
      'class' => [
        'request',
        'request' . $node->id(),
        'request-archive-form',
        'request-archive-form' . $node->id(),
        'archive',
        'mx-auto',
        'mb-3',
        'col-sm-6',
      ],
    ];
    $form['action']['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $this->t('qs_sharing.collection.request.archive'),
      '#attributes' => [
        'icon' => 'trash',
        'icon_left' => TRUE,
        'class' => [
          'btn',
          'btn-outline-invert',
          'btn-icon',
          'shadow-to-bottom',
          'btn-block',
          'bg-danger',
        ],
        'data-confirm' => $this->t('qs_sharing.collection.request.archive.confirmed'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('qs_acl.access_control'),
      $container->get('entity_type.manager'),
      $container->get('qs_sharing.manager.request')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_request_archive_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->nodeStorage->load($form_state->get('node'));

    // Archive the request.
    $this->requestManager->archive($node);

    $this->messenger()->addMessage($this->t('qs_sharing.collection.request.archive.success'));

    $form_state->setRedirect('qs_sharing.collection.request', [
      'community' => $node->field_community->target_id,
    ]);
  }

}
