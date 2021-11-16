<?php
/**
 * Login To Viberent
 * 
 * Plugin Name: Login To Viberent
 * Plugin URI: 
 * Description: Login
 * Version: 1.0
 * Author: Rizeen
 * Author URI: 
 * License: GPLv2 or later
 * Text Domain: viberent login
*/
define('VIBERENT__PLUGIN_DIR', plugin_dir_path(__FILE__));

//register the css files
function my_admin_scripts()
{
  wp_register_style('viberent_style', plugins_url('assets/css/viberent.css', __FILE__));
  wp_enqueue_style('viberent_style');
  wp_register_style('font-awesome', plugins_url('assets/css/font-awesome.min.css', __FILE__));
  wp_enqueue_style('font-awesome');
}
add_action('admin_enqueue_scripts', 'my_admin_scripts');

include_once('functions-layout-api.php');
include_once('viberent-hook.php');
?>