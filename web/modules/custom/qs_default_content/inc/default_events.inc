<?php

/**
 * @file
 * Contains alteration of default events dates.
 */

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Global function to alter default events with date according now.
 *
 * @SuppressWarnings(PHPMD)
 */
function qs_default_content_events() {
  $field_start = 'field_start_at';
  $field_end = 'field_end_at';

  // @var \Drupal\Core\Entity\EntityTypeManagerInterface
  $entityManager = \Drupal::service('entity_type.manager');
  $nodeStorage = $entityManager->getStorage('node');

  $now =  new DrupalDateTime();

  // Event - Activity (2) - 2 days ago at 01h00.
  $event_15 = $nodeStorage->load(15);
  $dt = new DrupalDateTime();
  $dt->modify('-2 days')->setTime(1, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_15->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+20 minutes');
  $event_15->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_15->save();

  // Event - Activity (2) - 1 day ago at 15h00.
  $event_16 = $nodeStorage->load(16);
  $dt = new DrupalDateTime();
  $dt->modify('-1 day')->setTime(15, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_16->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+30 minutes');
  $event_16->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_16->save();

  // Event - Activity (2) - Today in 1 hour.
  $event_17 = $nodeStorage->load(17);
  $dt = new DrupalDateTime();
  $dt->modify('+1 hour');
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_17->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+45 minutes');
  $event_17->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_17->save();

  // Event - Activity (4) - Tomorrow at 18h00.
  $event_18 = $nodeStorage->load(18);
  $dt = new DrupalDateTime();
  $dt->modify('tomorrow')->setTime(18, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_18->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+17 minutes');
  $event_18->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_18->save();

  // Event - Activity (6) - In 2 days at 19h00.
  $event_19 = $nodeStorage->load(19);
  $dt = new DrupalDateTime();
  $dt->modify('+2 days')->setTime(19, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_19->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+22 minutes');
  $event_19->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_19->save();

  // Event - Activity (5) - 10 days ago at 23h20.
  $event_20 = $nodeStorage->load(20);
  $dt = new DrupalDateTime();
  $dt->modify('-10 days')->setTime(23, 20);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_20->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+13 minutes');
  $event_20->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_20->save();

  // Event - Activity (5) - Today 2 hours ago.
  $event_21 = $nodeStorage->load(21);
  $dt = new DrupalDateTime();
  $dt->modify('-2 hours');
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_21->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+15 minutes');
  $event_21->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_21->save();

  // Event - Activity (4) - Today in 4 hours.
  $event_22 = $nodeStorage->load(22);
  $dt = new DrupalDateTime();
  $dt->modify('+4 hours');
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_22->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+21 minutes');
  $event_22->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_22->save();

  // Event - Activity (7) - Yesterday at 00h05.
  $event_23 = $nodeStorage->load(23);
  $dt = new DrupalDateTime();
  $dt->modify('yesterday')->setTime(00, 5);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_23->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+47 minutes');
  $event_23->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_23->save();

  // Event - Activity (7) - 1 Month ago, the 2nd at 16h00.
  $event_24 = $nodeStorage->load(24);
  $dt = new DrupalDateTime();
  $dt->modify('-1 month')->setTime(16, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_24->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+8 minutes');
  $event_24->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_24->save();

  // Event - Activity (10) - In 1 Month, the 16th at 11h00.
  $event_25 = $nodeStorage->load(25);
  $dt = new DrupalDateTime();
  $dt->modify('-1 month')->setTime(11, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $dt->setDate($dt->format('Y'), $dt->format('m'), 16);
  $event_25->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+29 minutes');
  $event_25->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_25->save();

  // Event - Activity (9) - In 16 Months, the 01st at 15h00.
  $event_26 = $nodeStorage->load(26);
  $dt = new DrupalDateTime();
  $dt->modify('+16 months')->setTime(15, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $dt->setDate($dt->format('Y'), $dt->format('m'), 1);
  $event_26->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+14 minutes');
  $event_26->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_26->save();

  // Event - Activity (9) - 22 Months ago, the 26nd at 06h00.
  $event_27 = $nodeStorage->load(27);
  $dt = new DrupalDateTime();
  $dt->modify('-22 months')->setTime(06, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $dt->setDate($dt->format('Y'), $dt->format('m'), 26);
  $event_27->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+34 minutes');
  $event_27->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_27->save();

  // Event - Activity (2) - 9 Months ago, the 21nd at 12h00.
  $event_28 = $nodeStorage->load(28);
  $dt = new DrupalDateTime();
  $dt->modify('-9 months')->setTime(12, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $dt->setDate($dt->format('Y'), $dt->format('m'), 21);
  $event_28->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+12 minutes');
  $event_28->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_28->save();

  // Event - Activity (11) - Today in 2 hours.
  $event_29 = $nodeStorage->load(29);
  $dt = new DrupalDateTime();
  $dt->modify('+2 hours');
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_29->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+27 minutes');
  $event_29->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_29->save();

  // Event - Activity (6) - Yesterday at 15h00.
  $event_30 = $nodeStorage->load(30);
  $dt = new DrupalDateTime();
  $dt->modify('yesterday')->setTime(15, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_30->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+14 minutes');
  $event_30->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_30->save();

  // Event - Activity (8) - Tomorrow at 13h30.
  $event_31 = $nodeStorage->load(31);
  $dt = new DrupalDateTime();
  $dt->modify('tomorrow')->setTime(13, 30);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_31->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+25 minutes');
  $event_31->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_31->save();

  // Event - Activity (7) - In 14 days at 16h00.
  $event_32 = $nodeStorage->load(32);
  $dt = new DrupalDateTime();
  $dt->modify('+14 days')->setTime(16, 00);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_32->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+24 minutes');
  $event_32->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_32->save();

  // Event - Activity (11) - In 2 days at 22h10.
  $event_33 = $nodeStorage->load(33);
  $dt = new DrupalDateTime();
  $dt->modify('+2 days')->setTime(22, 10);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_33->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+50 minutes');
  $event_33->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_33->save();

  // Event - Activity (10) - Yesterday at 00h00.
  $event_34 = $nodeStorage->load(34);
  $dt = new DrupalDateTime();
  $dt->modify('yesterday')->setTime(0, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_34->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+22 minutes');
  $event_34->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_34->save();

  // Event - Activity (3) - In 2 days at 16h00.
  $event_35 = $nodeStorage->load(35);
  $dt = new DrupalDateTime();
  $dt->modify('+2 days')->setTime(16, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_35->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+56 minutes');
  $event_35->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_35->save();

  // Event - Event - Activity (3) - In 1 month at 16h00.
  $event_36 = $nodeStorage->load(36);
  $dt = new DrupalDateTime();
  $dt->modify('+1 month')->setTime(16, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_36->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+47 minutes');
  $event_36->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_36->save();

  // Event - Event - Activity (3) - today at 23:00.
  $event_37 = $nodeStorage->load(37);
  $dt = new DrupalDateTime();
  $dt->modify('today')->setTime(23, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_37->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+12 minutes');
  $event_37->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_37->save();

  // Event - Event - Activity (3) - Yesterday at 12h00.
  $event_38 = $nodeStorage->load(38);
  $dt = new DrupalDateTime();
  $dt->modify('yesterday')->setTime(12, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_38->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+2 hours');
  $event_38->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_38->save();

  // Event - Event - Activity (5) - 1 weeks ago at 12h00.
  $event_39 = $nodeStorage->load(39);
  $dt = new DrupalDateTime();
  $dt->modify('-1 week')->setTime(12, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_39->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('3 hours');
  $event_39->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_39->save();

  // Event - Event - Activity (5) - In 8 months at 11h00.
  $event_40 = $nodeStorage->load(40);
  $dt = new DrupalDateTime();
  $dt->modify('+8 months')->setTime(11, 0);
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_40->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+4 hours');
  $event_40->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_40->save();

  // Event - Activity (3) - In 2 days at 19h00.
  // Maximum at last day of current month to avoid failing Monthly view tests.
  $event_54 = $nodeStorage->load(54);
  $dt = new DrupalDateTime();
  $dt->modify('+2 days')->setTime(19, 20);
  if ($dt->format('m') != $now->format('m')) {
    $dt->modify('last day of '.$now->format('F'));
  }
  $dt->setTimezone(new DateTimeZone('UTC'));
  $event_54->set($field_start, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $dt->modify('+3 hours');
  $event_54->set($field_end, $dt->format(DATETIME_DATETIME_STORAGE_FORMAT));
  $event_54->save();
}
