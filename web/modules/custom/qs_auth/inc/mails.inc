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
  $configFacotry = \Drupal::service('config.factory');
  $token = \Drupal::token();

  // Get Drupal Config for users mails.
  $config = $configFacotry->get('user.mail');

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
      $content = $config->get('register_pending_approval');

      $message['subject'] = $token->replace($content['subject']);
      $message['body'][] = Markup::create($token->replace($content['body']));
      break;
  }
}
