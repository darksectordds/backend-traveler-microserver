<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected $message = 'Bad Request';
    protected $code = 400;
}