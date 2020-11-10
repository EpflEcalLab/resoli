<?php

namespace Drupal\qs_community\Controller;

use Drupal\Core\Controller\ControllerBase;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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

    $this->excelExporter->setSpreadsheetTitle($title->render());
    $this->excelExporter->addHeader([
      $this->t('qs_community.events.export.header.event.label')->render(),
      $this->t('qs_community.events.export.header.timetable.label')->render(),
      $this->t('qs_community.events.export.header.venue.label')->render(),
      $this->t('qs_community.events.export.header.contact')->render(),
    ], 0, ['background' => '7030A0', 'foreground' => 'ffffff']);

    foreach ($events as $event) {

      // Set a Rich text for timetable with partial bold content.
      $timetable = new RichText();
      $bold_timetable = $timetable->createTextRun($event->field_start_at->date->format('d.m.Y'));
      $bold_timetable->getFont()->setBold(TRUE);
      $timetable->createText(sprintf(' %s - %s', $event->field_start_at->date->format('H:i'), $event->field_end_at->date->format('H:i')));

      // Set a HTML text for contact with new line.
      $contact = [];
      if ($event->field_contact_name->value) {
        $contact[] = $event->field_contact_name->value;
      }
      if ($event->field_contact_name->value) {
        $contact[] = '<br>' . $event->field_contact_phone->value;
      }
      $contact_html = new Html();
      $contact_html = $contact_html->toRichTextObject(implode(', ', $contact));

      $this->excelExporter->addRow([
        ['value' => $event->getTitle()],
        ['value' => $timetable],
        ['value' => $event->field_venue->value],
        ['value' => $contact_html],
      ], [
        'odd-even-background' => TRUE,
        'v-alignment' => Alignment::VERTICAL_CENTER,
        'h-alignment' => Alignment::HORIZONTAL_CENTER,
      ]);
    }
    $this->excelExporter->finalize();
    $this->excelExporter->lastRowBorder();

    $this->excelExporter->selColDimensions([
      'A' => ['width' => 48],
      'B' => ['width' => 48],
      'C' => ['width' => 68],
      'D' => ['width' => 48],
    ]);

    return $this->excelExporter->download();
  }

}
