<?php

namespace Drupal\qs_activity\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Subscribe to KernelEvents::REQUEST.
 *
 * Redirect event to the activity page.
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
    $events[KernelEvents::REQUEST][] = ['communityRedirect'];
    $events[KernelEvents::REQUEST][] = ['eventRedirect'];
    return $events;
  }

  /**
   * Redirect Event canonical access.
   *
   * It verify the current route is Event canonical access then
   * redirect on the activity page.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event subscriber.
   */
  public function eventRedirect(GetResponseEvent $event) {
    $node = $this->routeMatch->getParameter('node');
    if ($this->routeMatch->getRouteName() == 'entity.node.canonical' && $node->bundle() === 'event') {
      $destination = Url::fromRoute('entity.node.canonical', ['node' => $node->field_activity->target_id]);
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

  /**
   * Redirect Community canonical access.
   *
   * It verify the current route is Community canonical access then
   * redirect on the activities page.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event subscriber.
   */
  public function communityRedirect(GetResponseEvent $event) {
    $term = $this->routeMatch->getParameter('taxonomy_term');
    if ($this->routeMatch->getRouteName() == 'entity.taxonomy_term.canonical' && $term->bundle() === 'communities') {
      $destination = Url::fromRoute('qs_community.welcome', ['community' => $term->id()]);
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

}
