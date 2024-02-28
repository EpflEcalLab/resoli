<?php

namespace Drupal\qs_supervisor\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscribe to KernelEvents::REQUEST.
 *
 * Events and redirect for Accounts.
 */
class AccountRedirect implements EventSubscriberInterface {

  /**
   * The current user account proxy.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public function __construct(RouteMatchInterface $route_match, AccountInterface $current_user) {
    $this->routeMatch = $route_match;
    $this->currentUser = $current_user;
  }

  /**
   * User account dashboard.
   *
   * It verify the current route is default drupal '/user/{user}'
   * then redirect on our custom one.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event subscriber.
   */
  public function dashboardRedirect(GetResponseEvent $event) {
    if ($this->routeMatch->getRouteName() === 'entity.user.canonical') {
      $user = $event->getRequest()->get('user');
      $destination = Url::fromRoute('qs_supervisor.account.dashboard', ['user' => $user->id()]);
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

  /**
   * User Edit Form.
   *
   * It verify the current route is default drupal '/user/{user}/edit' as
   * non-admin then redirect on our custom one.
   * We also keep the administration page accessible for admin user.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event subscriber.
   */
  public function editFormRedirect(GetResponseEvent $event) {
    if (!$this->currentUser->hasPermission('access administration pages') && $this->routeMatch->getRouteName() === 'entity.user.edit_form') {
      $user = $event->getRequest()->get('user');
      $destination = Url::fromRoute('qs_supervisor.account.form.edit', ['user' => $user->id()]);
      $event->setResponse(new RedirectResponse($destination->toString()));
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['dashboardRedirect'];
    $events[KernelEvents::REQUEST][] = ['editFormRedirect'];

    return $events;
  }

}
