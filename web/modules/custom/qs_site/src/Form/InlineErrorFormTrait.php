<?php

namespace Drupal\qs_site\Form;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides helper methods for inlining error form.
 */
trait InlineErrorFormTrait {

  /**
   * Apply all errors as inline field error.
   *
   * @param array $form
   *   The current form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public static function applyErrorsInline(array &$form, FormStateInterface $form_state) {
    // If validation errors, add inline errors.
    if ($errors = $form_state->getErrors()) {
      // Add error to fields using Symfony Accessor.
      $accessor = PropertyAccess::createPropertyAccessor();
      foreach ($errors as $field_accessor => $error) {
        try {
          $accessor->getValue($form, $field_accessor);
          if ($field = $accessor->getValue($form, $field_accessor)) {

            if (isset($field['#prefix'])) {
              $prefix = str_replace('form-group', 'form-group has-danger', $field['#prefix']);
              $accessor->setValue($form, $field_accessor . '[#prefix]', $prefix);
            }

            if (isset($field['#suffix'])) {
              $suffix = '<div class="form-control-feedback" id="' . $field['#id'] . '-error">' . $error . '</div>' . $field['#suffix'];
              $accessor->setValue($form, $field_accessor . '[#suffix]', $suffix);
            }

            $accessor->setValue($form, $field_accessor . '[#attributes][aria-invalid]', 'true');
            $accessor->setValue($form, $field_accessor . '[#attributes][aria-describedby]', $field['#id'] . '-error');
          }
        }
        catch (\Exception $e) {

        }
      }
    }
  }

}
