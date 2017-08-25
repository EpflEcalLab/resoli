<?php

namespace Drupal\qs_site\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Provide custom Entity Access.
 */
class EntityAccess implements EventSubscriberInterface {

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
   * Disabled $disabledVocabularies terms vid.
   *
   * @var array
   */
  private $disabledVocabularies = [
    'themes',
  ];

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['isDisabledTaxonomy'];
    return $events;
  }

  /**
   * It verify the requested page is a disabled term view and shut-it-down.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   A response for a request.
   */
  public function isDisabledTaxonomy(GetResponseEvent $event) {
    $term = $this->routeMatch->getParameter('taxonomy_term');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name == 'entity.taxonomy_term.canonical' && in_array($term->vid->target_id, $this->disabledVocabularies)) {
      $dest = Url::fromRoute('<front>')->toString();
      $event->setResponse(RedirectResponse::create($dest));
    }
  }

}
