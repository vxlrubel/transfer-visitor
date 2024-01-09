<?php

namespace TV\classes;
use TV\trait\DB_Table as Transfer_Table;

// derectly access denied
defined('ABSPATH') || exit;

/**
 * create a class Template for html stucture template of admin page
 * @version 1.0
 * @author Rubel Mahmud <vxlrubel@gmail.com>
 * @link https://github.com/vxlrubel
 */

 class Template{

   use Transfer_Table;

    /**
     * list page
     * this method display the main list page
     *
     * @return void
     */
    public static function list_page(){
      $action_url = $_SERVER['PHP_SELF'] . '?page=transfer-visitor';
      $list_table = new TV_List_Table;
      $list_table->prepare_items();

      ?>
      <div class="wrap transfer-visitor-list">

         <?php
            printf(
               '<h1 class="wp-heading-inline">%s</h1>',
               esc_html__( 'All Redirection Url', 'transfer-visitor' )
            );
         ?>

         <form action="<?php echo esc_url( $action_url );?>" method="POST" name="transfer_visitor_search_form">
            <?php $list_table->search_box( 'Search', 'transfer_visitor_search_box' ); ?>
         </form>

         <?php
            $list_table->display();
         ?>

      </div>
      <?php
    }

    /**
     * add_new_page
     * this method use to display the add_new_page stucture
     *
     * @return void
     */
    public static function add_new_page(){
      ?>
      <div class="wrap transfer-visitor-add-new">
         <div class="header">
            <h1>Add New Redirection.</h1>
            <a href="javascript:void(0)"> View List </a>
         </div>

         <div class="form-parent">
            <form action="javascript:void(0)" class="form-add-new-record">
               <h2 class="inner-title">Insert new record</h2>
               <table class="form-table" role="presentation">
                  <tbody>
                     <tr>
                        <th><label for="redirection-name">Redirection Name:</label></th>
                        <td>
                           <input type="text" class="regular-text" id="redirection-name">
                           <span class="small">Enter redirection name.</span>
                        </td>
                     </tr>
                     <tr>
                        <th><label for="old-url">Old URL:</label></th>
                        <td>
                           <input type="url" class="regular-text" id="old-url">
                           <span class="small">Enter old url which will be redireted.</span>
                        </td>
                     </tr>
                     <tr>
                        <th><label for="new-url">New URL:</label></th>
                        <td>
                           <input type="url" class="regular-text" id="new-url">
                           <span class="small">Enter url url where will be redirect.</span>
                        </td>
                     </tr>
                  </tbody>
               </table>
               <p class="submit">
                  <input type="submit" value="Add New Record" class="button button-primary add-new-record">
               </p>
            </form>
            <?php self::author_info(); ?>
         </div>
         
      </div>
      <?php
    }

    /**
     * options_page
     * this method use to display the options page
     *
     * @return void
     */
    public static function options_page(){
      printf('<div class="wrap">%s</div>', 'Options page.');
    }

    /**
     * author information
     *
     * @return void
     */
    private static function author_info(){
      ?>
         <div class="author-info">
            <h2 class="inner-title">Author Information</h2>
            <div class="author-info-box">
               <div class="author-img-box">
                  <img src="<?php echo TV_ASSETS_IMG . 'rubel-mahmud.jpg'; ?>" class="img-fluid" alt="Rubel Mahmud (Sujan)">
               </div>
               <div class="author-detail">
                  <h3 class="author-name">Rubel Mahmud (Sujan)</h3>
                  <p>Hi, I am a professional WordPress developer. I have created so many plugins and themes for my clients. If you like my plugin then hire me for your project.</p>
                  <h4>Social Share</h4>
                  <div class="social">
                     <a href="https://www.facebook.com/rubel.ft.me" target="_blank"><span class="dashicons dashicons-facebook-alt"></span></a>
                     <a href="https://www.linkedin.com/in/vxlrubel/" target="_blank"><span class="dashicons dashicons-linkedin"></span></a>
                     <a href="https://twitter.com/vxlrubel" target="_blank"><span class="dashicons dashicons-twitter"></span></a>
                     <a href="https://www.instagram.com/vxlrubel/" target="_blank"><span class="dashicons dashicons-instagram"></span></a>
                     <a href="https://www.reddit.com/user/vxlrubel" target="_blank"><span class="dashicons dashicons-reddit"></span></a>
                     <a href="https://api.whatsapp.com/send?phone=8801625601619&text=Hi, Rubel!" target="_blank"><span class="dashicons dashicons-whatsapp"></span></a>
                  </div>
                  <p>
                     <a href="https://api.whatsapp.com/send?phone=8801625601619&text=Hi, Mr. Rubel!" target="_blank" class="hire-me"><span class="dashicons dashicons-phone"></span> Hire Me</a>
                  </p>
               </div>
            </div>
         </div>
      <?php
    }

 }