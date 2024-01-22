<?php

namespace Core;

class Form {
    public static function empty($input) {
        if (count($input) === 0) {
            return true;
        }

        foreach ($input as $key => $value) {
            if (empty(trim($value))) {
                return true;
            }
        }
        return false;
    }
}