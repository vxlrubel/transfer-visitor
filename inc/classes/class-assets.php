<?php

namespace TV\classes;

// derectly access denied
defined('ABSPATH') || exit;

/**
 * create a class Assets for enqueue scripts
 * @version 1.0
 * @author Rubel Mahmud <vxlrubel@gmail.com>
 * @link https://github.com/vxlrubel
 */
class Assets{
    public function __construct(){
        add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
    }

    /**
     * register enqueue scripts
     *
     * @return void
     */
    public function register_scripts(){
        wp_enqueue_style(
            'tv-admin-style',                  // handle
            TV_ASSETS . 'css/admin-style.css', // source
            [],                                // deps
            TV_VERSION,                        // version
        );
        wp_enqueue_script(
            'tv-admin-script',                       // handle
            TV_ASSETS . 'js/admin-script.js',        // source
            ['jquery', 'jquery-ui-tabs'],            // deps
            TV_VERSION,                              // version
            true                                     // in footer
        );

        $data_set = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => home_url('/wp-json/transfer-visitor/v1/redirections'),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
        ];
        
        wp_localize_script( 'tv-admin-script', 'redirections', $data_set );
    }
}