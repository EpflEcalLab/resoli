<?php

namespace Drupal\qs_community\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * The members filters form used on community members controller.
 *
 * @see \Drupal\qs_community\Controller\MembersController
 */
class MembersFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#method'] = 'GET';

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';

    $form['keywords'] = [
      '#attributes' => [
        'required' => TRUE,
        'icon' => 'search',
      ],
      '#title' => $this->t('qs_community.members.filter.search.title'),
      '#placeholder' => $this->t('qs_community.members.filter.search.placeholder'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $this->getRequest()->get('keywords'),
      '#theme_wrappers' => [
        'form_element',
        'container__center',
      ],
    ];

    $form['actions'] = [
      '#type' => 'fieldset',
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
      '#type' => 'submit',
      '#attributes' => [
        'icon' => 'check',
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
          'mx-auto',
        ],
      ],
      '#value' => $this->t('qs_community.members.filter.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_community_members_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
