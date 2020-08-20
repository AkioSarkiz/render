<?php

declare(strict_types=1);

namespace Render\Interfaces;

use Render\Exceptions\NotSupportTagException;

/**
 * Interface ManagerTags
 * @package Render\Interfaces
 */
interface ManagerTagsInterface
{
    /**
     * Map tags for parse.
     *
     * @return array
     */
    public function getMapData(): array;

    /**
     * Add tag to manager.
     *
     * @param string $htmlTag
     * @param string $data
     * @param callable|null $handle
     */
    public function addTag(string $htmlTag, string $data, ?callable $handle = null): void;

    /**
     * Check free tag html.
     *
     * @param string $htmlTag
     * @return bool
     */
    public function isFree(string $htmlTag): bool;

    /**
     * Remove tag html.
     *
     * @param string $htmlTag
     * @return void
     */
    public function removeTag(string $htmlTag): void;

    /**
     * Get callable of tag.
     *
     * @param string $htmlTag
     * @return string
     * @throws NotSupportTagException
     */
    public function getData(string $htmlTag): string;

    /**
     * Try get callable.
     *
     * @param string $htmlTag
     * @return callable|null
     */
    public function tryGetCallable(string $htmlTag): ?callable;
}