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
      $message['subject'] = t('qs_auth.mails.register.subject');
      $message['body'][] =  Markup::create(t('qs_auth.mails.register.body') . ' <br /><br />');
      // $message['body'][] = Markup::create(t('Hi @username,', ['@username' => $params['user']->mail->value]) . ' <br /><br />');
      break;
  }

  $message['body'][] = Markup::create('<br /><br /><br /><br /><b>' . t('qs_auth.mails.regards') . '</b>');
}
