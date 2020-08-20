<?php

declare(strict_types=1);

namespace Render;

use Render\Exceptions\NotSupportTagException;
use Render\Interfaces\ManagerTagsInterface;

/**
 * Class ManagerTags
 * @package Render
 */
class ManagerTags implements ManagerTagsInterface
{
    /**
     * Map tags.
     * @var array
     */
    protected $mapData = [];
    protected $mapCallable = [];

    /**
     * @inheritDoc
     */
    public function getMapData(): array
    {
        return $this->mapData;
    }

    /**
     * @inheritDoc
     */
    public function addTag(string $htmlTag, string $data, ?callable $handle = null): void
    {
        $this->mapData[$htmlTag] = $data;
        $this->mapCallable[$htmlTag] = $handle;
    }

    /**
     * @inheritDoc
     */
    public function removeTag(string $htmlTag): void
    {
        if (!$this->isFree($htmlTag))
            unset($this->mapData[$htmlTag]);

    }

    /**
     * @inheritDoc
     */
    public function isFree(string $htmlTag): bool
    {
        return !array_key_exists($htmlTag, $this->mapData);
    }

    /**
     * @inheritDoc
     */
    public function getData(string $htmlTag): string
    {
        if ($this->isFree($htmlTag)) {
            throw new NotSupportTagException($htmlTag);
        }
        return $this->mapData[$htmlTag];
    }

    /**
     * @inheritDoc
     */
    public function tryGetCallable(string $htmlTag): ?callable
    {
        return array_key_exists($htmlTag, $this->mapCallable) ? $this->mapCallable[$htmlTag] : null;
    }
}