<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Form to delete an offer.
 */
class OfferDeleteForm extends OfferActionFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
    if (!isset($options['offer'])) {
      return $form;
    }
    $offer = $options['offer'];

    $form = parent::buildForm($form, $form_state, $offer);

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;

    $form['#attributes'] = [
      'data-ajax' => 'true',
      'data-parent' => 'card' . $offer->id(),
      'class' => ['offer'],
    ];
    $form['action']['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $this->t('qs_sharing.user.offers.collection.delete'),
      '#attributes' => [
        'icon' => 'trash',
        'icon_left' => TRUE,
        'class' => [
          'btn',
          'btn-outline-invert',
          'btn-icon',
          'shadow-to-bottom',
          'btn-block',
          'bg-danger',
        ],
        'data-confirm' => $this->t('qs_sharing.user.offers.collection.delete.confirmed'),
      ],
    ];

    // Remove unload script.
    $form['#attached']['library'] = [];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_offer_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $offer = $this->nodeStorage->load($form_state->get('offer'));
    $community = $offer->field_offer_type->entity->field_community->entity;

    // Delete the offer.
    $offer->delete();

    $this->messenger()->addMessage($this->t('qs_sharing.offers.form.delete.success @offer', [
      '@offer' => $offer->getTitle(),
    ]));

    $form_state->setRedirect('qs_sharing.collection.user.offers', [
      'community' => $community->id(),
      'user' => $this->currentUser->id(),
    ], []);
  }

}
