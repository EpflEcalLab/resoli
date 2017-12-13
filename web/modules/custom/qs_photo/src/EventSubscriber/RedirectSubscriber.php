<?php

namespace Drupal\qs_photo\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Subscribe to KernelEvents::REQUEST.
 *
 * Redirect photo to the photo by activity page.
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
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['photoRedirect'];
    return $events;
  }

  /**
   * Redirect Photo canonical access.
   *
   * It verify the current route is Photo canonical access then
   * redirect on the photos by activity page.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event subscriber.
   */
  public function photoRedirect(GetResponseEvent $event) {
    $node = $this->routeMatch->getParameter('node');
    if ($this->routeMatch->getRouteName() == 'entity.node.canonical' && $node->bundle() === 'photo') {
      $activity = $node->field_event->entity->field_activity->entity;
      $destination = Url::fromRoute('qs_photo.activity', ['activity' => $activity->id()]);
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

}
