<?php

namespace Drupal\qs_sharing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Confirmation page of created Requests for Sharing.
 */
class RequestConfirmationController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl) {
    $this->acl = $acl;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\node\NodeInterface $request
   *   Run access checks for this node.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access results.
   */
  public function access(NodeInterface $request): AccessResultInterface {
    $access = AccessResult::forbidden();

    if ($this->acl->hasBypass() || $request->get('uid')->target_id === $this->currentUser()->id()) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Request thanks page.
   */
  public function confirm(NodeInterface $request) {
    $community = $request->field_community->entity;

    $floating_buttons = [
      [
        'icon' => 'sharing',
        'label' => $this->t('qs_sharing.floating.dashboard'),
        'theme' => 'primary',
        'url' => Url::fromRoute('qs_sharing.sharing.dashboard', [
          'community' => $community->id(),
          'user' => $this->currentUser()->id(),
        ]),
      ],
    ];

    return [
      '#theme' => 'qs_sharing_confirmation_requests_page',
      '#variables' => [
        'community' => $community,
        'request' => $request,
        'floating_buttons' => $floating_buttons,
      ],
      '#cache' => [
        'tags' => $this->getCacheTags(),
        'contexts' => [
          'user',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
      $container->get('qs_acl.access_control')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
    $tags = [];

    if ($nodes) {
      foreach ($nodes as $node) {
        $tags[] = 'node:' . $node->id();
      }
    }

    return $tags;
  }

}
