<?php

/**
 * @file
 * Mail hook.
 */

use Drupal\Core\Render\Markup;
use Drupal\user\UserInterface;

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
    /*
     * Mails already woking :
     *  - qs.mail.pass.recovery - Mails for requesting a new pass.
     *  - qs.mail.account.cancel_confirm - Mails to confirm your cancelation.
     *  - qs.mail.welcome.no_approval - Mails when you account is created
     *
     * Mails @todo:
     *  - qs.mail.awaiting.approval.subject - Mails send when you create
     *    request approval in community
     *  - qs.mail.awaiting.approval_confirm - Mails send when approval
     *    confirmed/rejected
     *
     *  - qs.mail.admin.awaiting.approval - Mails for admin - when user
     *    requestion approval in community
     *  - qs.mail.admin.welcome.no_approval - Mails for admin when user
     *    create account
     */

    case 'user_cancel_confirm':
      $account = $message['params']['account'];
      $message['subject'] = t('qs.mail.account.cancel_confirm.subject @sitename @mail @firstname @lastname', [
        '@sitename'  => $site->get('name'),
        '@mail'      => $account->mail->value,
        '@firstname' => $account->field_firstname->value,
        '@lastname'  => $account->field_lastname->value,
      ]);

      $message['body'][0] = Markup::create(t('qs.mail.account.cancel_confirm.body @mail @firstname @lastname @cancel-url', [
        '@mail'       => $account->mail->value,
        '@firstname'  => $account->field_firstname->value,
        '@lastname'   => $account->field_lastname->value,
        '@cancel-url' => qs_user_cancel_url($account),
      ]));
      break;

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

/**
 * Generates a URL to confirm an account cancellation request.
 *
 * @param \Drupal\user\UserInterface $account
 *   The user account object.
 * @param array $options
 *   (optional) A keyed array of settings. Supported options are:
 *   - langcode: A language code to be used when generating locale-sensitive
 *     URLs. If langcode is NULL the users preferred language is used.
 *
 * @return string
 *   A unique URL that may be used to confirm the cancellation of the user
 *   account.
 *
 * @see user_mail_tokens()
 * @see \Drupal\user\Controller\UserController::confirmCancel()
 */
function qs_user_cancel_url(UserInterface $account, array $options = []) {
  $timestamp = REQUEST_TIME;
  $langcode = isset($options['langcode']) ? $options['langcode'] : $account->getPreferredLangcode();
  $url_options = ['absolute' => TRUE, 'language' => \Drupal::languageManager()->getLanguage($langcode)];
  return \Drupal::url('qs_auth.cancel.confirm', [
    'user'        => $account->id(),
    'timestamp'   => $timestamp,
    'hashed_pass' => user_pass_rehash($account, $timestamp),
  ], $url_options);
}
