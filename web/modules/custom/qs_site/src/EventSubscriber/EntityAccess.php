<?php

namespace Drupal\qs_site\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
   * Disabled $disabledVocabularies terms vid.
   *
   * @var array
   */
  private $disabledVocabularies = [
    'themes',
  ];

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
    $events[KernelEvents::REQUEST][] = ['isDisabledTaxonomy'];

    return $events;
  }

  /**
   * It verify the requested page is a disabled term view and shut-it-down.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   A response for a request.
   */
  public function isDisabledTaxonomy(RequestEvent $event): void {
    $term = $this->routeMatch->getParameter('taxonomy_term');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.taxonomy_term.canonical' && \in_array($term->vid->target_id, $this->disabledVocabularies, TRUE)) {
      $dest = Url::fromRoute('<front>')->toString();
      $event->setResponse(new RedirectResponse($dest));
    }
  }

}
