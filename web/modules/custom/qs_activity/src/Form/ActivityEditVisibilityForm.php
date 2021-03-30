<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;

/**
 * Activity form to update visibilities rules.
 */
class ActivityEditVisibilityForm extends ActivityEditFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?NodeInterface $activity = NULL) {
    $form = parent::buildForm($form, $form_state, $activity);

    $form['#theme_wrappers'] = [
      'form__modal',
    ];
    $form['#attributes'] = [
      'title' => $activity->title->value,
      'description' => $this->t('qs.activity.edit_visibility'),
      'theme' => 'primary',
    ];

    $form['#floating_buttons'][] = [
      'label' => $this->t('qs.activity.edit_visibility'),
      'icon' => 'activities',
      'active' => TRUE,
    ];

    $form['step-1'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => [
          'mb-5',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
      ],
    ];

    $form['step-1']['community_can_subscribe'] = [
      '#title' => $this->t('qs_activity.add_form.community_can_subscribe'),
      '#description' => $this->t('qs_activity.add_form.community_can_subscribe.description'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $activity->field_community_can_subscribe->value,
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $form['step-1']['community_access_contact'] = [
      '#title' => $this->t('qs_activity.add_form.community_access_contact'),
      '#description' => $this->t('qs_activity.add_form.community_access_contact.description'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $activity->field_community_access_contact->value,
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $form['step-1']['community_access_detail'] = [
      '#title' => $this->t('qs_activity.add_form.community_access_detail'),
      '#description' => $this->t('qs_activity.add_form.community_access_detail.description'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $activity->field_community_access_detail->value,
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $form['step-1']['community_access_story'] = [
      '#title' => $this->t('qs_activity.add_form.community_access_story'),
      '#description' => $this->t('qs_activity.add_form.community_access_story.description'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $activity->field_community_access_story->value,
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $form['step-1']['member_create_story'] = [
      '#title' => $this->t('qs_activity.add_form.member_create_story'),
      '#description' => $this->t('qs_activity.add_form.member_create_story.description'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $activity->field_member_create_story->value,
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $form['step-1']['community_access_gallery'] = [
      '#title' => $this->t('qs_activity.add_form.community_access_gallery'),
      '#description' => $this->t('qs_activity.add_form.community_access_gallery.description'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $activity->field_community_access_gallery->value,
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $form['step-1']['member_create_gallery'] = [
      '#title' => $this->t('qs_activity.add_form.member_create_gallery'),
      '#description' => $this->t('qs_activity.add_form.member_create_gallery.description'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $activity->field_member_create_gallery->value,
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
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
  public function getFormId() {
    return 'qs_activity_edit_visibility_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->get('activity'));

    // Format authorizations for creations.
    $fields = [
      'field_community_can_subscribe' => (bool) $form_state->getValue('community_can_subscribe'),
      'field_community_access_contact' => (bool) $form_state->getValue('community_access_contact'),
      'field_community_access_detail' => (bool) $form_state->getValue('community_access_detail'),
      'field_community_access_story' => (bool) $form_state->getValue('community_access_story'),
      'field_member_create_story' => (bool) $form_state->getValue('member_create_story'),
      'field_community_access_gallery' => (bool) $form_state->getValue('community_access_gallery'),
      'field_member_create_gallery' => (bool) $form_state->getValue('member_create_gallery'),
    ];

    // Create the new activity.
    $activity = $this->activityManager->update($activity, $fields);

    drupal_set_message($this->t('qs_activity.edit_visibility_form.success @activity', [
      '@activity' => $activity->getTitle(),
    ]));

    $form_state->setRedirect('qs_activity.activities.dashboard', ['activity' => $activity->id()], []);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

}
