<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form handler for sharing CRUD forms.
 *
 * @internal
 */
abstract class FormBasic extends FormBase {
  /**
   * Holds the container instance.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
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
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';

    return $form;
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
   * Lazy loading for the Drupal current user account proxy.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   Return The current user account proxy.
   */
  protected function getCurrentUser() {
    return $this->container->get('current_user');
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
   * Lazy loading for the Drupal entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   Return the Drupal entity type manager.
   */
  protected function getEntityTypeManager() {
    return $this->container->get('entity_type.manager');
  }

  /**
   * Lazy loading for The language Manager service.
   *
   * @return \Drupal\Core\Language\LanguageManagerInterface
   *   Return The language manager.
   */
  protected function getLanguageManager() {
    return $this->container->get('language_manager');
  }

  /**
   * Lazy loading for Mail service.
   *
   * @return \Drupal\Core\Mail\MailManagerInterface
   *   Return the Mal service.
   */
  protected function getMail() {
    return $this->container->get('plugin.manager.mail');
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
   * Lazy loading for the Quartiers-Solidaires Privilege Manager service.
   *
   * @return \Drupal\qs_acl\Service\PrivilegeManager
   *   Return the Quartiers-Solidaires Privilege Manager.
   */
  protected function getPrivilegeManager() {
    return $this->container->get('qs_acl.privilege_manager');
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
   * Lazy loading for the Drupal URL Generator service.
   *
   * @return \Drupal\Core\Routing\UrlGeneratorInterface
   *   Return the Drupal URL Generator.
   */
  protected function getUrlGenerator() {
    return $this->container->get('url_generator');
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
}
