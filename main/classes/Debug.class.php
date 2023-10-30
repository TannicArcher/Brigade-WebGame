<?php
/**
 * Debug
 */

class Debug
{
    function __construct()
    {
    }

    public static function view($value, $print = false)
    {
        echo '<pre>';
        if ($print == true) {
            print_r($value);
        } else {
            var_dump($value);
        }
        echo '</pre>';
    }
}