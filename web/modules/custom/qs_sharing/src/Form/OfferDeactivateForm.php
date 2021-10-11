<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Form to deactivate an offer.
 */
class OfferDeactivateForm extends OfferActionFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
    if (!isset($options['offer'])) {
      return $form;
    }
    /** @var \Drupal\node\NodeInterface $offer */
    $offer = $options['offer'];

    $form = parent::buildForm($form, $form_state, $offer);

    // Disable caching.
    $form['#cache']['max-age'] = 0;

    $form['#attributes'] = [
      'data-ajax' => 'true',
      'data-parent' => 'card' . $offer->id(),
      'class' => ['offer', 'offer' . $offer->id(), 'deactivate'],
    ];
    $form['action']['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $this->t('qs_sharing.user.offers.collection.deactivate'),
      '#attributes' => [
        'icon' => 'cross',
        'icon_left' => TRUE,
        'class' => [
          'btn',
          'btn-outline-invert',
          'btn-icon',
          'shadow-to-bottom',
          'btn-block',
        ],
        'data-confirm' => $this->t('qs_sharing.user.offers.collection.deactivate.confirmed'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_offer_deactivate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $offer = $this->nodeStorage->load($form_state->get('offer'));
    $community = $offer->field_offer_type->entity->field_community->entity;

    // Deactivate the offer.
    $this->offerManager->deactivate($offer);

    $this->messenger()->addMessage($this->t('qs_sharing.offers.form.deactivate.success @offer', [
      '@offer' => $offer->getTitle(),
    ]));

    $form_state->setRedirect('qs_sharing.collection.user.offers', [
      'community' => $community->id(),
      'user' => $this->currentUser()->id(),
    ], ['fragment' => 'card' . $offer->id()]);
  }

}
