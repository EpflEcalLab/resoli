<?php

namespace Drupal\qs_site\TwigExtension;

use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides some renderer as Twig Extensions.
 */
class Render extends AbstractExtension {

  /**
   * The service container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Constructs a Render Twig Extension.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   *   The service container.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   *   The form builder.
   */
  public function __construct(ContainerInterface $container, FormBuilderInterface $form_builder) {
    $this->container = $container;
    $this->formBuilder = $form_builder;
  }

  /**
   * List of all Twig functions.
   */
  public function getFunctions() {
    return [
      new TwigFunction('qs_site_render_form', [$this, 'renderForm'], ['is_safe' => ['html']]),
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
    $class = "Drupal\\{$module}\\Form\\{$form}";
    $form = new $class($form_id, $this->container);

    return $this->formBuilder->getForm($form, $params);
  }

}
