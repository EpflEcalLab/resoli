<?php

// phpcs:ignoreFile

$databases['default']['default'] = array (
  'database' => 'drupal_test',
  'username' => 'drupal',
  'password' => 'drupal',
  'prefix' => '',
  'host' => 'db',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 */
$settings['hash_salt'] = 'JL8kjr9Sqp2g4twbSBPhUAFUWFJq5wgfWv4QD0trCsG9TlxV18pzHyVFrtqKgBANYTgkh7pIfQ';

/**
 * The Symfony Mailer transporter.
 *
 * @var string
 */
$config['symfony_mailer_lite.settings']['default_transport'] = 'smtp';
$config['symfony_mailer_lite.symfony_mailer_lite_transport.smtp']['configuration']['host'] = 'mail';
$config['symfony_mailer_lite.symfony_mailer_lite_transport.smtp']['configuration']['port'] = '1025';

/**
 * Private file path.
 *
 * @var string
 */
$settings['file_private_path'] = '/var/www/web/sites/default/files/private';

/**
 * Ensure Backerymails is never enable on tests.
 */
$config['backerymails.settings']['reroute']['status'] = FALSE;
