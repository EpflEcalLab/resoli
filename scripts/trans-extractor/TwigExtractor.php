<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Extractor\AbstractFileExtractor;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Source;

/**
 * TwigExtractor extracts translation messages from a twig template.
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwigExtractor
{
    /**
     * The twig environment.
     *
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($resource, MessageCatalogue $catalogue = null)
    {
        $translations = [];
        $files = $this->extractFiles($resource);
        foreach ($files as $file) {

            try {
                $trans = $this->extractTemplate(file_get_contents($file->getPathname()), $catalogue);
                $translations = array_merge($translations, $trans);
            } catch (Error $e) {
                if ($file instanceof \SplFileInfo) {
                    $path = $file->getRealPath() ?: $file->getPathname();
                    $name = $file instanceof SplFileInfo ? $file->getRelativePathname() : $path;
                    if (method_exists($e, 'setSourceContext')) {
                        $e->setSourceContext(new Source('', $name, $path));
                    } else {
                        $e->setTemplateName($name);
                    }
                }

                throw $e;
            }
        }

        return array_unique($translations);
    }

    /**
     * {@inheritdoc}
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    protected function extractTemplate($template)
    {
        $visitor = $this->twig->getExtension('drupal_trans_extractor')->getTranslationNodeVisitor();
        $visitor->enable();

        $this->twig->parse($this->twig->tokenize(new Source($template, '')));

        $translation = $visitor->getMessages();

        $visitor->disable();

        return $translation;
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    private function canBeExtracted($file)
    {
        return $this->isFile($file) && 'twig' === pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * @param string|array $directory
     *
     * @return array
     */
    private function extractFromDirectory($directory)
    {
        $finder = new Finder();

        return $finder->files()->name('*.twig')->in($directory);
    }


    /**
     * @param string|array $resource files, a file or a directory
     *
     * @return array
     */
    private function extractFiles($resource)
    {
        if (is_array($resource) || $resource instanceof \Traversable) {
            $files = array();
            foreach ($resource as $file) {
                if ($this->canBeExtracted($file)) {
                    $files[] = $this->toSplFileInfo($file);
                }
            }
        } elseif (is_file($resource)) {
            $files = $this->canBeExtracted($resource) ? array($this->toSplFileInfo($resource)) : array();
        } else {
            $files = $this->extractFromDirectory($resource);
        }

        return $files;
    }

    /**
     * @param string $file
     *
     * @return \SplFileInfo
     */
    private function toSplFileInfo($file)
    {
        return ($file instanceof \SplFileInfo) ? $file : new \SplFileInfo($file);
    }

    /**
     * @param string $file
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    private function isFile($file)
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException(sprintf('The "%s" file does not exist.', $file));
        }

        return true;
    }
}
