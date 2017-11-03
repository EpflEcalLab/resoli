<?php

namespace Drupal\qs_photo\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * UserPhotosForm class.
 */
class UserManageForm extends FormBasic {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * Photo Manager Service.
   *
   * @var \Drupal\qs_photo\Service\PhotoManager
   */
  private $photoManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->acl          = $this->getAcl();
    $this->photoManager = $this->getPhotoManager();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_photo_user_manage_form';
  }

  /**
   * Checks access for managing photos in the given activity.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\user\UserInterface $user
   *   The user.
   * @param \Drupal\node\NodeInterface $activity
   *   The activity.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $activity, UserInterface $user) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessAccountDashboard($user, $account) && $this->acl->hasWriteAccessPhoto($activity, $user)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $activity = NULL, AccountInterface $user = NULL) {
    $form = parent::buildForm($form, $form_state);

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#title'] = $this->t('qs_photo.user.form.manage.title_form');
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'class' => [
        'modal-body',
      ],
    ];
    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    $photos = $this->photoManager->getWritablePhotoByUser($activity, $user);
    $options = [];
    foreach ($photos as $photo) {
      $options[$photo->id()] = $photo->getTitle();
    }

    $form['photos'] = [
      '#attributes' => [
        'required' => TRUE,
        'title'    => $this->t('qs_photos.photos_select'),
        'variant' => 'image',
      ],
      '#theme_wrappers' => [
        'checkboxes__image',
      ],
      '#type'          => 'checkboxes',
      '#required'      => FALSE,
      '#options'       => $options,
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
