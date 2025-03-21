<?php

namespace App\Core\Download;

trait RowMap {
    public static function map_row($unmapped_row) {
        $row = self::$fields;
        foreach ($unmapped_row as $key => $value) {
            if (array_key_exists($key, self::$ignores)) {
                continue;
            }

            // Map Casted Values
            if (array_key_exists($key, self::$casts)) {
                if (array_key_exists(self::$casts[$key], $row)) {
                    $row[self::$casts[$key]] = $value;
                }
            }
            // Map DB Values
            elseif (array_key_exists($key, $row)) {
                $row[$key] = $value;
            }

            // Generate Row Dervied Values
            foreach (self::$derived as $key => $fn) {
                if (array_key_exists($key, $row) && ! $row[$key]) {
                    $row[$key] = self::callDerived($key, $unmapped_row);
                }
            }
        }

        return $row;
    }

    public static function callDerived($key, $arg) {
        if (array_key_exists($key, self::$derived)) {
            return forward_static_call([self::class, self::$derived[$key]], $arg);
        }
    }
}
