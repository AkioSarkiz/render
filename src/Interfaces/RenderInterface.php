<?php

declare(strict_types=1);

namespace Render\Interfaces;

use Render\Exceptions\NotFoundFileException;
use Render\Exceptions\NotSupportTagException;

/**
 * Interface RenderInterface
 * @package Render\Interfaces
 */
interface RenderInterface
{
    /**
     * Render constructor.
     * @param string|array $paths
     */
    public function __construct($paths);

    /**
     * Load template data
     *
     * @param string $xml template format xml
     * @return void
     */
    public function loadTemplate(string $xml): void;

    /**
     * Render template xml to html.
     *
     * @param array $data
     * @return string raw html
     * @throws NotFoundFileException
     * @throws NotSupportTagException
     */
    public function render(array $data = []): string;

    /**
     * Add custom path.
     *
     * @param $path
     */
    public function addPath($path): void;

    /**
     * Get content layout if he exists.
     *
     * @param $filename
     * @return string|null
     */
    public function getLayout($filename): ?string;
}