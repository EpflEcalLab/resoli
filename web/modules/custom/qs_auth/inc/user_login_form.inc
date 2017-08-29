<?php

/**
 * @file
 * User Login form hook.
 */

/**
 * Alter Drupal login form with custom markup and submission.
 */
function qs_auth_form_user_login_form_alter(&$form, $form_state) {
  $form['#theme_wrappers'] = [
    'form__fullpage',
  ];

  $form['login'] = [
    '#type'  => 'fieldset',
    '#title' => t('qs_auth.login_form.title'),
  ];

  $form['login']['name'] = $form['name'];
  unset($form['name']);
  $form['login']['name']['#title'] = t('qs_auth.login_form.name.label');
  $form['login']['name']['#attributes']['autofocus'] = "autofocus";
  $form['login']['name']['#description'] = t('qs_auth.login_form.name.description');
  $form['login']['name']['#placeholder'] = t('qs_auth.login_form.name.placeholder');

  $form['login']['pass'] = $form['pass'];
  unset($form['pass']);
  $form['login']['pass']['#title'] = t('qs_auth.login_form.password.label');
  $form['login']['pass']['#description'] = '';
  $form['login']['pass']['#placeholder'] = t('qs_auth.login_form.password.placeholder');

  $form['#submit'][] = 'qs_auth_user_login_form_submit';
  $form['actions']['submit']['#value'] = t('qs_auth.login_form.submit');
  $form['actions']['submit']['#attributes']['class'] = ['mx-auto d-block btn btn-outline-secondary mt-3 btn-outline-invert'];
  $form['actions']['submit']['#attributes']['icon'] = ['check'];
}

/**
 * Custom submit handler for login form.
 *
 * Set redirect when the GET parameter destination doesn't exists.
 */
function qs_auth_user_login_form_submit($form, $form_state) {
  // Fallback redirection.
  $form_state->setRedirect('<front>', [], []);

  // Load services.
  $acl = \Drupal::service('qs_acl.access_control');

  // User with Bypass access can go everywhere, so redirect him on communities.
  if ($acl->hasBypass()) {
    $form_state->setRedirect('qs_auth.communities', [], []);
    return;
  }

  // Check the current user has apply to at least one community.
  if (!$acl->hasCommunity()) {

    // Get the first pending approval.
    $approvals = $acl->getPendingApprovalCommunities();
    $approval = reset($approvals);

    // If has no communities access but already asked for approval,
    // go to the approval page.
    if ($approval) {
      $form_state->setRedirect('qs_auth.approval', ['community' => $approval->id()], []);
      return;
    }
    // Otherwise go to the form communities to apply.
    else {
      $form_state->setRedirect('qs_auth.communities.apply', [], []);
      return;
    }
  }

  // Check user is a beginner, so need unboarding.
  if ($acl->isBeginner()) {
    // $form_state->setRedirect('qs_onboarding.welcome', [], []);.
  }

  // If the user has multiple communities, redirect him on the communities page.
  // to choose which community he want to connect on.
  if ($acl->hasMultipleCommunities()) {
    $form_state->setRedirect('qs_auth.communities', [], []);
    return;
  }

  // Get the first communities.
  $communities = $acl->getCommunities();
  $community = reset($communities);

  // The user has only 1 community access so connect it on.
  if ($community) {
    $form_state->setRedirect('qs_activity.collection.themes', ['community' => $community->id()], []);
    return;
  }
}
