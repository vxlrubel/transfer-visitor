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
      ?>
      <div class="wrap transfer-visitor-list">
         <h1 class="wp-heading-inline">list</h1>
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
            <form action="javascript:void(0)">
               <h2>Insert new record</h2>
               <div class="field">
                  <label>
                     Redirection Name:
                     <input type="text" class="widefat">
                  </label>
               </div>

               <div class="field">
                  <label>
                     Old Url:
                     <input type="url" class="widefat">
                  </label>
               </div>

               <div class="field">
                  <label>
                     New Url:
                     <input type="url" class="widefat">
                  </label>
               </div>
               <div class="field">
                  <input type="submit" value="Add New Record" class="button button-primary">
               </div>
            </form>
            <div class="author-info">
               <h2>Author Information</h2>
               <div class="author-info-box">
                  <div class="img-fluid">
                     <img src="" alt="Rubel Mahmud (Sujan)">
                  </div>
                  <div class="author-detail">
                     <h3>Rubel Mahmud (Sujan)</h3>
                     <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Distinctio odio voluptas, natus molestiae dolore debitis?</p>
                     <h4>Social Share</h4>
                     <div class="social">
                        <a href="https://www.facebook.com/rubel.ft.me" target="_blank"><span class="dashicons dashicons-facebook-alt"></span></a>
                        <a href="https://www.linkedin.com/in/vxlrubel/" target="_blank"><span class="dashicons dashicons-linkedin"></span></a>
                        <a href="https://twitter.com/vxlrubel" target="_blank"><span class="dashicons dashicons-twitter"></span></a>
                        <a href="https://www.instagram.com/vxlrubel/" target="_blank"><span class="dashicons dashicons-instagram"></span></a>
                        <a href="https://www.reddit.com/user/vxlrubel" target="_blank"><span class="dashicons dashicons-reddit"></span></a>
                        <a href="https://api.whatsapp.com/send?phone=8801625601619&text=Hi, Rubel!" target="_blank"><span class="dashicons dashicons-whatsapp"></span></a>
                     </div>
                  </div>
               </div>
            </div>
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

 }