<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

abstract class BaseRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     */
    abstract public function passes($attribute, $value): bool;

    /**
     * Get the validation error message.
     */
    abstract public function message(): string;
}

