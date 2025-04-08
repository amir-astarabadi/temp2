<?php

namespace App\Exceptions\Authentication;

use Exception;

class LoginException extends Exception
{
    protected $message = 'Invalid credentials.';

    public function __construct(string $message = null, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}