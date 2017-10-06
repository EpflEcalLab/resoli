<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ActivityInlineAddMemberForm class.
 */
class ActivityInlineAddMemberForm extends ActivityEditFormBase {

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
      'description' => $this->t('qs.activity.add_member'),
    ];

    $query = $this->privilegeManager->queryMembersWithPrivileges($community);
    $rows = $query->execute()->fetchAll();
    foreach ($rows as $row) {
      $uids[] = $row->user;
    }
    // Load user entities whitout privileges.
    $community_members = $this->userStorage->loadMultiple($uids);

    $options = [];
    $fallback = [];
    if (!empty($community_members)) {
      foreach ($community_members as $community_member) {
        $options[$community_member->id()] = [
          'uid'         => $community_member->id(),
          'email'       => $community_member->mail->value,
          'displayname' => $community_member->field_firstname->value . ' ' . $community_member->field_lastname->value,
        ];
        $fallback[$community_member->id()] = !empty($options[$community_member->id()]['displayname']) ? $options[$community_member->id()]['displayname'] : $community_member->name->value;
      }
    }

    $form['member'] = [
      '#title'         => $this->t('Member'),
      '#type'          => 'select',
      '#multiple'      => FALSE,
      '#required'      => FALSE,
      '#options'       => $fallback,
      '#attributes'    => [
        'selectize'    => TRUE,
        'class'        => ['selectize-members'],
        'data-options' => json_encode($options),
      ],
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#attributes' => [
        'icon' => 'check',
        'modal' => TRUE,
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
        ],
      ],
      '#value' => $this->t('qs.form.submit'),
    ];

    return $form;
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

    $form_state->setRedirect('qs_activity.activities.members', ['activity' => $activity->id()], []);
  }

}
