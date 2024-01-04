<?php

namespace TV\classes;
use TV\trait\DB_Table as Transfer_Table;

// derectly access denied
defined('ABSPATH') || exit;

/**
 * create a class Admin_Menu
 * @version 1.0
 * @author Rubel Mahmud <vxlrubel@gmail.com>
 * @link https://github.com/vxlrubel
 */
class Admin_Menu{
    use Transfer_Table;
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
            'manage_options',                             // capability
            $this->slug_main_menu,                        // menu slug
            [ $this, '_cb_list_page' ],                   // callback function
            'dashicons-editor-ul',                        // icon
            25                                            //position
        );

        add_submenu_page(
            $this->slug_main_menu,                  // parent slug
            __( 'All Items', 'transfer-visitor' ),  // page title
            __( 'All Items', 'transfer-visitor' ),  // menu title
            'manage_options',                       // capability
            $this->slug_main_menu,                  // menu slug
            [ $this, '_cb_list_page' ]              // callback
        );

        add_submenu_page(
            $this->slug_main_menu,                // parent slug
            __( 'Add New', 'transfer-visitor' ),  // page title
            __( 'Add New', 'transfer-visitor' ),  // menu title
            'manage_options',                     // capability
            $this->slug_add_new,                  // menu slug
            [ $this, '_cb_add_new_page' ]         // callback
        );
    }

    public function _cb_add_new_page(){
        printf('<div class="wrap">%s</div>', 'Add New page.');
    }

    /**
     * create list page
     *
     * @return void
     */
    public function _cb_list_page(){
        printf('<div class="wrap">%s</div>', 'hello world');
    }
}