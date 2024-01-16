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

        // register route for multiple delete
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/drop-items',
            [
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [ $this, 'multiple_delete_items' ],
                    'permission_callback' => [ $this, 'check_permission' ]
                ]
            ]
        );

        // register route for moving to trash
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/' . 'trash',
            [
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [ $this, 'move_to_trash_multiple_items' ],
                    'permission_callback' => [ $this, 'check_permission' ]
                ]
            ]
        );

        // register route for moving to trash
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/' . 'trash' . '/(?P<id>[\d]+)',
            [
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [ $this, 'move_to_trash' ],
                    'permission_callback' => [ $this, 'check_permission' ]
                ]
            ]
        );

        // register route for move to restore to publish status
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/' . 'restore',
            [
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [ $this, 'move_to_publish_multiple_items' ],
                    'permission_callback' => [ $this, 'check_permission' ]
                ]
            ]
        );

        // register route for move to restore to publish status
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/' . 'restore' . '/(?P<id>[\d]+)',
            [
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [ $this, 'move_to_publish' ],
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
     * move to trash
     *
     * @param [type] $request
     * @return void
     */
    public function move_to_trash( $request ){
        global $wpdb;
        $response = '';
        $table    = $this->get_table_name();
        $params   = $request->get_params();
        $data     = [
            'status' => 'trash'
        ];

        $data_format  = ['%s'];
        
        $where_clause = [
            'ID' => (int) $params['id']
        ];

        $where_clause_format = ['%d'];

        if( empty( $params['id'] ) ){
            return rest_ensure_response( 'id is required.' );
        }

        $update = $wpdb->update( $table, $data, $where_clause, $data_format, $where_clause_format );

        if ( $update === false ){
            return new WP_Error( 'update_failed', 'update unsuccessfull.', [ 'status' => 500 ] );
        }

        return rest_ensure_response( 'update successfull.' );

    }

    /**
     * move to trush multiple items
     *
     * @param [type] $request
     * @return void
     */
    public function move_to_trash_multiple_items( $request ){
        global $wpdb;
        $response = '';
        $table    = $this->get_table_name();
        $params   = $request->get_params();
        $ids      = $params['ids'];
        $data     = [
            'status'=> 'trash'
        ];

        $data_format         = ['%s'];
        $where_clause_format = ['%d'];

        if ( count( $ids ) === 0 ){
            return rest_ensure_response( 'Did not found the id' );
        }

        foreach ( $ids as $id ) {

            $where_clause        = [ 'ID' => (int)$id ];

            $update = $wpdb->update( $table, $data, $where_clause, $data_format, $where_clause_format );

            if ( $update === false ){
                return new WP_Error( 'update_failed', 'Move to trash failed.', [ 'status'=> 500 ] );
            }

            $response = 'Move to trash successfully.';
        }

        return rest_ensure_response( $response );
    }

    /**
     * move to publist 
     * this method implement for restoreing the items from trash
     *
     * @param [type] $request
     * @return void
     */
    public function move_to_publish_multiple_items( $request ){
        
    }

    /**
     * move to publish
     *
     * @param [type] $request
     * @return void
     */
    public function move_to_publish( $request ){
        global $wpdb;
        $response = '';
        $table    = $this->get_table_name();
        $params   = $request->get_params();
        $data     = [
            'status' => 'publish'
        ];

        $data_format  = ['%s'];
        
        $where_clause = [
            'ID' => (int) $params['id']
        ];

        $where_clause_format = ['%d'];

        if( empty( $params['id'] ) ){
            return rest_ensure_response( 'id is required.' );
        }

        $update = $wpdb->update( $table, $data, $where_clause, $data_format, $where_clause_format );

        if ( $update === false ){
            return new WP_Error( 'update_failed', 'move to publish unsuccessfull.', [ 'status' => 500 ] );
        }

        return rest_ensure_response( 'update successfull.' );
    }

    /**
     * delete items through their id
     *
     * @param [type] $request
     * @return void
     */
    public function delete_item( $request ){
        global $wpdb;
        $response            = '';
        $table               = $this->get_table_name();
        $params              = $request->get_params();
        $id                  = isset( $params['id'] ) ? (int)$params['id'] : '';
        $where_clause        = [ 'ID' => $id ];
        $where_clause_format = ['%s'];

        if ( empty( $id ) ){
            return rest_ensure_response( 'ID must required' );
        }

        $delete = $wpdb->delete( $table, $where_clause, $where_clause_format );

        if ( $delete === false ){
            return new WP_Error( 'delete_failed', 'unsuccefull to delete.', [ 'status'=> 500] );
        }

        return rest_ensure_response( 'Delete successfull.' );
        
    }

    /**
     * delete multiple items
     *
     * @param [type] $request
     * @return void
     */
    public function multiple_delete_items( $request ){
        global $wpdb;
        $response = '';
        $table    = $this->get_table_name();
        $params   = $request->get_params();
        $ids      = $params['ids'];

        if ( count( $ids ) === 0 ){
            return rest_ensure_response( 'Did not found deletable id' );
        }

        foreach ( $ids as $id ) {
            $where_clause        = [ 'ID' => (int)$id ];
            $where_clause_format = ['%s'];
            $multiple_delete     = $wpdb->delete( $table, $where_clause, $where_clause_format );

            if ( $multiple_delete === false ){
                return new WP_Error( 'delete_failed', 'delete request is not valid.', [ 'status'=> 500 ] );
            }

            $response = 'delete successfull.';
        }

        return rest_ensure_response( $response );
        
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