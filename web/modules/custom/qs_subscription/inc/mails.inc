<?php

/**
 * @file
 * Mail hook.
 */

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;

/**
 * Mail hook.
 */
function qs_subscription_mail($key, &$message, $params) {
  // Load services.
  $configFactory = \Drupal::service('config.factory');
  $dateFormatter = \Drupal::service('date.formatter');

  // Get Drupal Config for site.
  $site = $configFactory->get('system.site');

  $headers = [
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/html; charset=UTF-8; format=flowed; delsp=yes',
    'Content-Transfer-Encoding' => '8Bit',
    'X-Mailer' => 'Drupal',
  ];

  // Add headers.
  foreach ($headers as $i => $value) {
    $message['headers'][$i] = $value;
  }

  switch ($key) {
    // Sent to user when event subscription request has been approved.
    case 'subscription_event_waiting_approval_confirm':
      $account = $message['params']['account'];
      $event = $message['params']['event'];
      $activity = $event->field_activity->entity;
      $community = $activity->field_community->entity;

      $message['subject'] = t('qs.mail.subscription.waiting_approval.confirm.subject @sitename @mail @firstname @lastname @community @event @activity', [
        '@sitename' => $site->get('name'),
        '@mail' => $account->mail->value,
        '@firstname' => $account->field_firstname->value,
        '@lastname' => $account->field_lastname->value,
        '@community' => $community->getName(),
        '@event' => new FormattableMarkup($event->getTitle(), []),
        '@activity' => new FormattableMarkup($activity->getTitle(), []),
      ]);

      $message['body'][0] = Markup::create(nl2br(t('qs.mail.subscription.waiting_approval.confirm.body @mail @firstname @lastname @community @event @date @time_start @time_end @activity @url', [
        '@mail' => $account->mail->value,
        '@firstname' => $account->field_firstname->value,
        '@lastname' => $account->field_lastname->value,
        '@community' => $community->getName(),
        '@event' => new FormattableMarkup($event->getTitle(), []),
        '@date' => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_medium_date_only'),
        '@time_start' => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_short_hours_minutes'),
        '@time_end' => $dateFormatter->format($event->field_end_at->date->getTimestamp(), 'default_short_hours_minutes'),
        '@activity' => new FormattableMarkup($activity->getTitle(), []),
        '@url' => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
      ])));

      break;
    // Sent to organizers & maintainers when some ask for event subscription.
    case 'subscription_event_waiting_approval_request_organizers':
      $user = $message['params']['user'];
      $event = $message['params']['event'];
      $activity = $event->field_activity->entity;
      $community = $activity->field_community->entity;

      $message['subject'] = t('qs.mail.subscription.waiting_approval.request_organizers.subject @sitename @mail @firstname @lastname @community @event @activity', [
        '@sitename' => $site->get('name'),
        '@mail' => $user->mail->value,
        '@firstname' => $user->field_firstname->value,
        '@lastname' => $user->field_lastname->value,
        '@community' => $community->getName(),
        '@event' => new FormattableMarkup($event->getTitle(), []),
        '@activity' => new FormattableMarkup($activity->getTitle(), []),
      ]);

      $message['body'][0] = Markup::create(nl2br(t('qs.mail.subscription.waiting_approval.request_organizers.body @mail @firstname @lastname @community @event @date @time_start @time_end @activity @url', [
        '@mail' => $user->mail->value,
        '@firstname' => $user->field_firstname->value,
        '@lastname' => $user->field_lastname->value,
        '@community' => $community->getName(),
        '@event' => new FormattableMarkup($event->getTitle(), []),
        '@date' => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_medium_date_only'),
        '@time_start' => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_short_hours_minutes'),
        '@time_end' => $dateFormatter->format($event->field_end_at->date->getTimestamp(), 'default_short_hours_minutes'),
        '@activity' => new FormattableMarkup($activity->getTitle(), []),
        '@url' => Url::fromRoute('qs_subscription.waiting_approval', ['event' => $event->id()], ['absolute' => TRUE])->toString(),
      ])));

      break;
    // Sent to organizers & maintainers when event subscription
    // request has been approved.
    case 'subscription_event_waiting_approval_confirm_organizers':
      $user = $message['params']['user'];
      $event = $message['params']['event'];
      $activity = $event->field_activity->entity;
      $community = $activity->field_community->entity;

      $message['subject'] = t('qs.mail.subscription.waiting_approval.confirm_organizers.subject @sitename @mail @firstname @lastname @community @event @activity', [
        '@sitename' => $site->get('name'),
        '@mail' => $user->mail->value,
        '@firstname' => $user->field_firstname->value,
        '@lastname' => $user->field_lastname->value,
        '@community' => $community->getName(),
        '@event' => new FormattableMarkup($event->getTitle(), []),
        '@activity' => new FormattableMarkup($activity->getTitle(), []),
      ]);

      $message['body'][0] = Markup::create(nl2br(t('qs.mail.subscription.waiting_approval.confirm_organizers.body @mail @firstname @lastname @community @event @date @time_start @time_end @activity @url', [
        '@mail' => $user->mail->value,
        '@firstname' => $user->field_firstname->value,
        '@lastname' => $user->field_lastname->value,
        '@community' => $community->getName(),
        '@event' => new FormattableMarkup($event->getTitle(), []),
        '@date' => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_medium_date_only'),
        '@time_start' => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_short_hours_minutes'),
        '@time_end' => $dateFormatter->format($event->field_end_at->date->getTimestamp(), 'default_short_hours_minutes'),
        '@activity' => new FormattableMarkup($activity->getTitle(), []),
        '@url' => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
      ])));

      break;
    // Sent to user when event subscription request has been declined.
    case 'subscription_event_waiting_approval_decline':
      $account = $message['params']['account'];
      $event = $message['params']['event'];
      $activity = $event->field_activity->entity;
      $community = $activity->field_community->entity;

      $message['subject'] = t('qs.mail.subscription.waiting_approval.decline.subject @sitename @mail @firstname @lastname @community @event @activity', [
        '@sitename' => $site->get('name'),
        '@mail' => $account->mail->value,
        '@firstname' => $account->field_firstname->value,
        '@lastname' => $account->field_lastname->value,
        '@community' => $community->getName(),
        '@event' => new FormattableMarkup($event->getTitle(), []),
        '@activity' => new FormattableMarkup($activity->getTitle(), []),
      ]);

      $message['body'][0] = Markup::create(nl2br(t('qs.mail.subscription.waiting_approval.decline.body @mail @firstname @lastname @community @event @date @time_start @time_end @activity @url', [
        '@mail' => $account->mail->value,
        '@firstname' => $account->field_firstname->value,
        '@lastname' => $account->field_lastname->value,
        '@community' => $community->getName(),
        '@event' => new FormattableMarkup($event->getTitle(), []),
        '@date' => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_medium_date_only'),
        '@time_start' => $dateFormatter->format($event->field_start_at->date->getTimestamp(), 'default_short_hours_minutes'),
        '@time_end' => $dateFormatter->format($event->field_end_at->date->getTimestamp(), 'default_short_hours_minutes'),
        '@activity' => new FormattableMarkup($activity->getTitle(), []),
        '@url' => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
      ])));

      break;
  }
}
