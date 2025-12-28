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

                if ($rule === 'email' && $value !== null) {
                    $isValid = filter_var($value, FILTER_VALIDATE_EMAIL);

                    // Permite formatos locais (ex.: dev@local) usados em ambientes internos
                    if (!$isValid && str_contains((string) $value, '@')) {
                        $isValid = true;
                    }

                    if (!$isValid) {
                        $errors[$field] = 'E-mail inválido';
                    }
                }

                // permite e-mails locais sem TLD, desde que contenham @
                if ($rule === 'email_local' && ($value === null || !str_contains((string) $value, '@'))) {
                    $errors[$field] = 'E-mail inválido';
                }
            }
        }

        return $errors;
    }
}
