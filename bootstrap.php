<?php
require('vendor/autoload.php');


define('REDIS_SERVER_IP', '127.0.0.1');
define('REDIS_SERVER_PORT', 6379);

// Register a custom loader because composer is being chafe
spl_autoload_register(
    function ($className) {
        require('classes' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php');
    }
);

/**
 * Pretty print stuff in browser
 *
 * @param object $pre  Object to print
 * @param string $name Name to print above it
 *
 * @return void
 */
function pre($pre, $name = null)
{
    if ($name) {
        echo '<h3>' . $name . '</h3>';
    }
    echo '<pre>';
    print_r($pre);
    echo '</pre>';
}
