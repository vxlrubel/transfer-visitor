<?php

namespace TV\api;
use WP_REST_Server;
use WP_REST_Controller;
use TV\trait\DB_Table as Transfer_Table;

// derectly access denied
defined('ABSPATH') || exit;

/**
 * create a class API_Route_Register for register routes
 * @version 1.0
 * @author Rubel Mahmud <vxlrubel@gmail.com>
 * @link https://github.com/vxlrubel
 */
class API_Route_Register extends WP_REST_Controller {
    
    public function __construct(){
        $this->namespace = 'tv/v1';
        $this->rest_base = 'transfer-visitor';
    }

    /**
     * register rest routes
     *
     * @return void
     */
    public function register_routes(){

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'insert_item' ],
                    'permission_callback' => [ $this, 'check_permission' ]
                ],
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items' ],
                    'permission_callback' => [ $this, 'check_permission' ]
                ]
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            [
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [ $this, 'edit_item' ],
                    'permission_callback' => [ $this, 'check_permission' ]
                ],
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [ $this, 'delete_item' ],
                    'permission_callback' => [ $this, 'check_permission' ]
                ]
            ]
        );
    }

    /**
     * this callback use for create item
     *
     * @param [type] $request
     * @return void
     */
    public function insert_item( $request ){
        
    }

    /**
     * get all the items
     *
     * @param [type] $request
     * @return void
     */
    public function get_items( $request ){
        
    }

    /**
     * edit item using their id
     *
     * @param [type] $request
     * @return void
     */
    public function edit_item( $request ){
        
    }

    /**
     * delete items through their id
     *
     * @param [type] $request
     * @return void
     */
    public function delete_item( $request ){
        
    }

    /**
     * create permission callback
     *
     * @return void
     */
    public function check_permission(){
        $permission = current_user_can( 'manage_options' );
        return $permission;
    }
}