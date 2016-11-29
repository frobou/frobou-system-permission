<?php

namespace Frobou\SystemPermission\Exceptions;

use Exception;

class FrobouSystemPermissionUserException extends Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (is_null($message)){
            $message = 'User value is invalid';
        }
        // garante que tudo está corretamente inicializado
        parent::__construct($message, $code, $previous);
    }

    // personaliza a apresentação do objeto como string
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function customFunction()
    {
        echo "Uma função específica desse tipo de exceção\n";
    }

}