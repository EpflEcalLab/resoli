<?php

/**
 * @file
 * User Forget Password & Reset form hook.
 */

/**
 * Alter Drupal Pass form with custom markup and submission.
 *
 * The user_pass form doesn't have a module_hook_form_alter.
 * The user_reset form doesn't have a module_hook_form_alter.
 */
function qs_auth_form_alter(&$form, $form_state, $form_id) {
  if ($form_id == 'user_pass' || $form_id == 'user_pass_reset') {
    $form['#theme_wrappers'] = [
      'form__fullpage',
    ];

    unset($form['#title']);

    // Display links to other meaningful pages.
    $form['#top_links'] = [
      'qs_auth.login' => [
        'label' => t('qs_auth.link.login'),
        'options' => [
          'icon' => 'chevron-left',
        ],
      ],
      '<front>' => [
        'label' => t('qs_auth.link.home'),
      ],
    ];

    $form['actions']['submit']['#value'] = t('qs.form.submit');
    $form['actions']['submit']['#attributes']['class'] = ['mx-auto d-block btn btn-outline-secondary mt-3 btn-outline-invert shadow-to-bottom'];
  }

  if ($form_id == 'user_pass') {
    $form['forget'] = [
      '#type'  => 'fieldset',
      '#title' => t('qs_auth.form.pass.title'),
    ];

    $form['forget']['mail'] = $form['mail'];
    unset($form['mail']);

    $form['forget']['name'] = $form['name'];
    unset($form['name']);

    $form['forget']['name']['#title'] = t('qs_auth.form.pass.name.label');
    $form['forget']['name']['#attributes']['autocomplete'] = "off";
    $form['forget']['name']['#attributes']['autofocus'] = "autofocus";
    $form['forget']['name']['#description'] = t('qs_auth.form.pass.name.description');
    $form['forget']['name']['#placeholder'] = t('qs_auth.form.pass.name.placeholder');

    $form['#submit'][] = 'qs_auth_user_pass_form_submit';
  }

  if ($form_id == 'user_pass_reset') {
    $form['reset'] = [
      '#type'  => 'fieldset',
      '#title' => t('qs_auth.pass_reset_form.title'),
    ];

    $form['forget']['name']['#description'] = t('qs_auth.form.pass.name.description');
    $form['forget']['name']['#description'] = t('qs_auth.form.pass.name.description');

    // Help.
    $form['reset']['help']['#markup'] = t('qs_auth.form.reset.help');

    // Message.
    $args = $form['message']['#markup']->getArguments();
    $form['reset']['message']['#markup'] = t('qs_auth.form.reset.message @mail @expiration_date', [
      '@mail'            => $args['%user_name'],
      '@expiration_date' => $args['%expiration_date'],
    ]);

    unset($form['help']);
    unset($form['message']);
  }
}

/**
 * Custom submit handler for pass recovery form.
 */
function qs_auth_user_pass_form_submit($form, $form_state) {
  $form_state->setRedirect('qs_auth.pass.confirmation', [], []);
}
