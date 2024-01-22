<?php

namespace Core;

class View {
    public static function display($view) {
        if (!file_exists("Views/{$view}.view.php")) {
            throw new \Exception("No matching view found for key {$view}");
            exit();
        }

        require "Views/{$view}.view.php";
    }

    public static function display404() {
        require base_path('Views/404.view.php');
    }
}