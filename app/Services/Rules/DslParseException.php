<?php

namespace App\Services\Rules;

class DslParseException extends \Exception
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct(implode("\n", $errors));
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

