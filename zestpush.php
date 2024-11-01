<?php

 
 /**
 *
 * @link              https://zestpush.com/
 * @since             1.0.0
 * @package           Zestpush
 *
 * @wordpress-plugin
 * Plugin Name:       Zestpush - Web Push Notifications
 * Description:       Zestpush is a all around web push notification solution for websites.Maximize engagement and retention on your WordPress site with ZestPush. A powerhouse plugin offering seamless web push notifications. Experience the next level of user connection.
 * Version:           1.1.2
 * Author:            Zestpush
 * Author URI:        https://zestpush.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}

define('ZESTPUSH_VERSION','1.1.2');
define('ZESTPUSH_URL', plugin_dir_url(__FILE__));
define('ZEST_PATH', plugin_dir_path(__FILE__));

require_once 'includes/save-settings.php';
include ZEST_PATH .'user/zestpush-worker.php';

// run code on plugin activation
function zest_activate_zestpush(){
    include_once ZEST_PATH. 'admin/activation.php';
    zest_push_activation();
}
register_activation_hook( __FILE__, 'zest_activate_zestpush' );

function zest_deactivate_zestpush(){
    include_once ZEST_PATH. 'admin/deactivation.php';
    zestpush_deactivation();
}
register_deactivation_hook( __FILE__, 'zest_deactivate_zestpush' );

// include zestpush files on pages

function zest_initial_init_function() {
    // Check if 'activate_zest' option is set to '1'
    if (get_option('zest_status') === "1") {
        // Check if 'amp_is_request' function exists and if it's an AMP request
        if (function_exists('amp_is_request') && amp_is_request()) {
            include_once "user/include-on-amp.php"; // Include specific file for AMP requests
        } else {
            include_once 'user/include-main-js.php'; // Include main JavaScript for non-AMP requests
        }
    }
}
add_action('wp', 'zest_initial_init_function', 20);


add_action('admin_menu', 'zestpush_admin_menu');

// Function to add admin menu and sub-menu pages
function zestpush_admin_menu() {
    // Adding the main menu page ('Zestpush')
    add_menu_page(
        '',
        'Zestpush',
        '',
        'zest-options',
        'zest_menu_callback',
		ZESTPUSH_URL.'/assets/img/zestpush-logo.png'
    );
	
	// Adding Sub-menu page 2 ('States & Analytics')
    add_submenu_page(
        'zest-options', // Parent menu slug
        'Dashboard - zestpush',
        'Dashboard',
        'manage_options',
        'zest-states',
        'zest_submenu2_callback'
    );

    // Adding Sub-menu page 1 ('Zest Configuration')
    add_submenu_page(
        'zest-options', // Parent menu slug
        'Zest Configuration', 
        'API Configuration',
        'manage_options',
        'zest-configuration',
        'zest_submenu1_callback'
    );


}

// Callback function for the main menu page
function zest_menu_callback() {
    // Redirect to the sub-menu page ('API Configuration') from the main menu page
    wp_redirect(admin_url('admin.php?page=zest-configuration'));
    exit;
}

// Callback function for the first sub-menu page ('API Configuration')
function zest_submenu1_callback() {
    // Including content for the first sub-menu page
    include_once ZEST_PATH.'admin/configuration-page.php';
}

// Callback function for the second sub-menu page ('States & Analytics')
function zest_submenu2_callback() {
    // Including content for the second sub-menu page
    include_once ZEST_PATH.'admin/states-and-analytics.php';
}


function zestpush_style() {
    wp_enqueue_style( 'zestpush_style', ZESTPUSH_URL. 'assets/style/admin-meta.css' );
	wp_enqueue_style( 'zestpush_global', ZESTPUSH_URL. 'assets/style/admin-global.css' );
	wp_enqueue_script('zestpush_global_script', ZESTPUSH_URL . 'assets/js/admin-view.js');
	wp_enqueue_script( 'zestpush-chart', esc_url( ZESTPUSH_URL . 'assets/js/chart.js' ), array(), null, false );
}
add_action( 'admin_enqueue_scripts', 'zestpush_style' );

function zest_include_metabox_on_post_edit_page() {
    global $pagenow;

    // Check if current page is 'post.php' or 'post-new.php'
    if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {
        include_once ZEST_PATH . 'admin/add-metabox.php';
		include_once ZEST_PATH . 'includes/sendpush.php';
    }
}

add_action('admin_init', 'zest_include_metabox_on_post_edit_page');
    
