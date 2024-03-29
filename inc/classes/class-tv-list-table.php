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

    private $is_trash;

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
        $status      = isset( $_GET['status'] ) && $_GET['status'] == 'trash' ? trim( $_GET['status'] ) : 'publish';

        $get_columns        = $this->get_columns();
        $get_hidden_columns = $this->get_hidden_columns();
        $data               = $this->get_items( $status, $order_by, $order, $search_term );
        $sortable_columns   = $this->get_sortable_columns();

        // pagination

        $items_per_page = '';

        if( isset( $_POST['set_items'] ) ){
            update_option('_filering_item_count', $_POST['set_items'] );
            $items_per_page = get_option( '_filering_item_count' );
        }else{
            if( ! empty( get_option( '_filering_item_count' ) ) ){
                $items_per_page = get_option( '_filering_item_count' );
            }else{
                $items_per_page = 10;
            }
            
        }

        $current_page   = $this->get_pagenum();
        $total_items    = (int) count( $data );

        $this->set_pagination_args([
            'total_items' => $total_items,
			'per_page'    => $items_per_page,
        ]);

        $trimed_data = array_slice( $data, ( $current_page - 1 ) * $items_per_page, $items_per_page );
        
        $this->is_trash = isset( $_REQUEST['status'] ) && 'trash' === $_REQUEST['status'];

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
            esc_html__( 'Delete Permanently', 'transfer-visitor' )
        );

        $trash = sprintf(
            '<a href="javascript:void(0)" class="submit-trush" id="%1$s">%2$s</a>',
            $item['ID'],
            esc_html__( 'Trash', 'transfer-visitor' )
        );

        $restore = sprintf(
            '<a href="javascript:void(0)" id="%1$s" class="submit-restore">%2$s</a>',
            (int) $item['ID'],
            esc_html__( 'Restore', 'transfer-visitor' )
        );

        $action = [
            'edit'   => $edit,
            'trash'  => $trash,
        ];

        if ( isset( $_GET['status'] ) && $_GET['status'] == 'trash' ){
            $action['restore'] = $restore;
            $action['delete']  = $delete;
            unset( $action['edit'] );
            unset( $action['trash'] );
        }
        
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
    public function get_items( $status, $order_by, $order, $search_term ){
        global $wpdb;
        $response = '';
        $table    = $this->get_table_name();
        $sql      = "SELECT * FROM $table WHERE status = '$status' ORDER BY $order_by $order";

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

        $actions = [];

        if ( $this->is_trash ){
            $actions['restore'] = esc_html__( 'Restore', 'transfer-visitor' );
            $actions['delete']  = esc_html__( 'Delete parmanently', 'transfer-visitor' );
        }else{
            $actions['trash']  = esc_html__( 'Move to trash', 'transfer-visitor' );
            $actions['delete'] = esc_html__( 'Detete parmanently', 'transfer-visitor' );
        }

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
        $table          = $this->get_table_name();
        $sql_all        = "SELECT ID FROM $table";
        $sql_publish    = "SELECT ID FROM $table WHERE status = 'publish'";
        $sql_trash      = "SELECT ID FROM $table WHERE status = 'trash'";
        $result_all     = $wpdb->get_results( $sql_all, ARRAY_A );
        $result_publish = $wpdb->get_results( $sql_publish, ARRAY_A );
        $result_trash   = $wpdb->get_results( $sql_trash, ARRAY_A );

        $all_status     = 'current';
        $publish_status = 'publish';
        $trash_status   = 'trash';

        if( isset( $_GET['status'] ) ){
            $all_status     = '';
            $publish_status = $_GET['status'] == 'publish' ? 'current' : '';
            $trash_status   = $_GET['status'] == 'trash' ? 'current' : '';
        }

        $count_all  = sprintf(
            '<a href="%1$s" class="%2$s">%3$s<span class="count">(%4$d)</span></a>',
            esc_url( admin_url( 'admin.php?page=' . $this->slug_main_menu ) ),
            esc_attr( $all_status ),
            esc_html( 'All' ),
            count( $result_all )
        );

        $count_publish  = sprintf(
            '<a href="%1$s" class="%2$s">%3$s<span class="count">(%4$d)</span></a>',
            esc_url( admin_url( 'admin.php?page=' . $this->slug_main_menu . '&status=publish' ) ),
            esc_attr( $publish_status ),
            esc_html( 'Publish' ),
            count( $result_publish )
        );
        
        $count_trash  = sprintf(
            '<a href="%1$s" class="%2$s">%3$s<span class="count">(%4$d)</span></a>',
            esc_url( admin_url( 'admin.php?page=' . $this->slug_main_menu . '&status=trash' ) ),
            esc_attr( $trash_status ),
            esc_html( 'Trush' ),
            count( $result_trash )
        );

        return [
            'all'    => $count_all,
            'active' => $count_publish,
            'trush'  => $count_trash,
        ];
    }

    /**
     * extran tale nav for page item count
     *
     * @param [type] $which
     * @return void
     */
    protected function extra_tablenav( $which ){
        $action         = $_SERVER['PHP_SELF'] . '?page=transfer-visitor';
        $get_item_count = '';
        if( ! empty( get_option( '_filering_item_count' ) ) ){
            $get_item_count = get_option( '_filering_item_count' );
        }else{
            $get_item_count = 10;
        }
        
        ?>
            <div class="alignleft actions">
                
                <form action="<?php echo esc_url( $action ); ?>" method="POST" class="filter-item-per-page-form">
                    <span>show</span> 
                    <select name="set_items">
                        <option value="10" <?php selected( $get_item_count, 10, true ); ?>>10</option>
                        <option value="20" <?php selected( $get_item_count, 20, true ); ?>>20</option>
                        <option value="30" <?php selected( $get_item_count, 30, true ); ?>>30</option>
                        <option value="40" <?php selected( $get_item_count, 40, true ); ?>>40</option>
                        <option value="50" <?php selected( $get_item_count, 50, true ); ?>>50</option>
                    </select>
                    <input type="submit" value="Filter" class="button action">
                </form>
            </div>
        <?php
    }

}