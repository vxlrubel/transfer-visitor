<?php
/*
 * Plugin Name:       Transfer Visitor
 * Plugin URI:        https://github.com/vxlrubel/transfer-visitor
 * Description:       The "Transfer Visitor" WordPress plugin facilitates URL redirection management via the WordPress dashboard using REST API endpoints. This plugin enables users to set up redirection pairs from old URLs to new destinations efficiently.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rubel Mahmud ( Sujan )
 * Author URI:        https://github.com/vxlrubel/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       transfer-visitor
 * Domain Path:       /lang
 */

 use TV\trait\DB_Table as Transfer_Table;

//  directly access denied
 defined('ABSPATH') || exit;

//  include autoload file
 if ( file_exists( dirname(__FILE__) . '/inc/autoload.php' ) ){
    require_once dirname(__FILE__) . '/inc/autoload.php';
 }

 final class Transfer_Visitor{

    use Transfer_Table;

    // version
    private $version = '1.0';

    // create instance
    private static $instance;

    /**
     * construct method
     */
    public function __construct(){

        // check WordPress version
        add_action( 'admin_init', [ $this, 'check_wp_version' ] );

        // load text domain
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );

        register_activation_hook( __FILE__, [ $this, 'activate_plugin' ] );
        
    }

    /**
     * activate plugin
     * create a custom table called transfer_visitor
     *
     * @return void
     */
    public function activate_plugin(){
        global $wpdb;
        $table = $this->get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table(
            ID mediumint(9) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255),
            old_url VARCHAR(255),
            new_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (ID)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( $sql );
        
    }

    /**
     * register text domain for this plugin
     *
     * @return void
     */
    public function load_textdomain(){
        load_plugin_textdomain( 
            'transfer-visitor',
            false,
            dirname( plugin_basename( __FILE__ ) ) . trailingslashit( '/lang' )
        );
    }

    /**
     * check WordPress version
     *
     * @return void
     */
    public function check_wp_version(){
        if ( version_compare( get_bloginfo( 'version' ), '5.2', '<' ) ) {
            add_action( 'admin_notice', [ $this, 'wp_version_notice' ] );
        }
    }

    /**
     * notice for update WordPress version
     *
     * @return void
     */
    public function wp_version_notice(){

        $notice_text = 'Transfer Visitor plugin requires WordPress version 5.2 or above. Please update your WordPress installation.';
        
        printf(
            '<div class="notice notice-warning"><p>%s</p></div>',
            __( $notice_text, 'transfer-visitor' )
        );
    }

    /**
     * create singleton instance
     *
     * @return void
     */
    public static function init(){
        if ( is_null( self::$instance ) ){
            self::$instance = new self();
        }
        return self::$instance;
    }
 }

 function transfer_visitor(){
    return Transfer_Visitor::init();
 }

 transfer_visitor();