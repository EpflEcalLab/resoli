<?php

namespace Drupal\qs_community\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_activity\Service\EventManager;
use Drupal\qs_export\Excel;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Events Controller by Community.
 */
class EventsController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * The QS Excel exporter.
   *
   * @var \Drupal\qs_export\Excel
   */
  protected $excelExporter;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EventManager $event_manager, Excel $excel_exporter) {
    $this->acl = $acl;
    $this->eventManager = $event_manager;
    $this->excelExporter = $excel_exporter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
    $container->get('qs_activity.event_manager'),
    $container->get('qs_export.excel')
    );
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();
    if ($this->acl->hasAdminAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * Export the complete list of events by community.
   *
   * For performance reason, we only fetch the next 6 months events.
   */
  public function export(TermInterface $community) {
    $now = new DrupalDateTime();

    $start = clone $now;
    $start->setTime(0, 0);

    $end = clone $now;
    $end->setTime(23, 59, 59);
    $end->modify('+6 months');

    $events = $this->eventManager->getByDate($community, $start, $end);

    $this->excelExporter->init();
    $this->excelExporter->normalize();

    $title = $this->t('qs_community.events.export.title @community @date', [
      '@community' => $community->getName(),
      '@date' => $now->format('d-m-Y'),
    ]);
    $summary = $this->t('qs_community.events.export.summary @total', [
      '@total' => count($events),
    ]);
    $disclaimer = $this->t('qs_community.events.export.disclaimer');

    $this->excelExporter->setTitle($title->render());
    $this->excelExporter->setSummary($summary->render());
    $this->excelExporter->addHeader([
      $this->t('qs_community.events.export.header.activity.label')->render(),
      $this->t('qs_community.events.export.header.event.label')->render(),
      $this->t('qs_community.events.export.header.date_start.label')->render(),
      $this->t('qs_community.events.export.header.date_end.label')->render(),
      $this->t('qs_community.events.export.header.venue.label')->render(),
      $this->t('qs_community.events.export.header.organizer.label')->render(),
      $this->t('qs_community.events.export.header.organizer_mail.label')->render(),
      $this->t('qs_community.events.export.header.organizer_phone.label')->render(),
    ]);

    foreach ($events as $event) {
      $activity = $event->field_activity->entity;
      $this->excelExporter->addRow([
        $activity->getTitle(),
        $event->getTitle(),
        $event->field_start_at->date,
        $event->field_end_at->date,
        $event->field_venue->value,
        $event->field_contact_name->value,
        $event->field_contact_mail->value,
        $event->field_contact_phone->value,
      ]);
    }
    $this->excelExporter->setFooter($disclaimer->render());
    $this->excelExporter->finalize();

    return $this->excelExporter->download();
  }

}
