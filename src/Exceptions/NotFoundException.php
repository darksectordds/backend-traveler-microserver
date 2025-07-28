<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected $message = 'Resource not found';
    protected $code = 404;
}