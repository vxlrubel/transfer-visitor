<?php

// directly access denied
defined('ABSPATH') || exit;

$files = [
    'get-visitor-table',
];

foreach ( $files as $file ) {
    if( file_exists(  dirname( __FILE__ ) . '/' . $file . '.php' ) ){
        require_once dirname( __FILE__ ) . '/' . $file . '.php';
    }else{
        throw new Exception("file not found {$file}", 1);
    }
}