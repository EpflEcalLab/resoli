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
  protected $acl;

  /**
   * Activity Manager Service.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

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
   * The file Storage.
   *
   * @var \Drupal\file\FileStorageInterface
   */
  protected $fileStorage;

  /**
   * The image factory.
   *
   * @var \Drupal\Core\Image\ImageFactory
   */
  protected $imageFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->currentUser     = $this->getCurrentUser();
    $this->acl             = $this->getAcl();
    $this->nodeStorage     = $this->getNodeStorage();
    $this->fileStorage     = $this->getFileStorage();
    $this->activityManager = $this->getActivityManager();
    $this->imageFactory    = $this->getImageFactory();
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
      // Show only activity where user has access &
      // with at least one past event.
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
      '#title'     => $this->t('qs_photo.add.form.activity'),
      '#type'      => 'select',
      '#multiple'  => FALSE,
      '#required'  => FALSE,
      '#options'   => $fallback,
      '#validated' => TRUE,
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
      '#validated' => TRUE,
      '#attributes'    => [
        'selectize'    => TRUE,
        'class'        => [
          'selectize-activity',
          'selectize-events',
        ],
        'data-sort-field' => '',
      ],
      '#theme_wrappers' => [
        'form_element',
        'container__center',
      ],
    ];

    $form['step-3'] = [
      '#type' => 'fieldset',
      '#description' =>
      $this->t('qs_photo.add.form.step3.description') .
      '<div class="text-center mb-3">' .
      $this->t('qs_photo.add.form.step3.helper @file_validate_extensions @file_validate_size @file_validate_image_resolution', [
        '@file_validate_extensions'       => 'png gif jpg jpeg',
        '@file_validate_size'             => $this->humanFilesize(file_upload_max_size()),
        '@file_validate_image_resolution' => '5000x5000',
      ]) .
      '</div>',
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
        'file_validate_extensions'          => ['png gif jpg jpeg'],
        'file_validate_size'                => [file_upload_max_size()],
        // We don't use the Drupal file_validate_image_resolution
        // because this will alter the original image & remove original EXIF.
        'qs_file_validate_image_resolution' => ['5000x5000'],
      ],
    ];

    $form['step-4'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_photo.add.form.step4.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_photo.add.form.step4'),
        'class' => [
          'text-center',
        ],
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
        'outline' => TRUE,
        'icon' => 'check',
        'icon_left' => TRUE,
        'class' => [
          'js-form-normal',
          'col-md-8',
          'mx-auto',
          'mb-3',
        ],
      ],
    ];

    $form['step-4']['comment'] = [
      '#type' => 'submit',
      '#name' => 'comment',
      '#value' => $this->t('qs_photo.add.form.comment'),
      '#attributes' => [
        'outline' => TRUE,
        'icon' => 'comment',
        'icon_left' => TRUE,
        'class' => [
          'js-form-normal',
          'col-md-8',
          'mx-auto',
          'mb-3',
        ],
      ],
    ];

    $form['step-4']['story'] = [
      '#type' => 'submit',
      '#name' => 'story',
      '#value' => $this->t('qs_photo.add.form.story'),
      '#attributes' => [
        'outline' => TRUE,
        'icon' => 'story',
        'icon_left' => TRUE,
        'onclick' => 'alert("Cette fonctionnalité n\'est pas encore disponible.");return false;',
        'class' => [
          'js-form-normal',
          'col-md-8',
          'mx-auto',
          'mb-3',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $event_nid = $form_state->getValue('event');
    $event = $this->getNodeStorage()->load($event_nid);
    $activity = NULL;

    // Detect illegal event or activity POST.
    if ($event && $event->bundle() === 'event') {
      $activity = $event->field_activity->entity;
    }
    else {
      $form_state->setErrorByName('[step-1][activity]', $this->t('qs.form.upload.illegal_choice'));
    }

    // Assert activity is filled.
    if (!$form_state->getValue('activity')) {
      $form_state->setErrorByName('[step-1][activity]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['step-1']['activity']['#title']]));
    }

    // Assert event is filled.
    if (!$form_state->getValue('event')) {
      $form_state->setErrorByName('[step-2][event]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['step-2']['event']['#title']]));
    }

    // Check write access of activity.
    if ($activity && $event
      && !$this->acl->hasWriteAccessPhoto($activity)) {
      $form_state->setErrorByName('[step-1][activity]', $this->t('qs.form.upload.illegal_choice'));
    }

    $all_files = $this->getRequest()->files->get('files', []);
    $file_upload = $all_files['photos'];

    // Prepare uploaded files info. Representation is slightly different
    // for multiple uploads and we fix that here.
    $uploaded_files = $file_upload;
    if (!is_array($file_upload)) {
      $uploaded_files = [$file_upload];
    }

    // Ensure we have the file uploaded.
    if (!$uploaded_files) {
      $form_state->setErrorByName('[step-3][photos]', $this->t('qs.form.upload.at_least_one'));
      return;
    }

    foreach ($uploaded_files as $file_info) {
      // Check for file upload errors and return FALSE for this file if a
      // lower level system error occurred. For a complete list of errors:
      // See http://php.net/manual/features.file-upload.errors.php.
      switch ($file_info->getError()) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
          $form_state->setErrorByName('[step-3][photos]', $this->t('The file %file could not be saved because it exceeds %maxsize, the maximum allowed size for uploads.', ['%file' => $file_info->getFilename(), '%maxsize' => format_size(file_upload_max_size())]), 'error');
          continue;

        case UPLOAD_ERR_PARTIAL:
        case UPLOAD_ERR_NO_FILE:
          $form_state->setErrorByName('[step-3][photos]', $this->t('The file %file could not be saved because the upload did not complete.', ['%file' => $file_info->getFilename()]), 'error');
          continue;

        case UPLOAD_ERR_OK:
          // Final check that this is a valid upload, if it isn't, use the
          // default error handler.
          if (is_uploaded_file($file_info->getRealPath())) {
            $form_state->setErrorByName('[step-3][photos]', $this->t('The file %file could not be saved. An unknown error has occurred.', ['%file' => $file_info->getFilename()]), 'error');
            return;
          }

        default:
          // Unknown error.
          $form_state->setErrorByName('[step-3][photos]', $this->t('The file %file could not be saved. An unknown error has occurred.', ['%file' => $file_info->getFilename()]), 'error');
          continue;
      }

      // Begin building file entity.
      $file = $this->fileStorage->create([
        'filename' => $file_info->getClientOriginalName(),
        'uri'      => $file_info->getRealPath(),
        'filesize' => $file_info->getSize(),
      ]);

      // Custome max width/height validations to prevent GD library to crash.
      list($width, $height) = explode('x', $form['step-3']['photos']['#upload_validators']['qs_file_validate_image_resolution'][0]);
      $image = $this->imageFactory->get($file->getFileUri());
      if ($image->getWidth() > $width || $image->getHeight() > $height) {
        $form_state->setErrorByName('[step-3][photos]', $this->t('The file %file could not be saved because it exceeds the maximum size of %widthx%height.', [
          '%file'   => $file_info->getClientOriginalName(),
          '%width'  => $width,
          '%height' => $height,
        ]), 'error');
      }

      // Apply Drupal validators to asserts the images fit our requirements.
      // We can't use the `file_validate_image_resolution` because it will
      // alter the file size & remove original EXIF.
      $errors = file_validate($file, $form['step-3']['photos']['#upload_validators']);
      if (!$errors) {
        continue;
      }

      // Show Drupal validators errors messages.
      $form_state->setErrorByName('[step-3][photos]', $this->t('The specified file %name could not be uploaded.', ['%name' => $file_info->getClientOriginalName()]));
      foreach ($errors as $error) {
        $form_state->setErrorByName('[step-3][photos]', $error->render());
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

    // Save the uploaded images on the private folder.
    $photos = file_save_upload('photos', [], 'private://photos');

    $nodes = [];
    foreach ($photos as $photo) {
      $node = $this->getPhotoManager()->create($event, $photo);
      $nodes[] = $node->id();
    }

    drupal_set_message($this->t("qs_photo.form.add.success @number @event @activity", [
      '@activity' => $activity->getTitle(),
      '@event'    => $event->getTitle(),
      '@number'   => count($nodes),
    ]));

    $trigger = $form_state->getTriggeringElement();
    switch ($trigger['#name']) {
      case 'comment':
        $form_state->setRedirect('qs_photo.form.comments', [
          'activity' => $activity->id(),
          'photos'   => $nodes,
        ]);
        break;

      case 'story':
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
      'nid'   => '_none',
      'title' => $this->t('qs.form.select'),
    ];

    $activity_nid = $form_state->getValue('activity');

    if ($activity_nid) {

      $activity = $this->getNodeStorage()->load((int) $activity_nid);
      $events   = $this->getEventManager()->getAllPrev($activity);

      if ($events) {
        $select_options = [];
        foreach ($events as $event) {
          $select_options[] = [
            'nid'   => $event->id(),
            'title' => $event->field_end_at->date->format('d.m.Y') . ' - ' . $event->getTitle(),
          ];
        }
      }
    }

    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand('#edit-event', 'selectizeClearOptions'));
    $response->addCommand(new InvokeCommand('#edit-event', 'selectizeAddOptions', [$select_options]));
    return $response;
  }

  /**
   * Get a human readable file size.
   *
   * @param int $bytes
   *   The original file size in bytes.
   * @param int $decimals
   *   The number of final decimals.
   *
   * @return string
   *   The human readable file size.
   */
  public function humanFilesize($bytes, $decimals = 2) {
    $size = ['o', 'ko', 'Mo', 'Go', 'To', 'Po', 'Eo', 'Zo', 'Yo'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' [' . @$size[$factor] . ']';
  }

}
