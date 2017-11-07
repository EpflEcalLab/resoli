<?php

namespace Drupal\qs_photo\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * PhotoAddForm class.
 */
class AddForm extends FormBasic {

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
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->acl             = $this->getAcl();
    $this->nodeStorage     = $this->getNodeStorage();
    $this->activityManager = $this->getActivityManager();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_photo_add_form';
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

    // From the container, inject services.
    $this->acl             = $this->getAcl();
    $this->activityManager = $this->getActivityManager();
    $this->nodeStorage     = $this->getNodeStorage();
    $this->currentUser     = $this->getCurrentUser();

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#title'] = $this->t('qs_photo.form.add.title_form');

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__fullpage__multistep',
    ];

    // Save the community for submission.
    $form['community'] = [
      '#type'  => 'hidden',
      '#value' => $community->id(),
    ];

    $form['step-1'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_photo.add.form.step1.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_photo.add.form.step1'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    if ($this->acl->hasBypass()) {
      // Show every activity in this community for bypass user.
      $nids = $this->activityManager->getThemed($community);
      $activities = $this->nodeStorage->loadMultiple($nids);
    }
    else {
      // Show only activity where user has access.
      $activities = $this->activityManager->getByUserPhoto($community, $this->currentUser);
    }

    $fallback = [];
    $select_options = [];
    foreach ($activities as $activity) {
      $fallback[$activity->id()] = $activity->getTitle();
      $select_options[] = [
        'nid'         => $activity->id(),
        'title'       => $activity->getTitle(),
      ];
    }

    $form['step-1']['activity'] = [
      '#title'    => $this->t('qs_photo.add.form.activity'),
      '#type'     => 'select',
      '#multiple'      => FALSE,
      '#required' => FALSE,
      '#options'  => $fallback,
      '#attributes'    => [
        'selectize'    => TRUE,
        'class'        => ['selectize-activity'],
        'data-options' => json_encode($select_options),
      ],
      '#theme_wrappers' => [
        'form_element',
        'container__center',
      ],
      '#ajax'     => [
        'callback' => [$this, 'selectEventAjax'],
        'wrapper'  => 'model_wrapper',
      ],
    ];

    $form['step-2'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_photo.add.form.step2.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_photo.add.form.step2'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['step-2']['event'] = [
      '#title'     => $this->t('qs_photo.add.form.event'),
      '#type'      => 'select',
      '#required'  => FALSE,
      '#options'   => ['_none' => $this->t('qs.form.select')],
      '#attributes'    => [
        'selectize'    => TRUE,
        'class'        => [
          'selectize-activity',
          'selectize-events',
        ],
      ],
      '#theme_wrappers' => [
        'form_element',
        'container__center',
      ],
    ];

    $form['step-3'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_photo.add.form.step3.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_photo.add.form.step3'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['step-3']['photos'] = [
      '#title'      => $this->t('qs_photo.add.form.photos'),
      '#type'       => 'file',
      '#multiple'   => TRUE,
      '#required'   => FALSE,
      '#upload_validators' => [
        'file_validate_extensions' => ['png gif jpg jpeg'],
        'file_validate_size' => [file_upload_max_size()],
      ],
    ];

    $form['step-4'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_photo.add.form.step4.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_photo.add.form.step4'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['step-4']['publish'] = [
      '#type' => 'submit',
      '#name' => 'publish',
      '#value' => $this->t('qs_photo.add.form.publish'),
      '#attributes' => [
        'class' => [
          'js-form-normal',
        ],
      ],
    ];

    $form['step-4']['comment'] = [
      '#type' => 'submit',
      '#name' => 'comment',
      '#value' => $this->t('qs_photo.add.form.comment'),
      '#attributes' => [
        'class' => [
          'js-form-normal',
        ],
      ],
    ];

    $form['step-4']['story'] = [
      '#type' => 'submit',
      '#name' => 'story',
      '#value' => $this->t('qs_photo.add.form.story'),
      '#attributes' => [
        'class' => [
          'js-form-normal',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $files = $this->getRequest()->files->get('files');

    if (empty($files['photos'])) {
      $form_state->setErrorByName('[step-3][photos]', $this->t('qs.form.upload.at_least_one'));
    }
    else {
      $this->photos = file_save_upload(
        'photos',
        $form['step-3']['photos']['#upload_validators'],
        'private://photos'
      );
      // Ensure we have the file uploaded.
      if ($this->photos === NULL) {
        $form_state->setErrorByName('[step-3][photos]', $this->t('qs.form.upload.at_least_one'));
      }
      else {
        foreach ($this->photos as $file) {
          if (!$file) {
            $form_state->setErrorByName('[step-3][photos]', $this->t('qs.form.error.something_went_wrong'));
            break;
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $event_nid = $form_state->getValue('event');
    $event = $this->getNodeStorage()->load($event_nid);
    $activity = $event->field_activity->entity;
    dump($activity);
    dump($event);
    die();

    foreach ($this->photos as $photo) {
      $this->getPhotoManager()->create($event, $photo);
    }

    drupal_set_message($this->t("qs_photo.form.add.success @number @event @activity", [
      '@activity' => $activity->getTitle(),
      '@event'    => $event->getTitle(),
      '@number'   => count($this->photos),
    ]));

    $trigger = $form_state->getTriggeringElement();
    switch ($trigger['#name']) {
      case 'comment':
      case 'story':
        $form_state->setRedirect('qs_photo.activity', ['activity' => $activity->id()]);
        break;

      case 'publish':
      default:
        $form_state->setRedirect('qs_photo.activity', ['activity' => $activity->id()]);
        break;
    }
  }

  /**
   * Called via Ajax to populate the Event field according Activity.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form model field structure.
   */
  public function selectEventAjax(array &$form, FormStateInterface $form_state) {
    $select_options[] = [
      'nid' => '_none',
      'title' => $this->t('qs.form.select'),
    ];

    $activity_nid = $form_state->getValue('activity');

    if ($activity_nid) {

      $activity     = $this->getNodeStorage()->load((int) $activity_nid);
      $events       = $this->getEventManager()->getAllPrev($activity);

      if ($events) {
        $select_options = [];
        foreach ($events as $event) {
          $select_options[] = [
            'nid'         => $event->id(),
            'title'       => $event->getTitle(),
          ];
        }
      }
    }

    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand('#edit-event', 'selectizeClearOptions'));
    $response->addCommand(new InvokeCommand('#edit-event', 'selectizeAddOptions', [$select_options]));
    return $response;
  }

}
