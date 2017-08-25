<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_site\Form\InlineErrorFormTrait;

/**
 * ActivityAddForm class.
 */
class ActivityAddForm extends FormBase {
  use InlineErrorFormTrait;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, ActivityManager $activity_manager) {
    $this->acl             = $acl;
    $this->termStorage     = $entity_type_manager->getStorage('taxonomy_term');
    $this->activityManager = $activity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
    $container->get('qs_acl.access_control'),
    $container->get('entity_type.manager'),
    $container->get('qs_activity.activity_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_add_form';
  }

  /**
   * Checks access for creating file in the given rubric.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();
    if ($this->acl->hasWriteAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, TermInterface $community = NULL) {
    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';

    // Save the community for submisson.
    $form['community'] = [
      '#type'  => 'hidden',
      '#value' => $community->id(),
    ];

    $form['activity']['step-1'] = [
      '#type' => 'fieldset',
    ];

    $form['activity']['step-1']['title'] = [
      '#attributes'  => ['required' => TRUE],
      '#title'       => $this->t('qs_activity.add_form.title'),
      '#placeholder' => $this->t('qs_activity.add_form.title.placeholder'),
      '#type'        => 'textfield',
      '#required'    => FALSE,
    ];

    $form['activity']['step-2'] = [
      '#type'  => 'fieldset',
    ];

    // Get all themes for options.
    $themes = $this->termStorage->loadTree('themes', 0, NULL, TRUE);
    $options = [];
    foreach ($themes as $theme) {
      $options[$theme->id()] = $theme->getName();
    }
    $form['activity']['step-2']['theme'] = [
      '#attributes' => [
        'required' => TRUE,
        'title'    => $this->t('qs_activity.add_form.theme'),
      ],
      '#type'     => 'radios',
      '#required' => FALSE,
      '#options'  => $options,
    ];

    $form['activity']['step-3'] = [
      '#type'  => 'fieldset',
    ];

    $form['activity']['step-3']['community_can_subscribe'] = [
      '#title'         => $this->t('qs_activity.add_form.community_can_subscribe'),
      '#description'   => $this->t('qs_activity.add_form.community_can_subscribe.description'),
      '#type'          => 'checkbox',
      '#required'      => FALSE,
      '#default_value' => 0,
    ];

    $form['activity']['step-3']['community_access_contact'] = [
      '#title'         => $this->t('qs_activity.add_form.community_access_contact'),
      '#description'   => $this->t('qs_activity.add_form.community_access_contact.description'),
      '#type'          => 'checkbox',
      '#required'      => FALSE,
      '#default_value' => 1,
    ];

    $form['activity']['step-3']['community_access_detail'] = [
      '#title'         => $this->t('qs_activity.add_form.community_access_detail'),
      '#description'   => $this->t('qs_activity.add_form.community_access_detail.description'),
      '#type'          => 'checkbox',
      '#required'      => FALSE,
      '#default_value' => 1,
    ];

    $form['activity']['step-3']['community_access_story'] = [
      '#title'         => $this->t('qs_activity.add_form.community_access_story'),
      '#description'   => $this->t('qs_activity.add_form.community_access_story.description'),
      '#type'          => 'checkbox',
      '#required'      => FALSE,
      '#default_value' => 0,
    ];

    $form['activity']['step-3']['member_create_story'] = [
      '#title'         => $this->t('qs_activity.add_form.member_create_story'),
      '#description'   => $this->t('qs_activity.add_form.member_create_story.description'),
      '#type'          => 'checkbox',
      '#required'      => FALSE,
      '#default_value' => 1,
    ];

    $form['activity']['step-3']['community_access_gallery'] = [
      '#title'         => $this->t('qs_activity.add_form.community_access_gallery'),
      '#description'   => $this->t('qs_activity.add_form.community_access_gallery.description'),
      '#type'          => 'checkbox',
      '#required'      => FALSE,
      '#default_value' => 0,
    ];

    $form['activity']['step-3']['member_create_gallery'] = [
      '#title'       => $this->t('qs_activity.add_form.member_create_gallery'),
      '#description' => $this->t('qs_activity.add_form.member_create_gallery.description'),
      '#type'        => 'checkbox',
      '#required'    => FALSE,
      '#default_value'     => 1,
    ];

    $form['activity']['step-4'] = [
      '#type'  => 'fieldset',
    ];

    $form['activity']['step-4']['event'] = [
      '#attributes' => [
        'required' => TRUE,
        'title'    => $this->t('qs_activity.add_form.event'),
      ],
      '#type'       => 'radios',
      '#required'   => FALSE,
      '#options'    => [0 => $this->t('qs_activity.add_form.event.yes'), 1 => $this->t('qs_activity.add_form.event.no')],
    ];

    $form['activity']['step-4']['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('qs_activity.add_form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the title is valid.
    if (!$form_state->getValue('title') || empty($form_state->getValue('title'))) {
      $form_state->setErrorByName('[activity][step-1][title]', $this->t('qs_activity.form.error.empty @fieldname', ['@fieldname' => $form['activity']['step-1']['title']['#title']]));
    }

    // Assert the theme is valid.
    if (!$form_state->getValue('theme') || empty($form_state->getValue('theme'))) {
      $form_state->setErrorByName('[activity][step-2][theme]', $this->t('qs_activity.form.error.empty @fieldname', ['@fieldname' => $form['activity']['step-2']['theme']['#attributes']['title']]));
    }

    // Add inline errors.
    $this->applyErrorsInline($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $community = $this->termStorage->load($form_state->getValue('community'));

    // Format authorizations for creations.
    $authorizations = [
      'field_community_can_subscribe'  => (bool) $form_state->getValue('community_can_subscribe'),
      'field_community_access_contact' => (bool) $form_state->getValue('community_access_contact'),
      'field_community_access_detail'  => (bool) $form_state->getValue('community_access_detail'),
      'field_community_access_story'   => (bool) $form_state->getValue('community_access_story'),
      'field_member_create_story'      => (bool) $form_state->getValue('member_create_story'),
      'field_community_access_gallery' => (bool) $form_state->getValue('community_access_gallery'),
      'field_member_create_gallery'    => (bool) $form_state->getValue('member_create_gallery'),
    ];

    // Format themes for creations.
    // Use an array now for futur proof (multiple themes).
    $themes = [$form_state->getValue('theme')];

    // Create the new activity.
    $activity = $this->activityManager->create($form_state->getValue('title'), $themes, $authorizations, $community);

    drupal_set_message($this->t("qs_activity.add_form.success @activity", [
      '@activity' => $activity->getTitle(),
    ]));
    $form_state->setRedirect('qs_activity.collection.themes', ['community' => $community->id()], []);
  }

}
