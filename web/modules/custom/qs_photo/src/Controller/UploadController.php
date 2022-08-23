<?php

namespace Drupal\qs_photo\Controller;

use Drupal\Component\Utility\Environment;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Controller receiving uppy call to upload/save an image.
 */
class UploadController extends ControllerBase {
  use MessengerTrait;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * Listing of allowed extensions.
   *
   * @var string
   */
  protected $extensions = 'png gif jpg jpeg';

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Construct a new UploadController object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\qs_acl\Service\AccessControl $acl
   *   The access control.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function __construct(RequestStack $request_stack, AccessControl $acl) {
    $this->requestStack = $request_stack;
    $this->acl = $acl;
    $this->nodeStorage = $this->entityTypeManager()->getStorage('node');
  }

  /**
   * Checks access for adding file in the given event.
   *
   * Custom access checker does not have access to Request. So inject the
   * RequestStack.
   * see https://www.drupal.org/project/drupal/issues/2786941
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account) {
    $access = AccessResult::forbidden();
    $request = $this->requestStack->getCurrentRequest();

    // Prevent upload if event parameter is not set.
    if (!$request->request->has('event') || empty($request->get('event'))) {
      return AccessResult::forbidden();
    }

    $event = $this->nodeStorage->load($request->get('event'));

    if (!$event || $event->bundle() !== 'event') {
      return AccessResult::forbidden();
    }

    $activity = $event->field_activity->entity;

    // Prevent upload if the current user doesn't have access to photos.
    if ($this->acl->hasAccessPhoto($activity)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      $container->get('request_stack'),
      $container->get('qs_acl.access_control')
    );
  }

  /**
   * Upload function.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Return data about the uploaded file or an error message.
   */
  public function upload(Request $request) {
    $upload_validators = [
      'file_validate_extensions' => [$this->extensions],
      'file_validate_size' => [Environment::getUploadMaxSize()],
      'qs_photo_file_validate_image_max_resolution' => ['10000x10000'],
    ];

    $files = file_save_upload(0, $upload_validators, 'private://photos');

    // Get errors throw by file_save_upload.
    $messages = $this->messenger()->messagesByType(MessengerInterface::TYPE_ERROR);

    // Clear the error bag for potential next call.
    $this->messenger()->deleteByType(MessengerInterface::TYPE_ERROR);

    // Return error(s) to Uppy.
    if ($messages && !empty($messages)) {
      $errors = [];

      foreach ($messages as $error) {
        $errors[] = strip_tags($error->__toString());
      }

      return new JsonResponse([
        'error' => implode('. ', $errors),
      ], 400);
    }

    // Return the file which will be processed later.
    $data = [];

    foreach ($files as $file) {
      $data[] = [
        'fid' => $file->fid->value,
        'uuid' => $file->uuid->value,
      ];
    }

    return new JsonResponse([
      'success' => TRUE,
      'data' => $data,
    ]);
  }

}
