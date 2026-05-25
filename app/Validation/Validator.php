<?php

declare(strict_types=1);

namespace App\Validation;

final class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            foreach (explode('|', $ruleString) as $rule) {
                $value = trim((string) ($data[$field] ?? ''));
                if ($rule === 'required') {
                    $this->checkRequired($field, $value);
                } elseif (str_starts_with($rule, 'min:')) {
                    $this->checkMin($field, $value, (int) substr($rule, 4));
                } elseif (str_starts_with($rule, 'max:')) {
                    $this->checkMax($field, $value, (int) substr($rule, 4));
                }
            }
        }

        return $this->errors === [];
    }

    private function checkRequired(string $field, string $value): void
    {
        if ($value === '') {
            $this->errors[$field][] = ucfirst($field) . ' is required.';
        }
    }

    private function checkMin(string $field, string $value, int $min): void
    {
        if ($value === '') {
            return;
        }
        if (strlen($value) < $min) {
            $this->errors[$field][] = ucfirst($field) . " must be at least {$min} characters.";
        }
    }

    private function checkMax(string $field, string $value, int $max): void
    {
        if (strlen($value) > $max) {
            $this->errors[$field][] = ucfirst($field) . " must not exceed {$max} characters.";
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
