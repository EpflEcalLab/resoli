<?php

namespace Drupal\qs_photo\Controller;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileSystem;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\image\ImageStyleInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Stream one image to be displayed in <img> tag.
 *
 * The stream is necessary to avoid direct access on image file in order tp
 * to protect files with access checking.
 */
class StreamController extends ControllerBase {

  /**
   * Provides helpers to operate on files and stream wrappers.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fso;

  /**
   * The lock backend.
   *
   * @var \Drupal\Core\Lock\LockBackendInterface
   */
  protected $lock;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, FileSystem $fso, LockBackendInterface $lock) {
    $this->acl = $acl;
    $this->fso = $fso;
    $this->lock = $lock;
  }

  /**
   * Checks access for accessing private image.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $photo
   *   Run access checks for this photo.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $photo) {
    $access = AccessResult::forbidden();

    $event = $photo->field_event->entity;
    $activity = $event->field_activity->entity;

    if ($photo->bundle() === 'photo' && $this->acl->hasAccessPhoto($activity)) {
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
      // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('file_system'),
      $container->get('lock')
    );
  }

  /**
   * Download page.
   */
  public function stream(NodeInterface $photo, ?ImageStyleInterface $image_style = NULL) {
    $file_download = NULL;

    if ($photo->bundle() !== 'photo') {
      throw new NotFoundHttpException();
    }

    if ($photo->get('field_image')->isEmpty()) {
      throw new NotFoundHttpException();
    }

    // Get the image file from the photo entity.
    $file = $photo->get('field_image')->entity;
    $file_uri = $file->getFileUri();
    $path = $this->fso->realpath($file_uri);

    if (!is_file($path)) {
      throw new NotFoundHttpException();
    }

    $file_download = (object) [
      'path' => $path,
      'filename' => $file->getFilename(),
    ];

    // Generate or steam the requested image style.
    if ($image_style) {
      // Assert the image style doesn't already exist.
      $image_style_uri = $image_style->buildUri($file_uri);
      $image_style_path = $this->fso->realpath($image_style_uri);

      if (!is_file($image_style_path)) {
        $lock_name = 'image_style_deliver:' . $image_style->id() . ':' . Crypt::hashBase64($file_uri);
        $lock_acquired = $this->lock->acquire($lock_name);

        if (!$lock_acquired) {
          // Tell client to retry again in 3 seconds. Currently no browsers are
          // known to support Retry-After.
          throw new ServiceUnavailableHttpException(3, 'Image generation in progress. Try again shortly.');
        }

        if (!empty($lock_acquired)) {
          $this->lock->release($lock_name);
        }

        // Create the new image derivative.
        $image_style->createDerivative($file_uri, $image_style_uri);
        $image_style_path = $this->fso->realpath($image_style_uri);
      }
      $file_download = (object) [
        'path' => $image_style_path,
        'filename' => $file->getFilename(),
      ];
    }

    $logger = $this->getLogger('qs_photo');

    try {
      // Stream the file.
      $user = $this->currentUser()->getAccount();
      $now = new \DateTime();

      $logger->info($this->t('@user (@uid) stream `@file` (@nid) at @now.', [
        '@user' => $user->name,
        '@uid' => $user->id(),
        '@file' => $file_download->filename,
        '@nid' => $photo->nid->value,
        '@now' => $now->format('d-m-Y H:i:s'),
      ]));

      $response = new BinaryFileResponse($file_download->path);

      // Create the disposition of the file.
      $response->trustXSendfileTypeHeader();
      $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_INLINE,
        $file_download->filename
      );

      // Dispatch request.
      return $response;
    }
    catch (\Exception $e) {
      $logger->error($e->getMessage());

      // Note: Don't display the default drupal message for security reason,
      // may contain the file name.
      throw new NotFoundHttpException();
    }
  }

}
