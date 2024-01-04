<?php

namespace TV\classes;

// derectly access denied
defined('ABSPATH') || exit;

/**
 * create a class Admin_Menu
 * @version 1.0
 * @author Rubel Mahmud <vxlrubel@gmail.com>
 * @link https://github.com/vxlrubel
 */
class Admin_Menu{
    
    public function __construct(){
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * create admin menu
     *
     * @return void
     */
    public function admin_menu(){
        add_menu_page(
            __( 'Transfer Visitor', 'transfer-visitor' ), // page title
            __( 'Transfer Visitor', 'transfer-visitor' ), // menu title
            'manage_options', // capability
            'transfer-visitor', // menu slug
            [ $this, '_cb_list_page' ], // callback
            'dashicons-editor-ul', // icon
            25 //position
        );
    }
}