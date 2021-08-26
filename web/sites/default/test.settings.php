<?php

/**
 * Extends the development settings and allow override below.
 */
include __DIR__ . '/' . 'development.settings.php';

/**
 * Ensure Backerymails is never enable on tests.
 */
$config['backerymails.settings']['reroute']['status'] = FALSE;
