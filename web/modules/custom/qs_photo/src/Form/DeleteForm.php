<?php

namespace Drupal\qs_photo\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to remove photos by batch.
 */
class DeleteForm extends FormBasic {

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

    $photos_params = $this->getRequest()->query->get('photos');

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
      if (!$this->acl->hasWriteAccessPhoto($event_activity)) {
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
    $user = $this->getCurrentUser();

    $photos_params = $this->getRequest()->query->get('photos');
    $photos = $this->nodeStorage->loadMultiple($photos_params);

    // Save the activity for submission.
    $form_state->set('activity', $activity->id());

    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    $form['#attributes'] = [
      'title' => $activity->getTitle(),
      'description' => $this->t('qs_photo.form.delete.warning'),
      'icon' => 'trash',
      'theme' => 'danger',
    ];

    $form['#floating_buttons'][] = [
      'icon' => 'trash',
      'label' => $this->t('qs.photo.delete'),
      'active' => TRUE,
    ];

    $form['gallery'] = [
      '#theme' => 'qs_photo_delete_gallery_form',
      '#variables' => ['photos' => $photos, 'activity' => $activity],
    ];

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

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('qs.form.cancel'),
      '#url' => Url::fromRoute('qs_photo.user.form.manage', [
        'activity' => $activity->id(),
        'user' => $user->id(),
      ]),
      '#attributes' => [
        'class' => [
          'btn btn-outline-danger btn-outline-invert',
          'mb-2',
        ],
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => [
        'class' => [
          'text-danger',
          'mb-2',
        ],
        'icon' => 'trash',
        'icon_left' => TRUE,
        'white' => TRUE,
      ],
      '#value' => $this->t('qs.form.delete_submit'),
    ];

    // Remove unload script.
    $form['#attached']['library'] = [];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_photo_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->get('activity'));
    $user = $this->getCurrentUser();
    $photos_params = $this->getRequest()->query->get('photos');

    $this->messenger()->addMessage($this->t('qs_photo.form.delete.success @activity @number', [
      '@activity' => $activity->getTitle(),
      '@number' => \count($photos_params),
    ]));

    $form_state->setRedirect('qs_photo.user.form.manage', [
      'activity' => $activity->id(),
      'user' => $user->id(),
    ]);

    // Delete the photos.
    $photos = $this->nodeStorage->loadMultiple($photos_params);

    foreach ($photos as $photo_nid => $comment) {
      $photo = $this->nodeStorage->load($photo_nid);
      $photo->delete();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

}
