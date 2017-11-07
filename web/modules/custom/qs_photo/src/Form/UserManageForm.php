<?php

namespace Drupal\qs_photo\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * UserManageForm class.
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
   * @param \Drupal\node\NodeInterface $activity
   *   The activity.
   * @param \Drupal\user\UserInterface $user
   *   The user.
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
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'title' => $this->t('qs_photo.user.form.manage.title_form @activity', ['@activity' => $activity->getTitle()]),
      'description' => $this->t('qs_photo.user.form.manage.description_form'),
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

    $form['select_all'] = [
      '#type' => 'link',
      '#title' => $this->t('qs_photos.photos_select_all'),
      '#url' => Url::fromRoute('<front>'),
      '#attributes' => [
        'class' => [
          'btn btn-outline-danger btn-outline-invert',
        ],
      ],
    ];

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

    $form['actions'] = [
      '#type' => 'fieldset',
      '#theme_wrappers' => [
        'container__center',
      ],
      '#attributes' => [
        'class' => [
          'text-center',
        ],
      ],
    ];

    $form['actions']['comment'] = [
      '#type' => 'submit',
      '#attributes' => [
        'icon' => 'comment',
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
        ],
      ],
      '#value' => $this->t('qs_photos.photos_comment'),
    ];

    $form['actions']['delete'] = [
      '#type' => 'submit',
      '#attributes' => [
        'icon' => 'trash',
        'icon_left' => TRUE,
        'class' => [
          'btn-danger',
        ],
      ],
      '#value' => $this->t('qs_photos.photos_delete'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // TODO implement validateForm.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO implement submitForm.
  }

}
