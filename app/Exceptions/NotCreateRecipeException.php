<?php

namespace App\Exceptions;

use Exception;

class NotCreateRecipeException extends Exception
{
    public function __construct()
    {
        parent::__construct();
    }
}