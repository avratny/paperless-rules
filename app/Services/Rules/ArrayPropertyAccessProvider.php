<?php

namespace App\Services\Rules;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ArrayPropertyAccessProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            // This allows property access on our ArrayPropertyAccess objects
            ExpressionFunction::fromPhp('property_exists'),
        ];
    }
}

