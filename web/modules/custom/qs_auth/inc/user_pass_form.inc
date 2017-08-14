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
    $form['#attributes']['class'] = ['form-emphasis'];

    $form['forget'] = [
      '#type'  => 'fieldset',
      '#title' => t('Forgot your password?'),
    ];

    $form['forget']['name'] = $form['name'];
    unset($form['name']);

    $form['forget']['name']['#title'] = t('E-mail address');
    $form['forget']['name']['#attributes']['autocomplete'] = "off";
    $form['forget']['name']['#attributes']['autofocus'] = "autofocus";
    $form['forget']['name']['#description'] = $form['mail']['#markup'];
    $form['forget']['name']['#prefix'] = '<div class="form-group">';
    $form['forget']['name']['#suffix'] = '</div>';
    unset($form['mail']);

    $form['actions']['submit']['#attributes']['class'] = ['btn', 'btn-primary'];
    $form['actions']['submit']['#prefix'] = '<div class="form-group mb-0">';
    $form['actions']['submit']['#suffix'] = '</div>';
  }

  if ($form_id == 'user_pass_reset') {
    $form['#attributes']['class'] = ['form-emphasis'];

    $form['reset'] = [
      '#type'  => 'fieldset',
      '#title' => t('One time login'),
    ];

    $form['reset']['help'] = $form['help'];
    unset($form['help']);
    $form['reset']['message'] = $form['message'];
    unset($form['message']);

    $form['actions']['submit']['#attributes']['class'] = ['btn', 'btn-primary'];
    $form['actions']['submit']['#prefix'] = '<div class="form-group mb-0">';
    $form['actions']['submit']['#suffix'] = '</div>';
  }
}
