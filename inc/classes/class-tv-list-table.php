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
        
        $order_by    = isset( $_GET['orderby'] ) ? trim( $_GET['orderby'] ) : 'ID';
        $order       = isset( $_GET['order'] ) ? trim( $_GET['order'] ) : 'DESC';
        $search_term = isset( $_POST['s'] ) ? trim( $_POST['s'] ) : '';

        $get_columns        = $this->get_columns();
        $get_hidden_columns = $this->get_hidden_columns();
        $data               = $this->get_items( $order_by, $order, $search_term );
        $sortable_columns   = $this->get_sortable_columns();

        // pagination
        $items_per_page = 10;
        $current_page   = $this->get_pagenum();
        $total_items    = (int) count( $data );

        $this->set_pagination_args([
            'total_items' => $total_items,
			'per_page'    => $items_per_page,
        ]);

        $trimed_data = array_slice( $data, ( $current_page - 1 ) * $items_per_page, $items_per_page );
        
        $this->_column_headers = [ $get_columns, $get_hidden_columns, $sortable_columns ];
        $this->items = $trimed_data;
    }

    /**
     * get sortable columns
     *
     * @return void
     */
    public function get_sortable_columns(){
        $sortable_columns = [
            'name'    => [ 'name', false ],
            'old_url' => [ 'old_url', false ],
            'new_url' => [ 'new_url', false ],
        ];

        return $sortable_columns;
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
            '<a href="javascript:void(0)"class="row-title" data-ajax-name="%3$s">%1$s</a> %2$s',
            $item['name'],
            $this->row_actions( $action ),
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
    public function column_old_url( $item ){

        $elements = sprintf(
            '<a href="%1$s" class="row-title" data-id="%2$s" data-ajax-old-url="%2$s" target="_blank">%1$s</a>',
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
            '<a href="%1$s" class="row-title" data-id="%2$s" data-ajax-new-url="%2$s" target="_blank">%1$s</a>',
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
    public function get_items( $order_by, $order, $search_term ){
        global $wpdb;
        $response = '';
        $table    = $this->get_table_name();
        $sql      = "SELECT * FROM $table ORDER BY $order_by $order";

        if( ! empty( $search_term ) ){
            $sql = "SELECT * FROM $table WHERE name LIKE '%$search_term%' OR old_url LIKE '%$search_term%' OR new_url LIKE '%$search_term%'";
        }
        
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
            'status'     => __( 'Status', 'transfer-visitor' ),
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

    /**
     * add bulk action
     *
     * @return void
     */
    public function get_bulk_actions(){
        $actions = [
            'delete' => 'Detete',
        ];
        return $actions;
    }

    /**
     * add attributes in tr tag
     *
     * @param [type] $item
     * @return void
     */
    public function single_row( $item ) {
        echo "<tr class=\"transfer_visitor-item-id-{$item['ID']}\">";
		$this->single_row_columns( $item );
		echo '</tr>';
	}
    
    /**
     * check permission
     *
     * @return void
     */
    public function ajax_user_can(){
        return current_user_can( 'manage_options' );
    }

    /**
     * set view item count
     *
     * @return void
     */
    public function get_views(){
        global $wpdb;
        $table      = $this->get_table_name();
        $sql        = "SELECT ID FROM $table";
        $result_all = $wpdb->get_results( $sql, ARRAY_A );

        $all_status    = 'current';
        $active_status = 'active';
        $trush_status  = 'trush';

        if( isset( $_GET['status'] ) ){
            $all_status    = $_GET['status'] == 'all' ? 'current' : '';
            $active_status = $_GET['status'] == 'active' ? 'current' : '';
            $trush_status  = $_GET['status'] == 'trush' ? 'current' : '';
        }

        $count_all  = sprintf(
            '<a href="%1$s" class="%2$s">%3$s<span class="count">(%4$d)</span></a>',
            esc_url( admin_url( 'admin.php?page=' . $this->slug_main_menu . '&status=all' ) ),
            esc_attr( $all_status ),
            esc_html( 'All' ),
            count( $result_all )
        );

        $count_active  = sprintf(
            '<a href="%1$s" class="%2$s">%3$s<span class="count">(%4$d)</span></a>',
            esc_url( admin_url( 'admin.php?page=' . $this->slug_main_menu . '&status=active' ) ),
            esc_attr( $active_status ),
            esc_html( 'Active' ),
            count( $result_all )
        );
        
        $count_trush  = sprintf(
            '<a href="%1$s" class="%2$s">%3$s<span class="count">(%4$d)</span></a>',
            esc_url( admin_url( 'admin.php?page=' . $this->slug_main_menu . '&status=trush' ) ),
            esc_attr( $trush_status ),
            esc_html( 'Trush' ),
            count( $result_all )
        );

        return [
            'all'    => $count_all,
            'active' => $count_active,
            'trush'  => $count_trush,
        ];
    }

}