<?php
/**
 * Enable autoloading of classes in modules which are derived from this structure
 * @param $className
 */
const DS = DIRECTORY_SEPARATOR;

spl_autoload_register(function ( $className ) {
    $content = str_replace("\\", "/", $className) . ".php";
    $base = WP_CONTENT_DIR . DS . 'plugins' . DS . '*' . DS . 'vendor/';
    
    $results = glob($base . $content);

    if(count($results)) {
        return require_once $results[0];
    }

    $content = str_replace("_", "/", $className) . ".php";
    $results = glob($base . $content);

    if(count($results)) {
        return require_once $results[0];
    }

});

