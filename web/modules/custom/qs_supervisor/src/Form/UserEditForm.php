<?php

namespace Drupal\qs_supervisor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;

/**
 * UserEditForm Class.
 */
class UserEditForm extends FormBase {

  /**
   * Checks access for account settings page.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\user\UserInterface $user
   *   Run access checks for this user.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account, UserInterface $user) {
    return AccessResult::forbidden();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_supervisor_account_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, UserInterface $user = NULL) {
    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('qs.form.submit'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
