<?php 

/*
Plugin Name: Health Traker BP Addon
Version: 1.6
Plugin URI: http://www.georgetudor.me/health-tracker.html
Description: Tracks health evolution based on question responses. Daily entry, variable creation and graph representation.

Author: George Tudor | SeventhQueen
Email: georgebitq@gmail.com
Author URI: http://www.georgetudor.me
*/

if (!defined('TRACKER_PATH')) {
    define('TRACKER_PATH', trailingslashit(plugin_dir_path(__FILE__)));
}

include_once TRACKER_PATH . 'HTracker.php';

global $tracker;
$tracker = new HTracker();

if(!function_exists('bp_is_active')) {
	add_action('admin_notices', array($tracker, 'notice'));
}

if(function_exists('w3tc_pgcache_flush')) {
	w3tc_pgcache_flush();
} elseif(function_exists('wp_cache_clear_cache')) {
	wp_cache_clear_cache();
}

register_activation_hook( __FILE__, array($tracker, 'ht_migrate'));
wp_enqueue_script('chartJS', plugin_dir_url(__FILE__) . 'js/Chart.min.js');
wp_enqueue_style('health-tracker', plugin_dir_url(__FILE__) . 'css/htstyle.css');