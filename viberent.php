<?php
/**
 * Login To Viberent
 * 
 * Plugin Name: Login To Viberent
 * Plugin URI: https://wordpress.org/plugins/login-to-viberent
 * Description: Login To Viberent is a plugin created to fetch product details from viberent system as per the entered login credentials.
 * Version: 1.0.0
 * Author: Rizeen
 * Author URI: https://github.com/rizeenmujawar-tudip/viberent-plugin
 * License: GPLv2 or later
 * Text Domain: viberent login
*/
define('VIBERENT__PLUGIN_DIR', plugin_dir_path(__FILE__));

//register the css files
function viberent_admin_scripts()
{
  wp_register_style('viberent_style', plugins_url('assets/css/viberent.css', __FILE__));
  wp_enqueue_style('viberent_style');
  wp_register_style('font-awesome', plugins_url('assets/css/font-awesome.min.css', __FILE__));
  wp_enqueue_style('font-awesome');
}
add_action('admin_enqueue_scripts', 'viberent_admin_scripts');

// function theme_scripts()
// {
//   wp_enqueue_script('jquery');
// }
// add_action('admin_enqueue_scripts', 'theme_scripts');

// function mytheme_enqueue_typekit()
// {
//   wp_register_style('viberent_style', plugins_url('assets/js/moment.min.js', __FILE__));
//   wp_enqueue_style('viberent_style');
// }
// add_action('wp_enqueue_scripts', 'mytheme_enqueue_typekit');

include_once('functions-layout-api.php');
include_once('viberent-hook.php');

?>
