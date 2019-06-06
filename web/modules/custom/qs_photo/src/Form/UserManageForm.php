<?php

namespace Drupal\qs_photo\Form;

use Drupal\Core\Form\FormStateInterface;
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

    // Save the activity for submission.
    $form_state->set('activity', $activity->id());

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'title' => $this->t('qs_photo.user.form.manage.title_form @activity', ['@activity' => $activity->getTitle()]),
      'description' => $this->t('qs_photo.user.form.manage.description_form'),
      'theme' => 'primary',
    ];
    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    $form['#floating_buttons'][] = [
      'icon'   => 'picture',
      'label'  => $this->t('qs_photo.user.form.manage.title'),
      'active' => TRUE,
    ];

    $activity_id = $activity->id();
    $photos = $this->photoManager->getWritablePhotoByUser($activity, $user);
    $options = [];
    if ($photos) {
      foreach ($photos as $photo) {
        $hasComment = $photo->body->value ? 'true' : 'false';
        $options[$photo->id()] = "$activity_id|$hasComment";
      }
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

    if (empty($photos)) {
      $form['empty'] = [
        '#markup' => '<p class="text-center my-4">' . $this->t('qs_photos.photos_select.empty') . '</p>',
      ];
    }

    $form['actions'] = [
      '#type' => 'fieldset',
      '#theme_wrappers' => [
        'container__center',
      ],
      '#attributes' => [
        'sticky_footer' => TRUE,
        'class' => [
          'text-center',
        ],
      ],
    ];

    $form['actions']['comment'] = [
      '#type' => 'submit',
      '#name' => 'comment',
      '#attributes' => [
        'icon' => 'comment',
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom mb-2',
        ],
      ],
      '#value' => $this->t('qs_photos.photos_comment'),
    ];

    $form['actions']['delete'] = [
      '#type' => 'submit',
      '#name' => 'delete',
      '#attributes' => [
        'icon' => 'trash',
        'icon_left' => TRUE,
        'class' => [
          'btn-danger mb-2',
        ],
      ],
      '#value' => $this->t('qs_photos.photos_delete'),
    ];

    // Remove unload script.
    $form['#attached']['library'] = [];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Get every checked photos.
    $photos = $this->getCheckedPhotos($form_state);

    if (empty($photos)) {
      $form_state->setErrorByName('form', $this->t('qs_photo.user.form.manage.choose_one'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->getNodeStorage()->load($form_state->get('activity'));

    // Get every checked photos.
    $photos = $this->getCheckedPhotos($form_state);

    $trigger = $form_state->getTriggeringElement();
    switch ($trigger['#name']) {
      case 'comment':
        $form_state->setRedirect('qs_photo.form.comments', [
          'activity' => $activity->id(),
          'photos' => $photos,
        ]);
        break;

      case 'delete':
        $form_state->setRedirect('qs_photo.form.delete', [
          'activity' => $activity->id(),
          'photos' => $photos,
        ]);
        break;
    }
  }

  /**
   * Retrieve checked photos.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Collection of checked photos.
   */
  private function getCheckedPhotos(FormStateInterface $form_state) {
    // Get every checked photos.
    $photos = array_filter($form_state->getValue('photos'), function ($value) {
      return !empty($value);
    });
    return $photos;
  }

}
