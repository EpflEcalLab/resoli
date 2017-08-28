<?php

/**
 * @file
 * User Forget Password & Reset form hook.
 */

/**
 * Alter Drupal Pass form with custom markup and submission.
 *
 * The user_pass form desn't have a module_hook_form_alter.
 * The user_reset form desn't have a module_hook_form_alter.
 */
function qs_auth_form_alter(&$form, $form_state, $form_id) {
  if ($form_id == 'user_pass') {
    $form['#attributes']['autocomplete'] = "off";

    $form['forget'] = [
      '#type'  => 'fieldset',
      '#title' => t('qs_auth.forget_pass_form.title'),
    ];

    $form['forget']['name'] = $form['name'];
    unset($form['name']);

    $form['forget']['name']['#title'] = t('qs_auth.forget_pass_form.name.label');
    $form['forget']['name']['#attributes']['autocomplete'] = "off";
    $form['forget']['name']['#attributes']['autofocus'] = "autofocus";
    $form['forget']['name']['#description'] = t('qs_auth.forget_pass_form.name.description');

    $form['actions']['submit']['#value'] = t('qs_auth.forget_pass_form.submit');
  }

  if ($form_id == 'user_pass_reset') {
    $form['reset'] = [
      '#type'  => 'fieldset',
      '#title' => t('qs_auth.pass_reset_form.title'),
    ];

    $form['reset']['help'] = $form['help'];
    unset($form['help']);
    $form['reset']['message'] = $form['message'];
    unset($form['message']);

    $form['actions']['submit']['#value'] = t('qs_auth.pass_reset_form.submit');
  }
}
