<?php

namespace Drupal\qs_photo\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to comment photos by batch.
 */
class CommentForm extends FormBasic {

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->acl = $this->getAcl();
    $this->nodeStorage = $this->getNodeStorage();
  }

  /**
   * Checks access for add photos in the given community.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $activity
   *   The activity.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $activity) {
    $access = AccessResult::allowed();

    $photos_params = $this->getRequest()->query->all('photos');

    if (!$photos_params) {
      return AccessResult::forbidden();
    }

    $photos = $this->nodeStorage->loadMultiple($photos_params);

    // Check write access of every photos.
    foreach ($photos as $photo) {
      $event_activity = $photo->field_event->entity->field_activity->entity;

      // The photo doesn't belongs to the same activity as url.
      if ($event_activity->id() !== $activity->id()) {
        $access = AccessResult::forbidden();

        break;
      }

      // Has not write access for photos.
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
  public function buildForm(array $form, FormStateInterface $form_state, ?NodeInterface $activity = NULL) {
    $form = parent::buildForm($form, $form_state);
    $form['#attributes']['title'] = $this->t('qs_photo.form.comment.title_form');
    $form['#tree'] = TRUE;

    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'title' => $this->t('qs_photo.form.comment.title_form @activity', ['@activity' => $activity->getTitle()]),
      'class' => [
        'modal-body',
        'js-comment-form',
      ],
      'theme' => 'secondary',
    ];

    $form['#floating_buttons'][] = [
      'icon' => 'pencil',
      'label' => $this->t('qs_photo.form.comment.title'),
      'active' => TRUE,
    ];

    $photos_params = $this->getRequest()->query->all('photos');

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    // Save the activity for submission.
    $form_state->set('activity', $activity->id());

    foreach ($photos_params as $nid) {
      $photo = $this->nodeStorage->load($nid);

      $form['photos'][$nid] = [
        '#type' => 'textarea',
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
  public function getFormId() {
    return 'qs_photo_comments_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->get('activity'));
    $user = $this->getCurrentUser();

    $photos = $form_state->getValue('photos');

    foreach ($photos as $photo_nid => $comment) {
      $photo = $this->nodeStorage->load($photo_nid);
      $photo->set('body', $comment);
      $photo->save();
    }

    $this->messenger()->addMessage($this->t('qs_photo.form.comment.success @number @activity', [
      '@number' => \count($photos),
      '@activity' => $activity->getTitle(),
    ]));

    $form_state->setRedirect('qs_photo.user.form.manage', [
      'activity' => $activity->id(),
      'user' => $user->id(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

}
