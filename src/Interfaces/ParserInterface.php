<?php

namespace Render\Interfaces;

interface ParserInterface
{
    /**
     * Parser the content and return him after build.
     *
     * @param string $content
     * @param array $data
     * @return string
     */
    public function parse(string $content, array $data = []): string;
}