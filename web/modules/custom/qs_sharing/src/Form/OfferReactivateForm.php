<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Form to reactivate an offer.
 */
class OfferReactivateForm extends OfferActionFormBase {

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
      'class' => ['offer', 'offer' . $offer->id(), 'reactivate'],
    ];
    $form['action']['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $this->t('qs_sharing.user.offers.collection.reactivate'),
      '#attributes' => [
        'icon' => 'clockwise',
        'icon_left' => TRUE,
        'class' => [
          'btn',
          'btn-outline-invert',
          'btn-icon',
          'shadow-to-bottom',
          'btn-block',
        ],
        'data-confirm' => $this->t('qs_sharing.user.offers.collection.reactivate.confirmed'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_offer_reactivate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $offer = $this->nodeStorage->load($form_state->get('offer'));
    $community = $offer->field_offer_type->entity->field_community->entity;

    // Reactivate the offer.
    $offer->set('moderation_state', 'published');
    $offer->save();

    $this->messenger()->addMessage($this->t('qs_sharing.offers.form.reactivate.success @offer', [
      '@offer' => $offer->getTitle(),
    ]));

    $form_state->setRedirect('qs_sharing.collection.user.offers', [
      'community' => $community->id(),
      'user' => $this->currentUser()->id(),
    ], []);
  }

}
