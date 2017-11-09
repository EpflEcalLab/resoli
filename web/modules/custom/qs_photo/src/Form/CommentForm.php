<?php

namespace Drupal\qs_photo\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * CommentForm class.
 */
class CommentForm extends FormBasic {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * Activity Manager Service.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  private $activityManager;

  /**
   * The current user account proxy.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The file storage.
   *
   * @var \Drupal\file\FileStorageInterface
   */
  protected $fileStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->acl             = $this->getAcl();
    $this->nodeStorage     = $this->getNodeStorage();
    $this->activityManager = $this->getActivityManager();
    $this->fileStorage     = $this->getFileStorage();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_photo_comments_form';
  }

  /**
   * Checks access for add photos in the given community.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::allowed();

    $photos_params = $this->getRequest()->query->get('photos');
    $photos = $this->nodeStorage->loadMultiple($photos_params);

    // Check write access of every photos.
    foreach ($photos as $photo) {
      $activity = $photo->field_event->entity->field_activity->entity;

      if (!$this->acl->hasWriteAccessPhoto($activity)) {
        $access = AccessResult::forbidden();
        break;
      }
    }
    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, TermInterface $community = NULL) {
    $form = parent::buildForm($form, $form_state);
    $form['#attributes']['title'] = $this->t('qs_photo.form.comment.title_form');
    $form['#tree'] = TRUE;

    $photos_params = $this->getRequest()->query->get('photos');

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    // Save the community for submission.
    $form['community'] = [
      '#type'  => 'hidden',
      '#value' => $community->id(),
    ];

    foreach ($photos_params as $nid) {
      $photo = $this->nodeStorage->load($nid);

      $form['photos'][$nid] = [
        '#type'     => 'textarea',
        '#required' => FALSE,
        '#theme' => ['textarea__photo'],
        '#placeholder' => $this->t('qs_photo.form.comment.photo_comment_placeholder'),
        '#default_value' => $photo->body->value,
        '#attributes' => [
          'title' => $this->t('qs_photo.form.comment.photo_comment_title'),
          'photo' => (int) $nid,
        ],
      ];
    }

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('qs.form.submit'),
      '#attributes' => [
        'icon' => 'check',
        'modal' => TRUE,
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $community = $form_state->getValue('community');
    $user = $this->getCurrentUser();

    $photos = $form_state->getValue('photos');
    foreach ($photos as $photo_nid => $comment) {
      $photo = $this->nodeStorage->load($photo_nid);
      $photo->set('body', $comment);
      $photo->save();
    }

    drupal_set_message($this->t("qs_photo.form.comment.success @number", [
      '@number'   => count($photos),
    ]));

    $form_state->setRedirect('qs_photo.user.activities.collection', [
      'community' => $community,
      'user' => $user->id(),
    ]);
  }

}
