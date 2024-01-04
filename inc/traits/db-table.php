<?php

namespace TV\trait;

// derectly access denied
defined('ABSPATH') || exit;

/**
 * create a trait
 * @version 1.0
 * @author Rubel Mahmud <vxlrubel@gmail.com>
 * @link https://github.com/vxlrubel
 */


 trait DB_Table{
    
    // table name
    private $db_table_name = 'transfer_visitor';

    /**
     * get table name
     *
     * @return void
     */
    public function get_table_name(){
        global $wpdb;
        $table = $wpdb->prefix . $this->db_table_name;
        return $table;
    }
 }