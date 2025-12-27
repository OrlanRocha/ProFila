<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    /**
     * @param array<string, array<string>> $rules
     * @return array<string, string>
     */
    public static function validate(array $input, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $input[$field] ?? null;

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $errors[$field] = 'Obrigatório';
                }

                if ($rule === 'email' && $value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'E-mail inválido';
                }
            }
        }

        return $errors;
    }
}
