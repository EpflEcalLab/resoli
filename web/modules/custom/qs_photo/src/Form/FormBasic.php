<?php

namespace Drupal\qs_photo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
   * @return \Drupal\qs_activity\Service\EventManager
   *   Return the Quartiers-Solidaires Event Manager.
   */
  protected function getEventManager() {
    return $this->container->get('qs_activity.event_manager');
  }

  /**
   * Lazy loading for the Quartiers-Solidaires Photo Manager service.
   *
   * @return \Drupal\qs_photo\Service\PhotoManager
   *   Return the Quartiers-Solidaires Photo Manager.
   */
  protected function getPhotoManager() {
    return $this->container->get('qs_photo.photo_manager');
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
   * Return the file storage.
   *
   * @return \Drupal\file\FileStorageInterface
   *   Return the term storage.
   */
  protected function getFileStorage() {
    return $this->getEntityTypeManager()->getStorage('file');
  }

  /**
   * Return the user storage.
   *
   * @return \Drupal\user\Entity\User
   *   Return the user storage.
   */
  protected function getUserStorage() {
    return $this->getEntityTypeManager()->getStorage('user');
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
    return $this->container->get('qs_acl.privilege_manager');
  }

  /**
   * Lazy loading for the Entity Field Manager.
   *
   * Manages the discovery of entity fields.
   *
   * @return \Drupal\Core\Entity\EntityFieldManagerInterface
   *   Return the Entity Field Manager.
   */
  protected function getEntityFieldManager() {
    return $this->container->get('entity_field.manager');
  }

  /**
   * Lazy loading for the Entity Field Manager.
   *
   * Manages the discovery of entity fields.
   *
   * @return \Drupal\Core\Entity\EntityFieldManagerInterface
   *   Return the Entity Field Manager.
   */
  protected function getImageFactory() {
    return $this->container->get('image.factory');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';
    $form['#attached']['library'][] = 'qs_site/unload';
    return $form;
  }

}
