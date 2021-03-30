<?php

namespace Drupal\qs_site\TwigExtension;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Provides some renderer as Twig Extensions.
 */
class Render extends \Twig_Extension {
  use ContainerAwareTrait;

  /**
   * List of all Twig functions.
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('qs_site_render_form', [$this, 'renderForm'], ['is_safe' => ['html']]),
    ];
  }

  /**
   * Unique identifier for this Twig extension.
   */
  public function getName() {
    return 'qs_site.twig.render';
  }

  /**
   * Load a given form with or whitout parameters.
   *
   * @param string $module
   *   The module name where the form below.
   * @param string $form
   *   The form class name.
   * @param array $params
   *   (optional) An array of parameters passed to the form.
   * @param string $form_id
   *   (optional) The form unique ID.
   *
   * @return array|null
   *   A render array for the form or NULL if the form does not exist.
   */
  public function renderForm($module, $form, array $params = [], $form_id = NULL) {
    $class = 'Drupal\\' . $module . '\\Form\\' . $form;
    $form = new $class($form_id, $this->container);

    return $this->getFormBuilder()->getForm($form, $params);
  }

  /**
   * Provides an interface for form building and processing.
   *
   * @return \Drupal\Core\Form\FormBuilderInterface
   *   Return the interface for form building and processing.
   */
  protected function getFormBuilder() {
    return $this->container->get('form_builder');
  }

}
