<?php

namespace Render\Exceptions;

use Exception;

class NotSupportTagException extends Exception
{
    public function __construct($tagName)
    {
        parent::__construct("Tag \"$tagName\" not support", 1, null);
    }
}