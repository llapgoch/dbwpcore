<?php
/**
 * Enable autoloading of classes in modules which are derived from this structure
 * @param $className
 */

spl_autoload_register(function ( $className ) {
    $content = str_replace("\\", "/", $className) . ".php";
    $base = WP_CONTENT_DIR . '/plugins/*/vendor/';
    
    $results = glob($base . $content);

    if(count($results)) {
        require_once $results[0];
    }
});

