<?php

/*
  Plugin Name: URL Redirect and Rewrite
  Plugin URI: 
  Description: Url Redirect and Rewrite permits 
  Version: 0.0.1
  Author: Gokulakrishnan
  Author URI: https://about.me/sivasamygokul
  License: GPL2
 * 
 */

if (!class_exists('URL_Redirect_Rewrite')) {

   class URL_Redirect_Rewrite {

      /**
       * Construct the plugin object
       */
      public function __construct() {
         // register actions
         add_action('admin_menu', array(&$this, 'add_menu'));
         add_action('admin_init', array(&$this, 'admin_init'));

         add_action('init', array(&$this, 'url_redirect_rewrite_init'));
      }

      /**
       * Activate the plugin
       */
      public static function activate() {
         add_option('url_redirect_rewrite_map', array());
      }

      /**
       * Deactivate the plugin
       */
      public static function deactivate() {
         // Do nothing
      }

      /**
       * hook into WP's admin_init action hook
       */
      public function admin_init() {
         // Set up the settings for this plugin
         // register the settings for this plugin
         register_setting('url_redirect_rewrite_option', 'url_redirect_rewrite_map');

         if (isset($_POST['url_redirect_rewrite_delete'])) {
            $map = get_option('url_redirect_rewrite_map', array());

            foreach ($map as $key => $value) {
               if ($value['name'] == $_POST['url_redirect_rewrite_delete']) {
                  unset($map[$key]);
               }
            }

            update_option('url_redirect_map', $map);
         }

         if (isset($_POST['url_redirect_rewrite_reset'])) {
            $map = get_option('url_redirect_rewrite_map', array());

            foreach ($map as $key => $value) {
               if ($value['name'] == $_POST['url_redirect_rewrite_reset']) {
                  $value['click'] = 0;

                  $map[$key] = $value;
               }
            }

            update_option('url_redirect_rewrite_map', $map);
         }

         if (
                 isset($_POST['url_redirect_rewrite_name']) and
                 isset($_POST['url_redirect_rewrite_link']) and
                 isset($_POST['url_redirect_rewrite_type']) and
                 
                 $_POST['url_redirect_rewrite_name'] != '' and
                 $_POST['url_redirect_rewrite_type'] != '' and
                 $_POST['url_redirect_rewrite_link'] != ''
         ) {

            $name = $_POST['url_redirect_rewrite_name'];
            $link = esc_url_raw($_POST['url_redirect_rewrite_link'], 'http');
            $type = $_POST['url_redirect_rewrite_type'];
            $save = TRUE;

            $map = get_option('url_redirect_rewrite_map', array());

            foreach ($map as $key => $value) {
               if ($value['name'] == $name) {
                  $value['link'] = $link;
                  $value['type'] = $type;

                  $map[$key] = $value;

                  $save = FALSE;
               }
            }

            if ($save) {
               $map[] = array(
                   'name' => $name,
                   'link' => $link,
                   'type' => $type,
                   'click' => 0
               );
            }

            update_option('url_redirect_rewrite_map', $map);
         }
      }

      /**
       * add a menu
       */
      public function add_menu() {
         add_management_page("URL Redirect Rewrite ", "URL Redirect Rewrite", "manage_categories", 'wp_url_redirect_rewrite', array(&$this, 'url_redirect_rewrite_settings_page'));
      }

      public
              function url_redirect_rewrite_settings_page() {
         if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
         }

         $map = get_option('url_redirect_rewrite_map', array());

         wp_enqueue_script('', plugins_url('js/admin.js', __FILE__), array('jquery'), time(), true);

         // Render the settings template
         include(sprintf("%s/templates/tools.php", dirname(__FILE__)));
      }

      function url_redirect_rewrite_init() {
         $name = substr($_SERVER["REQUEST_URI"], 1);

         $map = get_option('url_redirect_rewrite_map', array());

         $key = array_search($name, array_column($map, 'name'));

         
         if(!$key)
         {
            if(substr($name , -1)!=='/')
            {
              $name = $name."/";
            }
            else
            {
              $name = preg_replace('{/$}', '', $name);
            }
            $key = array_search($name, array_column($map, 'name'));
         }
         
         if($key)
         {
            if($map[$key]['type'] == "redirect")
            {
              wp_redirect($map[$key]['link']);
              exit();
            }
            else
            {
              
              $uri_to_rewrite = str_replace(wp_guess_url(),"",$map[$key]['link']);
              if(substr($uri_to_rewrite , -1)!=='/')
              {
                $uri_to_rewrite = $uri_to_rewrite."/";
              }
                           
              $_SERVER['REQUEST_URI'] = $uri_to_rewrite;
                         
            }
            
             $map[$key]['click'] = $map[$key]['clik']++;
             update_option('url_redirect_rewrite_map', $map);

         }
        
         // var_dump(isset($map["message"]["action"])); // false

         // foreach ($map as $key => $value) {
         //    if ($value['name'] == $name) {
         //       $value['click'] ++;
         //       $map[$key] = $value;


         //       update_option('url_redirect_rewrite_map', $map);

         //       wp_redirect($value['link']);
         //       exit;
         //    }
         // }
      }

   }

}

if (class_exists('URL_Redirect_Rewrite')) {
   // Installation and uninstallation hooks
   register_activation_hook(__FILE__, array('URL_Redirect_Rewrite', 'activate'));
   register_deactivation_hook(__FILE__, array('URL_Redirect_Rewrite', 'deactivate'));

   // instantiate the plugin class
   $wp_footer_pop_up_banner = new URL_Redirect_Rewrite();

   if (isset($wp_footer_pop_up_banner)) {

      // Add the settings link to the plugins page
      function url_redirect_rewrite_settings_link($links) {
         $settings_link = '<a href="tools.php?page=wp_url_redirect_rewrite">Settings</a>';
         array_unshift($links, $settings_link);
         return $links;
      }

      $plugin = plugin_basename(__FILE__);
      // var_dump($plugin);exit();
      add_filter("plugin_action_links_$plugin", 'url_redirect_rewrite_settings_link');
   }
}   