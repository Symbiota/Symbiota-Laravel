<?php

namespace App\Helpers;

class InputNormalizer {
    public static function optionalInt(mixed $value): ?int {
        if ($value === null) {
            return null;
        }

        if (is_string($value) && trim($value) === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }
}