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

    // Sent to the author of the request.
    case 'request_solved':
      $request = $message['params']['request'];

      /** @var \Drupal\user\UserInterface $user */
      $author = $request->uid->entity;
      /** @var \Drupal\user\UserInterface $solved_by */
      $solved_by = $request->get('field_solved_by')->entity;

      $message['subject'] = t('qs.mail.request.solved.subject @sitename @resolver_firstname @resolver_lastname @community @theme @created_at', [
        '@sitename' => $site->get('name'),
        '@community' => $request->field_community->entity->getName(),
        '@theme' => $request->field_theme->entity->getName(),
        '@created_at' => $dateFormatter->format($request->created->value, 'default_medium_date_only'),
        '@resolver_firstname' => $solved_by->field_firstname->value,
        '@resolver_lastname' => $solved_by->field_lastname->value,
      ]);

      $message['body'][] = Markup::create(nl2br(t('qs.mail.request.solved.body @author_firstname @author_lastname @resolver_firstname @resolver_lastname @community @theme @created_at @solved_at', [
        '@author_firstname' => $author->field_firstname->value,
        '@author_lastname' => $author->field_lastname->value,
        '@resolver_firstname' => $solved_by->field_firstname->value,
        '@resolver_lastname' => $solved_by->field_lastname->value,
        '@community' => $request->field_community->entity->getName(),
        '@theme' => $request->field_theme->entity->getName(),
        '@created_at' => $dateFormatter->format($request->created->value, 'default_medium_date_only'),
        '@solved_at' => $dateFormatter->format($request->changed->value, 'default_medium_date_only'),
      ])));

      break;

    // Sent to the author of the request.
    case 'request_archived':
      $request = $message['params']['request'];

      /** @var \Drupal\user\UserInterface $archived_by */
      $archived_by = $message['params']['archived_by'];

      /** @var \Drupal\user\UserInterface $user */
      $author = $request->uid->entity;

      $message['subject'] = t('qs.mail.request.archived.subject @sitename @archiver_firstname @archiver_lastname @community @theme @created_at', [
        '@sitename' => $site->get('name'),
        '@community' => $request->field_community->entity->getName(),
        '@theme' => $request->field_theme->entity->getName(),
        '@created_at' => $dateFormatter->format($request->created->value, 'default_medium_date_only'),
        '@archiver_firstname' => $archived_by->field_firstname->value,
        '@archiver_lastname' => $archived_by->field_lastname->value,
      ]);

      $message['body'][] = Markup::create(nl2br(t('qs.mail.request.archived.body @author_firstname @author_lastname @archiver_firstname @archiver_lastname @community @theme @created_at @archived_at', [
        '@author_firstname' => $author->field_firstname->value,
        '@author_lastname' => $author->field_lastname->value,
        '@archiver_firstname' => $archived_by->field_firstname->value,
        '@archiver_lastname' => $archived_by->field_lastname->value,
        '@community' => $request->field_community->entity->getName(),
        '@theme' => $request->field_theme->entity->getName(),
        '@created_at' => $dateFormatter->format($request->created->value, 'default_medium_date_only'),
        '@archived_at' => $dateFormatter->format($request->changed->value, 'default_medium_date_only'),
      ])));

      break;
  }
}
