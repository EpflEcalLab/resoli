<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ActivityInlineAddMemberForm class.
 */
class ActivityInlineAddMemberForm extends ActivityEditFormBase {

  /**
   * Fallback select options of members.
   *
   * @var array
   */
  protected $fallback;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->currentUser      = $this->getCurrentUser();
    $this->userStorage      = $this->getUserStorage();
    $this->privilegeManager = $this->getPrivilegeManager();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_add_member_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
    if (!isset($options['activity'])) {
      return $form;
    }
    $form = parent::buildForm($form, $form_state, $options['activity']);
    $community = $options['activity']->field_community->entity;

    $form['#attributes'] = [
      'title'       => $options['activity']->title->value,
    ];

    $query = $this->privilegeManager->queryMembersWithPrivileges($community);
    $rows = $query->execute()->fetchAll();
    foreach ($rows as $row) {
      $uids[] = $row->user;
    }
    // Load user entities without privileges.
    $community_members = $this->userStorage->loadMultiple($uids);
    $select_options = [];
    $this->fallback = [];
    if (!empty($community_members)) {
      foreach ($community_members as $community_member) {

        // Remove users showed on the page and passed in options to avoid
        // confusion.
        if (isset($options['members'][$community_member->id()])) {
          continue;
        }

        $select_options[] = [
          'uid'         => $community_member->id(),
          'email'       => $community_member->mail->value,
          'displayname' => $community_member->field_firstname->value . ' ' . $community_member->field_lastname->value,
        ];
        $this->fallback[$community_member->id()] = !empty($select_options[$community_member->id()]['displayname']) ? $select_options[$community_member->id()]['displayname'] : $community_member->name->value;
      }
    }

    $form['member'] = [
      '#title'         => $this->t('qs.activity.add_member'),
      '#type'          => 'select',
      '#multiple'      => FALSE,
      '#required'      => FALSE,
      '#options'       => $this->fallback,
      '#attributes'    => [
        'placeholder'   => $this->t('qs.activity.add_member.placeholder'),
        'selectize'    => TRUE,
        'class'        => ['selectize-members'],
        'data-options' => json_encode($select_options),
      ],
      '#validated' => TRUE,
      '#theme_wrappers' => [
        'form_element',
        'container__center',
      ],
    ];

    $form['actions'] = [
      '#type'  => 'fieldset',
      '#attributes' => [
        'class' => [
          'mb-5',
          'text-center',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
      ],
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#attributes' => [
        'icon' => 'plus',
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
          'mx-auto',
        ],
      ],
      '#value' => $this->t('qs.activity.add_member.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the member is valid.
    if (!$form_state->getValue('member') || empty($form_state->getValue('member'))) {
      $form_state->setErrorByName('member', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['member']['#title']]));
    }

    if (!isset($this->fallback[$form_state->getValue('member')])) {
      $form_state->setErrorByName('member', $this->t('qs.form.error.something_went_wrong'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->getValue('activity'));
    $account = $this->userStorage->load($form_state->getValue('member'));

    $this->privilegeManager->create('activity_members', $activity, $account);
    drupal_set_message($this->t("qs_activity.activities.form.add.member.success @activity", [
      '@activity' => $activity->getTitle(),
    ]));

    $form_state->setRedirect('qs_activity.activities.members', ['activity' => $activity->id()], ['fragment' => 'card' . $account->id()]);
  }

}
