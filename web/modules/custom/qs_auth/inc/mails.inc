<?php

/**
 * @file
 * Mail hook.
 */

use Drupal\Core\Render\Markup;

/**
 * Mail hook.
 */
function qs_auth_mail($key, &$message, $params) {
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

    // Sended to new user when registering.
    case 'register':
      $account = $params['user'];
      $message['subject'] = t('qs.mail.welcome.no_approval.subject @sitename @mail @firstname @lastname', [
        '@sitename'  => $site->get('name'),
        '@mail'      => $account->mail->value,
        '@firstname' => $account->field_firstname->value,
        '@lastname'  => $account->field_lastname->value,
      ]);

      $message['body'][] = Markup::create(t('qs.mail.welcome.no_approval.body @mail @firstname @lastname', [
        '@mail'           => $account->mail->value,
        '@firstname'      => $account->field_firstname->value,
        '@lastname'       => $account->field_lastname->value,
      ]));

      break;
  }
}

/**
 * Mail hook alteration.
 */
function qs_auth_mail_alter(&$message) {
  // Load services.
  $configFactory = \Drupal::service('config.factory');

  // Get Drupal Config for site.
  $site = $configFactory->get('system.site');

  switch ($message['id']) {
    case 'user_password_reset':
      $account = $message['params']['account'];
      $message['subject'] = t('qs.mail.pass.recovery.subject @sitename @mail @firstname @lastname', [
        '@sitename'  => $site->get('name'),
        '@mail'      => $account->mail->value,
        '@firstname' => $account->field_firstname->value,
        '@lastname'  => $account->field_lastname->value,
      ]);

      $message['body'][0] = Markup::create(t('qs.mail.pass.recovery.body @mail @firstname @lastname @one-time-login', [
        '@mail'           => $account->mail->value,
        '@firstname'      => $account->field_firstname->value,
        '@lastname'       => $account->field_lastname->value,
        '@one-time-login' => user_pass_reset_url($account),
      ]));
      break;
  }
}
