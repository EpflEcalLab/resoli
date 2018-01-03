<?php

namespace Drupal\qs_subscription\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\qs_subscription\Service\SubscriptionManager;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * When using this form, be are of the following:.
 *
 * This form is rendered more than once in the same page & it use Ajax.
 * Drupal has an issue & can't handle multiple same form with Ajax
 * in the same page.
 *
 * To avoid this issue, we have to instantiate the form object yourself
 * and pass it to the form builder. That can be a bit tricky as `getFormId()`
 * is called very early.
 *
 * Doing this way, you can't use the injection of control.
 *
 * You have to use our custom twig renderer `qs_site_render_form`.
 *
 * https://drupal.stackexchange.com/a/223273/63886
 */
class RequestForm extends FormBase {
  protected $uniqueId;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  private $subscriptionManager;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * Composes and optionally sends an email message.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mail;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return $this->uniqueId;
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($unique_id, ContainerInterface $container) {
    $this->uniqueId = $unique_id;

    /* @var \Drupal\qs_acl\Service\AccessControl */
    $this->acl = $container->get('qs_acl.access_control');
    /* @var \Drupal\node\NodeStorageInterface */
    $this->nodeStorage = $container->get('entity_type.manager')->getStorage('node');
    /* @var \Drupal\qs_subscription\Service\SubscriptionManager */
    $this->subscriptionManager = $container->get('qs_subscription.subscription_manager');
    /* @var \Drupal\Core\Mail\MailManagerInterface */
    $this->mail = $container->get('plugin.manager.mail');
    /* @var \Drupal\Core\Render\RendererInterface */
    $this->renderer = $container->get('renderer');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $params = NULL) {
    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';

    // A hidden field can't be altered, Drupal assert it.
    $form['event'] = [
      '#type'  => 'hidden',
      '#value' => $params['event'],
    ];

    $form['submit'] = [
      '#name' => 'request_subscription',
      '#type'  => 'submit',
      '#attributes' => [
        'data-status-show' => 'default',
        'icon' => 'register',
        'icon_left' => TRUE,
        'theme' => 'secondary',
        'class' => [
          'shadow-to-right',
          'btn-outline-secondary',
          'btn-block',
          'btn-white',
        ],
      ],
      '#ajax'        => [
        'callback' => [$this, 'respondToAjax'],
        'event'    => 'click',
        'progress' => [],
      ],
      '#value' => $this->t('qs.event.register'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $event_id = $form_state->getValue('event');
    $event = $this->nodeStorage->load($event_id);

    // Get the related activity.
    $activity = $event->field_activity->entity;

    if (!$event || !$activity) {
      $form_state->setErrorByName('', $this->t('qs.form.error.something_went_wrong'));
    }
    elseif (!$this->acl->hasSubscribeAccessEvent($activity)) {
      $form_state->setErrorByName('', $this->t('qs.form.error.something_went_wrong'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Handle redirection.
    $trigger = $form_state->getTriggeringElement();

    $event_id = $form_state->getValue('event');
    $event = $this->nodeStorage->load($event_id);

    if ($trigger['#name'] === 'request_subscription') {
      $event_id = $form_state->getValue('event');
      $event = $this->nodeStorage->load($event_id);

      drupal_set_message($this->t('qs_subscription.request.form.success @event', [
        '@event' => $event->getTitle(),
      ]));

      $this->subscriptionManager->request($event);
    }
  }

  /**
   * Ajax callback. which triggers commands. The form still works whitout JS.
   *
   * @param array $form
   *   Form API array structure.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   Form state information.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Response object.
   */
  public function respondToAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    // Process the submit only when no errors, but keep runing for the messages.
    if (!$form_state->hasAnyErrors()) {
      $this->submitForm($form, $form_state);

      $event_id = $form_state->getValue('event');
      $response->addCommand(new InvokeCommand('#card-event' . $event_id, 'attr', ['data-status', 'pending']));
    }

    // Create the bag message render array.
    $status_messages = ['#type' => 'status_messages'];
    $messages = $this->renderer->renderRoot($status_messages);
    if (!empty($messages)) {
      // Append the bag message(s).
    }

    // TODO InvokeCommand scrollTo the alerts.
    if ($form_state->hasAnyErrors()) {
    }

    return $response;
  }

}
