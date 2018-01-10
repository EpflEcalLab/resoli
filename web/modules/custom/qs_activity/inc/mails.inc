<?php

/**
 * @file
 * Mail hook.
 */

use Drupal\Core\Render\Markup;

/**
 * Mail hook.
 */
function qs_activity_mail($key, &$message, $params) {
  // Load services.
  $configFactory = \Drupal::service('config.factory');
  $dateFormatter = \Drupal::service('date.formatter');

  // Get Drupal Config for site.
  $site = $configFactory->get('system.site');

  $headers = [
    'MIME-Version'              => '1.0',
    'Content-Type'              => 'text/html; charset=UTF-8; format=flowed; delsp=yes',
    'Content-Transfer-Encoding' => '8Bit',
    'X-Mailer'                  => 'Drupal',
  ];

  // Add headers.
  foreach ($headers as $i => $value) {
    $message['headers'][$i] = $value;
  }

  switch ($key) {
    // Sent to Subscribed user(s) of event &
    // to all Activity Maintainer(s) et Activity Organizer(s).
    case 'activity_event_deleted':
      $author   = $message['params']['author'];
      $event    = $message['params']['event'];
      $activity = $event->field_activity->entity;

      $message['subject'] = t('qs.mail.event.deleted.subject @sitename @event', [
        '@sitename' => $site->get('name'),
        '@event'    => $event->getTitle(),
      ]);

      $message['body'][] = Markup::create(t('qs.mail.event.deleted.body @mail @firstname @lastname @activity @event @date @time_start @time_end', [
        '@mail'       => $author->getEmail(),
        '@firstname'  => $author->field_firstname->value,
        '@lastname'   => $author->field_lastname->value,
        '@activity'   => $activity->getTitle(),
        '@event'      => $event->getTitle(),
        '@date'       => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_medium_date_only'),
        '@time_start' => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_short_hours_minutes'),
        '@time_end'   => $dateFormatter->format($event->field_end_at->date->getTimestamp(), 'default_short_hours_minutes'),
      ]));
      break;

    // Sent to Subscribed user(s) of event &
    // to all Activity Maintainer(s) et Activity Organizer(s).
    case 'activity_event_modified':
      break;
  }
}
