<?php

namespace Render;

use Render\Interfaces\ParserInterface;

class Parser implements ParserInterface
{
    /** @var array  */
    protected $data = [];

    protected $render;

    public function __construct(Render $render)
    {
        $this->render = $render;
    }

    public function parse(string $content, array $data = []): string
    {
        $this->data = $data;

        // replace variables
        preg_match_all('/{{\s*([0-9A-z_\-\'\"]+)\s*}}/', $content, $variables_matches);
        for ($i = 0; $i < count($variables_matches[0]); $i++) {
            $rawVar = $variables_matches[0][$i];
            $nameVar = $variables_matches[1][$i];
            $content = str_replace($rawVar, $this->simpleParseVar($nameVar), $content);
        }

        preg_match_all('/\s*@layout\s+([\.\w0-9_-]+)\s*/', $content, $layout_extends);
        if (count($layout_extends[1]) > 0) {
            $layout = $this->render->getLayout($layout_extends[1][0]);
            $content = str_replace(['{{ slot }}', $layout_extends[0][0]], [$content, ''], $layout);
        }

        preg_match_all('/\s*@include\s+([\.\w0-9_-]+)\s*/', $content, $includes_extends);
        for ($i = 0; $i < count($includes_extends[0]); $i++) {
            $include = $this->render->getInclude(trim($includes_extends[1][$i]));
            $content = str_replace($includes_extends[0][$i], $include, $content);
        }

        return $content;
    }

    protected function simpleParseVar($nameVar): ?string
    {
        // Check array var
        preg_match_all('/\[[\'|"]([\w+0-9-_]+)[\'|\"]\]/u', $nameVar, $matches);

        // as array
        if (count($matches[0]) > 0) {
            preg_match('/\w+/u', $nameVar, $nameVar);
            $nameVar = $nameVar[0];

            $keys = [];
            foreach ($matches[1] as $match) {
                array_push($keys, $match);
            }

            if (array_key_exists($nameVar, $this->data)) {
                $subjectArray = $this->data[$nameVar];

                if ($this->existsMultiKey($keys, $subjectArray)) {
                    $value = $this->getMultiKey($keys, $subjectArray);
                    return self::simpleCommitValue($value);
                } else {
                    return $nameVar;
                }
            }

            // as variable
        } else {
            if (array_key_exists($nameVar, $this->data)) {
                return $this->simpleCommitValue($this->data[$nameVar]);
            }
            return $this->simpleCommitValue($nameVar);
        }

        return 'err_none';
    }

    /**
     * @param $value
     * @return string
     */
    protected function simpleCommitValue($value): string
    {
        if (in_array(gettype($value), ['string', 'integer', 'boolean'])) {
            return (string)$value;
        }
        return gettype($value);
    }

    /**
     * Check if specific array key exists in multidimensional array
     *
     * @param array $arr
     * @param array $keys
     * @param int $i
     * @return bool
     */
    protected function existsMultiKey(array $keys, array $arr = null, $i = 0): bool
    {
        if ($arr === null) {
            $arr = $this->data;
        }

        if (key_exists($keys[$i], $arr)) {
            if (count($keys) === $i + 1) {
                return true;
            }

            return $this->existsMultiKey($keys, $arr[$keys[$i]], $i + 1);
        }

        return false;
    }

    /**
     *  Get specific array key
     *
     * @param array $arr
     * @param array $keys
     * @param int $i
     * @return array|null
     */
    protected function getMultiKey(array $keys, array $arr = null, $i = 0)
    {
        if ($arr === null) {
            $arr = $this->data;
        }

        if (key_exists($keys[$i], $arr)) {
            if (count($keys) === $i + 1) {
                return $arr[$keys[$i]];
            }

            return $this->getMultiKey($keys, $arr[$keys[$i]], $i + 1);
        }

        return null;
    }
}