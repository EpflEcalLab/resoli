<?php

class TwigTranslationExtractorExtension extends \Twig_Extension
{
    protected $translationNodeVisitor;

    public function __construct()
    {
        $this->translationNodeVisitor = new TranslationNodeVisitor();
    }

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @deprecated since 1.23 (to be removed in 2.0), implement Twig_Extension_InitRuntimeInterface instead
     */
    public function initRuntime(Twig_Environment $environment)
    {
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     *
     * @deprecated since 1.23 (to be removed in 2.0), implement Twig_Extension_GlobalsInterface instead
     */
    public function getGlobals()
    {
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     *
     * @deprecated since 1.26 (to be removed in 2.0), not used anymore internally
     */
    public function getName()
    {
        return 'drupal_trans_extractor';
    }

    public function getNodeVisitors()
    {
        return [$this->translationNodeVisitor];
    }

    public function getTranslationNodeVisitor()
    {
        return $this->translationNodeVisitor;
    }
}
