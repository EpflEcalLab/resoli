<?php

namespace Drupal\qs_subscription\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_export\Excel;
use Drupal\qs_subscription\Service\SubscriptionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * SubscribersController.
 */
class SubscribersController extends ControllerBase {

  /**
   * The QS Excel exporter.
   *
   * @var \Drupal\qs_export\Excel
   */
  protected $excelExporter;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  private $configuration = ['limit' => 50];

  /**
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  private $subscriptionManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, SubscriptionManager $subscription_manager, Excel $excel_exporter) {
    $this->acl = $acl;
    $this->subscriptionManager = $subscription_manager;
    $this->userStorage = $this->entityTypeManager()->getStorage('user');
    $this->subscriptionStorage = $this->entityTypeManager()->getStorage('subscription');
    $this->excelExporter = $excel_exporter;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $event
   *   Run access checks for this node.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
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
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
    $container->get('qs_subscription.subscription_manager'),
    $container->get('qs_export.excel')
    );
  }

  /**
   * Export the complete list of subscribed account to the event.
   */
  public function export(NodeInterface $event) {
    $activity = $event->field_activity->entity;
    $now = new DrupalDateTime();

    $query = $this->subscriptionManager->querySubscribers($event);
    $rows = $query->execute()->fetchAll();

    $ids = [];

    foreach ($rows as $row) {
      $ids[] = $row->id;
    }

    // When nothing can be downloaded, return a 404.
    if (empty($ids)) {
      throw new NotFoundHttpException();
    }

    // Load subscriptions entities.
    $subscriptions = $this->subscriptionStorage->loadMultiple($ids);

    $this->excelExporter->init();
    $this->excelExporter->normalize();

    $title = $this->t('qs_subscription.subscribers.export.title @event @activity @date', [
      '@event' => $event->getTitle(),
      '@activity' => $activity->getTitle(),
      '@date' => $now->format('d-m-Y'),
    ]);
    $summary = $this->t('qs_subscription.subscribers.export.summary @total', [
      '@total' => \count($subscriptions),
    ]);
    $disclaimer = $this->t('qs_subscription.subscribers.export.disclaimer');

    $this->excelExporter->setTitle($title->render());
    $this->excelExporter->setSummary($summary->render());
    $this->excelExporter->addHeader([
      $this->t('qs_subscription.subscribers.export.header.firstname.label')->render(),
      $this->t('qs_subscription.subscribers.export.header.lastname.label')->render(),
      $this->t('qs_subscription.subscribers.export.header.mail.label')->render(),
      $this->t('qs_subscription.subscribers.export.header.phone.label')->render(),
      $this->t('qs_subscription.subscribers.export.header.date.label')->render(),
    ]);

    foreach ($subscriptions as $subscription) {
      $created = DrupalDateTime::createFromTimestamp($subscription->created->value);
      $created->setTimezone(new \DateTimeZone('UTC'));

      $this->excelExporter->addRow([
        ['value' => $subscription->getOwner()->entity->field_firstname->value],
        ['value' => $subscription->getOwner()->entity->field_lastname->value],
        ['value' => $subscription->getOwner()->entity->getEmail()],
        ['value' => $subscription->getOwner()->entity->field_phone->value],
        ['value' => $created],
      ], [
        'odd-even-background' => TRUE,
      ]);
    }
    $this->excelExporter->setFooter($disclaimer->render());
    $this->excelExporter->finalize();

    return $this->excelExporter->download();
  }

  /**
   * Subscribers page.
   */
  public function subscribers(NodeInterface $event) {
    $variables['event'] = $event;
    $variables['activity'] = $event->field_activity->entity;

    $query = $this->subscriptionManager->querySubscribers($event);
    $rows = $query->execute()->fetchAll();

    // Get all members to mailto before pagination.
    $mailto = [];

    foreach ($rows as $row) {
      $mailto[$row->user] = $row->mail;
    }
    $variables['mailto'] = $mailto;

    pager_default_initialize(\count($rows), $this->configuration['limit']);
    $variables['pager'] = [
      '#type' => 'pager',
      '#quantity' => '3',
    ];
    $page = pager_find_page();
    $query->range($page * $this->configuration['limit'], $this->configuration['limit']);
    $rows = $query->execute()->fetchAll();

    $ids = [];

    foreach ($rows as $row) {
      $ids[] = $row->id;
    }

    // Load subscriptions entities.
    $subscriptions = $this->subscriptionStorage->loadMultiple($ids);
    $variables['subscriptions'] = $subscriptions;

    return [
      '#theme' => 'qs_subscription_subscribers_page',
      '#variables' => $variables,
      '#cache' => [
        'tags' => [
          // Invalidated whenever any community is updated, deleted or created.
          'user_list:user',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'subscription_list:subscription',
        ],
      ],
    ];
  }

}
