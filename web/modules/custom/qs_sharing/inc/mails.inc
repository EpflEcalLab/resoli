<?php

/**
 * @file
 * Mail hook.
 */

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Render\Markup;

/**
 * Mail hook.
 */
function qs_sharing_mail($key, &$message, $params) {
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
    // Sent to the author of the offer.
    case 'offer_moderated':
      $offer = $message['params']['offer'];

      /** @var \Drupal\user\UserInterface $user */
      $user = $offer->uid->entity;

      $message['subject'] = t('qs.mail.offer.deactivated.subject @sitename @offer', [
        '@sitename' => $site->get('name'),
        '@offer' => $offer->getTitle(),
      ]);

      $message['body'][] = Markup::create(nl2br(t('qs.mail.offer.deactivated.body @mail @firstname @lastname @community @offer_type @offer @created_at', [
        '@mail' => $user->getEmail(),
        '@firstname' => $user->field_firstname->value,
        '@lastname' => $user->field_lastname->value,
        '@community' => new FormattableMarkup($offer->field_offer_type->entity->field_community->entity->getName(), []),
        '@offer_type' => new FormattableMarkup($offer->field_offer_type->entity->getTitle(), []),
        '@offer' => new FormattableMarkup($offer->getTitle(), []),
        '@created_at' => $dateFormatter->format($offer->changed->value, 'default_medium_date_only'),
      ])));

      break;
  }
}
