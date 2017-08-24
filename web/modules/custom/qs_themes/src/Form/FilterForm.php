<?php

namespace Drupal\qs_themes\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * FilterForm.
 */
class FilterForm extends FormBase {
  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Request stack that controls the lifecycle of requests.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_themes_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(AccountProxyInterface $currentUser, RequestStack $request_stack, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $currentUser;
    $this->requestStack = $request_stack;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
        $container->get('current_user'),
        $container->get('request_stack'),
        $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#method'] = 'GET';

    // The request should be took at the latest moment, avoid it on constructor.
    $master_request = $this->requestStack->getMasterRequest();

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';

    // Get all themes for options.
    $themes = $this->termStorage->loadTree('themes', 0, NULL, TRUE);
    $options = [];
    foreach ($themes as $community) {
      $options[$community->tid->value] = $community->name->value;
    }

    // Get all selected themes for options.
    $filtred_themes = $master_request->query->get('themes');
    $form['themes'] = [
      '#type'     => 'checkboxes',
      '#required' => FALSE,
      '#options'  => $options,
      '#default'  => $filtred_themes,
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('qs_themes.filter_form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
