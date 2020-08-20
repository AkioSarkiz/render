<?php

namespace Render\Exceptions;

use Exception;

class NotFoundFileException extends Exception
{
    public function __construct($path)
    {
        parent::__construct($path, 1, null);
    }
}