<?php

namespace Drupal\qs_sharing\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirect offers to the offer's type canonical page.
 */
class RedirectSubscriber implements EventSubscriberInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [
        ['offerRedirect'],
        ['requestRedirect'],
      ],
    ];
  }

  /**
   * Redirect Offer canonical access.
   *
   * It verifies the current route is Offer canonical access then
   * redirect on the Offer's type page.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   Event subscriber.
   */
  public function offerRedirect(RequestEvent $event): void {
    $node = $this->routeMatch->getParameter('node');

    if ($this->routeMatch->getRouteName() === 'entity.node.canonical' && $node->bundle() === 'offer') {
      $offerType = $node->field_offer_type->entity;
      $destination = Url::fromRoute('entity.node.canonical', ['node' => $offerType->id()]);
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

  /**
   * Redirect Request canonical access.
   *
   * It verifies the current route is Request canonical access then
   * redirect on the Request collection  page.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   Event subscriber.
   */
  public function requestRedirect(RequestEvent $event): void {
    $node = $this->routeMatch->getParameter('node');

    if ($this->routeMatch->getRouteName() === 'entity.node.canonical' && $node->bundle() === 'request') {
      $community = $node->field_community->entity;
      $destination = Url::fromRoute('qs_sharing.collection.request', ['community' => $community->id()]);
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

}
