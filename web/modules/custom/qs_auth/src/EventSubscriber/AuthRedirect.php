<?php

namespace Drupal\qs_auth\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Subscribe to KernelEvents::REQUEST.
 *
 * Events and redirect.
 */
class AuthRedirect implements EventSubscriberInterface {
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
    $events[KernelEvents::REQUEST][] = ['loginRedirect'];
    $events[KernelEvents::REQUEST][] = ['registerRedirect'];
    $events[KernelEvents::REQUEST][] = ['passRedirect'];
    return $events;
  }

  /**
   * Redirect login.
   *
   * It verify the current route is default drupal '/user/login' then
   * redirect on our custom one.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event subscriber.
   */
  public function loginRedirect(GetResponseEvent $event) {
    if ($this->routeMatch->getRouteName() == 'user.login') {
      $destination = Url::fromRoute('qs_auth.login');
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

  /**
   * Redirect register.
   *
   * It verify the current route is default drupal '/user/register' then
   * redirect on our custom one.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event subscriber.
   */
  public function registerRedirect(GetResponseEvent $event) {
    if ($this->routeMatch->getRouteName() == 'user.register') {
      $destination = Url::fromRoute('qs_auth.register');
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

  /**
   * Forget Password register.
   *
   * It verify the current route is default drupal '/user/password' then
   * redirect on our custom one.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event subscriber.
   */
  public function passRedirect(GetResponseEvent $event) {
    if ($this->routeMatch->getRouteName() == 'user.pass') {
      $destination = Url::fromRoute('qs_auth.pass');
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

}
