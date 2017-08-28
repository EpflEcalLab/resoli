<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;

/**
 * ActivityDeleteForm class.
 */
class ActivityDeleteForm extends ActivityEditFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $activity = NULL) {

    $form = parent::buildForm($form, $form_state, $activity);
    $form['warning']['#markup'] = '<p>' . $this->t('qs_activity.activities.form.delete.warning') . '</p>';

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('qs.form.submit'),
    ];

    $cancel_link = $this->urlGenerator->generateFromRoute('qs_activity.activities.form.edit', ['activity' => $activity->id()]);
    $form['actions']['cancel'] = [
      '#markup' => '<a href="' . $cancel_link . '">' . $this->t('qs_activity.form.cancel') . '</a>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->getValue('activity'));

    // Assert the activity has no event.
    $events = $this->eventManager->getAll($activity);
    if (!empty($events)) {
      $form_state->setError($form, $this->t("qs_activity.activities.form.delete.error.has_events @activity", ['@activity' => $activity->getTitle()]));
    }

    // Add inline errors.
    $this->applyErrorsInline($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->getValue('activity'));
    $community = $activity->field_community->entity;

    drupal_set_message($this->t("qs_activity.activities.form.delete.success @activity", [
      '@activity' => $activity->getTitle(),
    ]));

    $form_state->setRedirect('qs_activity.collection.themes', ['community' => $community->id()], []);

    // Delete the activity.
    $activity = $activity->delete();
  }

}
