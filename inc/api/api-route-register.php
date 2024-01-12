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
    
    use Transfer_Table;

    public function __construct(){
        $this->namespace = 'transfer-visitor/v1';
        $this->rest_base = 'redirections';
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
                    'callback'            => [ $this, 'update_item' ],
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
        global $wpdb;
        $table    = $this->get_table_name();
        $params   = $request->get_params();
        $name     = isset( $params['name'] ) ? sanitize_text_field( $params['name'] ) : '';
        $old_url  = isset( $params['old_url'] ) ? esc_url( $params['old_url'] ) : '';
        $new_url  = isset( $params['new_url'] ) ? esc_url( $params['new_url'] ) : '';

        if( empty( $name ) ){
            return rest_ensure_response( 'please insert name field.' );
        }

        if( empty( $old_url ) ){
            return rest_ensure_response( 'please insert old url field.' );
        }

        if( empty( $new_url ) ){
            return rest_ensure_response( 'please insert new url field.' );
        }
        
        $existing_url = $wpdb->get_var( $wpdb->prepare( "SELECT old_url FROM $table WHERE old_url = %s", $old_url ) );

        if ( $existing_url !== null ){
            return rest_ensure_response( 'The old URL already exists in the table.' );
        }
        
        $data = [
            'name'    => $name,
            'old_url' => $old_url,
            'new_url' => $new_url,
        ];

        $result = $wpdb->insert( $table, $data );

        if ( $result === false ){
            return new WP_Error('failed_insert', 'insert failed', [ 'status' => 500 ] );
        }

        return rest_ensure_response( 'Successfully add a new record.' );
        
    }

    /**
     * get all the items
     *
     * @param [type] $request
     * @return void
     */
    public function get_items( $request ){
        global $wpdb;
        $table  = $this->get_table_name();
        $params = $request->get_params();
        $sql    = "SELECT * FROM $table ORDER BY ID DESC";
        $result = $wpdb->get_results( $sql );
        $params = $result;

        if ( count( $params ) < 1 ){
            return rest_ensure_response( 'No result found.' );
        }
        
        return rest_ensure_response( $params );
    }

    /**
     * update item using their id
     *
     * @param [type] $request
     * @return void
     */
    public function update_item( $request ){
        global $wpdb;
        $response = '';
        $table    = $this->get_table_name();
        $params   = $request->get_params();
        $id       = isset( $params['id'] ) ? (int)$params['id'] : '';
        $name     = isset( $params['name'] ) ? sanitize_text_field( $params['name'] ) : '';
        $old_url  = isset( $params['old_url'] ) ? esc_url( $params['old_url'] ) : '';
        $new_url  = isset( $params['new_url'] ) ? esc_url( $params['new_url'] ) : '';

        $data     = [
            'name'    => $name,
            'old_url' => $old_url,
            'new_url' => $new_url,
        ];

        $data_format         = ['%s'];
        $where_clause        = [ 'id' => $id ];
        $where_clause_format = ['%s'];

        if ( ! empty( $id ) && ! empty( $name ) && ! empty( $old_url ) && ! empty( $new_url ) ){
            $result = $wpdb->update( $table, $data, $where_clause, $data_format, $where_clause_format );
            if ( $result === false ){
                $response = new WP_Error( 'failed_update', 'update failed', [ 'status' => 500 ] );
            }
            $response = 'Data update successfull.';
        }else{
            $response = 'Fill all fields currectly.';
        }

        return rest_ensure_response( $response );
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