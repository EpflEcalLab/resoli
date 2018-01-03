<?php

/**
 * @file
 * Mail hook.
 */

use Drupal\Core\Render\Markup;
use Drupal\Core\Url;

/**
 * Mail hook.
 */
function qs_subscription_mail($key, &$message, $params) {
  // Load services.
  $configFactory = \Drupal::service('config.factory');

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
    // Sent to user when event subscription request has been approved.
    case 'subscription_event_waiting_approval_confirm':
      $account   = $message['params']['account'];
      $event     = $message['params']['event'];
      $activity  = $event->field_activity->entity;
      $community = $activity->field_community->entity;

      $message['subject'] = t('qs.mail.subscription.waiting_approval.confirm.subject @sitename @mail @firstname @lastname @community @event @activity', [
        '@sitename'  => $site->get('name'),
        '@mail'      => $account->mail->value,
        '@firstname' => $account->field_firstname->value,
        '@lastname'  => $account->field_lastname->value,
        '@community' => $community->getName(),
        '@event'     => $event->getTitle(),
        '@activity'  => $activity->getTitle(),
      ]);

      $message['body'][0] = Markup::create(t('qs.mail.subscription.waiting_approval.confirm.body @mail @firstname @lastname @community @event @activity @url', [
        '@mail'      => $account->mail->value,
        '@firstname' => $account->field_firstname->value,
        '@lastname'  => $account->field_lastname->value,
        '@community' => $community->getName(),
        '@event'     => $event->getTitle(),
        '@activity'  => $activity->getTitle(),
        '@url'       => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
      ]));
      break;
  }
}
