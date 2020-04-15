<?php
declare(strict_types=1);
namespace Apteles\Paginator\Exceptions;

use Exception;

class PageNotFoundException extends Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        \http_response_code($code);
        parent::__construct($message, $code, $previous);
    }
}
