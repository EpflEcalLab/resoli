<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\qs_site\Form\InlineErrorFormTrait;

/**
 * FormBasic class.
 */
abstract class FormBasic extends FormBase {
  use InlineErrorFormTrait;

  protected $container;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container);
  }

  /**
   * Lazy loading for the Quartiers-Solidaires Access Control service.
   *
   * @return \Drupal\qs_acl\Service\AccessControl
   *   Return the Quartiers-Solidaires Access Control.
   */
  protected function getAcl() {
    return $this->container->get('qs_acl.access_control');
  }

  /**
   * Lazy loading for the Quartiers-Solidaires Activity Manager service.
   *
   * @return \Drupal\qs_activity\Service\ActivityManager
   *   Return the Quartiers-Solidaires Activity Manager.
   */
  protected function getActivityManager() {
    return $this->container->get('qs_activity.activity_manager');
  }

  /**
   * Lazy loading for the Quartiers-Solidaires Event Manager service.
   *
   * @return \Drupal\Core\Routing\UrlGeneratorInterface
   *   Return the Quartiers-Solidaires Event Manager.
   */
  protected function getEventManager() {
    return $this->container->get('qs_activity.event_manager');
  }

  /**
   * Lazy loading for the Drupal URL Generator service.
   *
   * @return \Drupal\Core\Routing\UrlGeneratorInterface
   *   Return the Drupal URL Generator.
   */
  protected function getUrlGenerator() {
    return $this->container->get('url_generator');
  }

  /**
   * Lazy loading for the Drupal entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   Return the Drupal entity type manager.
   */
  protected function getEntityTypeManager() {
    return $this->container->get('entity_type.manager');
  }

  /**
   * Return the node storage.
   *
   * @return \Drupal\node\NodeStorageInterface
   *   Return the node storage.
   */
  protected function getNodeStorage() {
    return $this->getEntityTypeManager()->getStorage('node');
  }

  /**
   * Return the term storage.
   *
   * @return \Drupal\taxonomy\TermStorageInterface
   *   Return the term storage.
   */
  protected function getTermStorage() {
    return $this->getEntityTypeManager()->getStorage('taxonomy_term');
  }

  /**
   * Lazy loading for the Drupal current user account proxy.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   Return The current user account proxy.
   */
  protected function getCurrentUser() {
    return $this->container->get('current_user');
  }

  /**
   * Lazy loading for the Quartiers-Solidaires Privilege Manager service.
   *
   * @return \Drupal\qs_acl\Service\PrivilegeManager
   *   Return the Quartiers-Solidaires Privilege Manager.
   */
  protected function getPrivilegeManager() {
    return $this->container->get('qs_acl.privilege_manger');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';
    return $form;
  }

  /**
   * Shortest date&time validator for all formats.
   *
   * @param string $date
   *   The date to validate.
   * @param string $format
   *   The format to validate.
   *
   * @return bool
   *   Does the given date match the requested format or not.
   */
  protected function validateDate($date, $format = 'Y-m-d H:i:s') {
    try {
      $d = DrupalDateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

}
