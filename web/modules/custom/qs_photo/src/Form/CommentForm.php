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
   * The Request stack.
   *
   * @var object|\Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

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
    $this->requestStack    = $this->getRequestStack();
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
    // TODO manage access.
    $access = AccessResult::forbidden();
    if ($this->acl->hasAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, TermInterface $community = NULL) {
    $form = parent::buildForm($form, $form_state);

    $masterRequest = $this->requestStack->getMasterRequest();
    $photosParameter = $masterRequest->query->get('photos');

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes'] = [
      'title' => $this->t('qs_photo.form.comment.title_form'),
    ];

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    // Save the community for submission.
    $form['community'] = [
      '#type'  => 'hidden',
      '#value' => $community->id(),
    ];

    foreach ($photosParameter as $nid) {
      $photo = $this->nodeStorage->load($nid);

      $form['photo'][$nid] = [
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
        'outline' => TRUE,
        'icon' => 'check',
        'icon_left' => TRUE,
      ],
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
    dump($form_state);
  }

}
