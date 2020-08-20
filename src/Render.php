<?php

declare(strict_types=1);

namespace Render;

use Render\Exceptions\NotFoundFileException;
use Render\Exceptions\NotSupportTagException;
use Render\Interfaces\RenderInterface;
use SimpleXMLElement;

/**
 * Class Render
 * @package Render
 * @version 1.0
 */
class Render implements RenderInterface
{
    /**
     * Paths to assets.
     * @var array
     */
    protected $paths;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /** @var Parser */
    protected $parser;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var ManagerTags
     */
    public $managerTags;

    /**
     * @inheritDoc
     */
    public function __construct($paths)
    {
        $this->paths = is_string($paths) ? [$paths] : $paths;
        $this->managerTags = new ManagerTags();
        $this->parser = new Parser($this);
    }

    /**
     * @inheritDoc
     */
    public function addPath($path): void
    {
        array_push($this->paths, $path);
    }

    /**
     * @inheritDoc
     */
    public function loadTemplate(string $xml): void
    {
        $this->xml = new SimpleXMLElement($xml);
    }

    /**
     * @inheritDoc
     */
    public function render(array $data = []): string
    {
        $this->data = $data;
        $rootNode = $this->xml;
        $result = trim($rootNode->__toString());
        foreach ($rootNode->children() as $child)
            $result .= $this->recursiveRenderNode($child);
        return $this->injectData($result);
    }

    /**
     * @param SimpleXMLElement $element
     * @return string
     * @throws NotFoundFileException
     * @throws NotSupportTagException
     */
    protected function recursiveRenderNode(SimpleXMLElement $element): string
    {
        $elementName = $element->getName();
        $viewPath = $this->managerTags->getData($elementName);
        $callable = $this->managerTags->tryGetCallable($elementName);
        $content = trim($element->__toString());
        $htmlView = null;

        // search template
        foreach ($this->paths as $path) {
            $path = realpath("$path/$viewPath");
            if ($path && file_exists($path)) {
                $htmlView = file_get_contents($path);
            } else {
                throw new NotFoundFileException("$path/$viewPath");
            }
        }

        foreach ($element->children() as $child) {
            $content .= $this->recursiveRenderNode($child);
        }

        if (is_callable($callable)) {
            $xmler = new Xmler();
            $callable(current($element->attributes()), $xmler);
            return str_replace([
                '{{ slot }}',
                '{{ slot_classes }}',
                '{{ slot_styles }}',
            ], [
                $content,
                implode(',', $xmler->getClasses()),
                implode(',', $xmler->getStyles())
            ], $htmlView);
        } else {
            return str_replace('{{ slot }}', $content, $htmlView);
        }
    }

    public function getLayout($filename): ?string
    {
        foreach ($this->paths as $path) {
            $path = realpath("$path/layouts/$filename");
            if ($path && file_exists($path)) {
                return file_get_contents($path);
            }
        }
        return null;
    }

    public function getInclude($filename): ?string
    {
        foreach ($this->paths as $path) {
            $path = realpath("$path/includes/$filename");
            if ($path && file_exists($path)) {
                return file_get_contents($path);
            }
        }
        return null;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function injectData(string $content): string
    {
        return $this->parser->parse($content, $this->data);
    }
}