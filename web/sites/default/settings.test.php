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
 * Swiftmailer configurations.
 */
$config['swiftmailer.transport']['transport'] = 'smtp';
$config['swiftmailer.transport']['smtp_host'] = 'mail';
$config['swiftmailer.transport']['smtp_port'] = '1025';
$config['swiftmailer.transport']['smtp_encryption'] = '0';

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
