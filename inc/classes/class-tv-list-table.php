<?php

namespace TV\classes;
use TV\trait\DB_Table as Transfer_Table;
use WP_List_Table;

// derectly access denied
defined('ABSPATH') || exit;

/**
 * create a new class TV_List_Table for viewing data into the default table
 * @version 1.0
 * @author Rubel Mahmud <vxlrubel@gmail.com>
 * @link https://github.com/vxlrubel
 */

if ( ! class_exists('WP_List_Table') ){
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class TV_List_Table extends WP_List_Table{

    use Transfer_Table;

    public function __construct(){
        parent::__construct( 
            [
                'singular' => 'Transfer Visitor',    // Singular name of the item
                'plural'   => 'Transfer Visitors',   // Plural name of the items
                'ajax'     => false,                 // If using AJAX, set to true
            ]
         );
    }
    /**
     * prepare items
     *
     * @return void
     */
    public function prepare_items(){

        $get_columns        = $this->get_columns();
        $get_hidden_columns = $this->get_hidden_columns();
        $data               = $this->get_items();
        
        $this->_column_headers = [ $get_columns, $get_hidden_columns ];
        $this->items = $data;
    }

    /**
     * modify name column
     *
     * @param [type] $item
     * @return void
     */
    public function column_name( $item ){
        $edit = sprintf(
            '<a href="javascript:void(0)" class="transfer-visitor-edit" data-id="%1$s">%2$s</a>',
            (int)$item['ID'],
            esc_html__( 'Edit', 'transfer-visitor' )
        );

        $delete = sprintf(
            '<a href="javascript:void(0)" class="transfer-visitor-delete" data-id="%1$s">%2$s</a>',
            (int)$item['ID'],
            esc_html__( 'Delete', 'transfer-visitor' )
        );

        $action = [
            'edit'   => $edit,
            'delete' => $delete,
        ];

        $elements = sprintf(
            '<a href="javascript:void(0)"class="row-title">%1$s</a> %2$s',
            $item['name'],
            $this->row_actions( $action )
        );
        
        return $elements;
    }

    /**
     * modify old url column
     *
     * @param [type] $item
     * @return void
     */
    public function column_old_url( $item ){

        $elements = sprintf(
            '<a href="%1$s" class="row-title" data-id="%2$s" target="_blank">%1$s</a>',
            $item['old_url'],
            $item['ID']
        );
        
        return $elements;
    }

    /**
     * modify old url column
     *
     * @param [type] $item
     * @return void
     */
    public function column_new_url( $item ){

        $elements = sprintf(
            '<a href="%1$s" class="row-title" data-id="%2$s" target="_blank">%1$s</a>',
            $item['new_url'],
            $item['ID']
        );
        
        return $elements;
    }

    /**
     * get all the items
     *
     * @return void
     */
    public function get_items(){
        global $wpdb;
        $response = '';
        $table    = $this->get_table_name();
        $sql      = "SELECT * FROM $table ORDER BY ID DESC";
        $result   = $wpdb->get_results( $sql, ARRAY_A );

        if ( $result > 0 ){
            $response = $result;
        }

        return $response;
    }

    /**
     * hide columns
     *
     * @return void
     */
    public function get_hidden_columns(){
        $hidden_columns = ['ID', 'updated_at'];
        return $hidden_columns;
    }

    /**
     * get columns
     *
     * @return void
     */
    public function get_columns(){
        $columns = [
            'cb'         => '<input type="checkbox" />',
            'ID'         => 'ID',
            'name'       => __( 'Name', 'transfer-visitor' ),
            'old_url'    => __( 'Old Url', 'transfer-visitor' ),
            'new_url'    => __( 'New Url', 'transfer-visitor' ),
            'created_at' => __( 'Create Date', 'transfer-visitor' ),
            'updated_at' => __( 'Update Date', 'transfer-visitor' ),
        ];

        return $columns;
    }

    /**
     * set checkbox for each item
     *
     * @return void
     */
    public function column_cb( $item ){
        return sprintf(
            '<input type="checkbox" name="transfer_visitor[]" value="%s" />',
            $item['ID']
        );
    }

    /**
     * set default coluns
     *
     * @param [type] $items
     * @param [type] $column_name
     * @return void
     */
    public function column_default( $items, $column_name ){
        return $items[ $column_name ];
    }

}