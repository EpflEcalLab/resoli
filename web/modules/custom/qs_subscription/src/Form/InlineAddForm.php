<?php

namespace Drupal\qs_subscription\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\qs_activity\Form\FormBasic;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Inline form use by privileged user to manually subscribe member.
 */
class InlineAddForm extends FormBasic {

  /**
   * The current user account proxy.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Fallback select options of members.
   *
   * @var array
   */
  protected $fallback;

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
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  private $privilegeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->currentUser = $this->getCurrentUser();
    $this->nodeStorage = $this->getNodeStorage();
    $this->userStorage = $this->getUserStorage();
    $this->privilegeManager = $this->getPrivilegeManager();
    $this->subscriptionManager = $this->getSubscriptionManager();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
    $form = parent::buildForm($form, $form_state);

    if (!isset($options['event'])) {
      return $form;
    }
    $event = $options['event'];

    // Save the event for later usage on submission.
    $form_state->set('event', $event->id());

    $activity = $event->field_activity->entity;
    $community = $activity->field_community->entity;

    $form['#attributes'] = [
      'title' => $options['event']->title->value,
    ];

    // Does the activity allow subscription from non-members ?
    $community_can_subscribe = (bool) $activity->field_community_can_subscribe->value;

    $activity_members = [];

    // When the whole community can subscribe, get all member of the community.
    if ($community_can_subscribe) {
      $query = $this->privilegeManager->queryMembersWithPrivileges($community, NULL);
    }
    else {
      // When only the activity member can subscribe, get every users with at
      // least 1 privilege on the activity.
      $query = $this->privilegeManager->queryMembersWithPrivileges($activity, NULL);
    }

    // Gather user to be subscribed (community or activity members).
    if ($query) {
      $rows = $query->execute()->fetchAll();

      foreach ($rows as $row) {
        $activity_members[$row->user] = $row->user;
      }
    }

    // Get every users already subscribed to the activity.
    // To prevent subscribe the same user twice.
    $query = $this->subscriptionManager->querySubscribers($event);

    if ($query) {
      $rows = $query->execute()->fetchAll();

      foreach ($rows as $row) {

        // Remove already subscribed member.
        if (!isset($activity_members[$row->user])) {
          unset($activity_members[$row->user]);
        }
      }
    }

    // Load user that may be subscribed manually.
    $subscribable_members = !empty($activity_members) ? $this->userStorage->loadMultiple($activity_members) : [];
    $select_options = [];
    $this->fallback = [];

    // Build the option for selectize.
    if (!empty($subscribable_members)) {
      foreach ($subscribable_members as $member) {
        $select_options[] = [
          'uid' => $member->id(),
          'email' => $member->mail->value,
          'displayname' => $member->field_firstname->value . ' ' . $member->field_lastname->value,
        ];
        $this->fallback[$member->id()] = !empty($select_options[$member->id()]['displayname']) ? $select_options[$member->id()]['displayname'] : $member->name->value;
      }
    }

    $form['member'] = [
      '#title' => $this->t('qs.subscription.subscribe_member'),
      '#type' => 'select',
      '#multiple' => FALSE,
      '#required' => FALSE,
      '#options' => $this->fallback,
      '#attributes' => [
        'placeholder' => $this->t('qs.subscription.subscribe_member.placeholder'),
        'selectize' => TRUE,
        'class' => ['selectize-members'],
        'data-options' => json_encode($select_options),
      ],
      '#validated' => TRUE,
      '#theme_wrappers' => [
        'form_element',
        'container__center',
      ],
    ];

    $form['actions'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => [
          'mb-5',
          'text-center',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => [
        'icon' => 'plus',
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
          'mx-auto',
        ],
      ],
      '#value' => $this->t('qs.subscription.subscribe_member.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_subscription_subscribe_member_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $event = $this->nodeStorage->load($form_state->get('event'));
    $account = $this->userStorage->load($form_state->getValue('member'));

    // Register the member but don't send mails to organizer(s).
    $subscription = $this->subscriptionManager->request($event, $account, FALSE);
    $this->subscriptionManager->confirm($subscription);

    $this->messenger()->addMessage($this->t('qs_subscription.subscription.form.subscribe.member.success @event', [
      '@event' => $event->getTitle(),
    ]));

    $form_state->setRedirect('qs_subscription.subscribers', ['event' => $event->id()], ['fragment' => 'card' . $account->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the member is valid.
    if (!$form_state->getValue('member') || empty($form_state->getValue('member'))) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['member']['#title']]));
    }

    if (!isset($this->fallback[$form_state->getValue('member')])) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.something_went_wrong'));
    }
  }

}
