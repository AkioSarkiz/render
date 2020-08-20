<?php

namespace Render;

/**
 * Class Xmler - helper class
 * @package Render
 */
class Xmler
{
    /** @var array Storage classes */
    protected $classes = [];
    /** @var array Storage styles */
    protected $styles = [];

    /**
     * Add new class.
     *
     * @param string $class
     * @return void
     */
    public function addClass(string $class): void
    {
        array_push($this->classes, $class);
    }

    /**
     * Add new style.
     *
     * @param string $style
     * @return void
     */
    public function addStyle(string $style): void
    {
        array_push($this->styles, $style);
    }

    /**
     * Get all styles.
     *
     * @return array
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * Get all classes.
     *
     * @return array
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}