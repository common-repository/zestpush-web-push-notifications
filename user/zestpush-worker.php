<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Register service worker for PWA's

if( class_exists( 'PWAFORWP_Plugin_Usage_Tracker' ) ){
	function zest_pwaforwp_change_sw_name($name){
        $name = 'zestpush-worker.js';         
        return $name;
    }
    add_filter( 'pwaforwp_sw_name_modify', 'zest_pwaforwp_change_sw_name' );
	
    function zest_add_pwaforwp_worker($content){
		$zest_domain = esc_attr( get_option("zest_domain") );
        $zest_user_id = esc_attr( get_option("zest_user_id") );
        $zest_site_id = esc_attr( get_option("zest_site_id") );

        $sw_url = "https://".$zest_domain."/scripts/worker.js?userid=".$zest_user_id."&siteid=".$zest_site_id;
    	$content = 'importScripts("'.$sw_url.'")'."\n".$content;
    	return $content;
    };
    add_filter("pwaforwp_sw_js_template", 'zest_add_pwaforwp_worker',10,1);
	return;
}

// Register custom rewrite rule
function zest_custom_rewrite_rule() {
    add_rewrite_rule('^zestpush-worker.js/?$', 'index.php?zest_custom_output=true', 'top');
}
add_action('init', 'zest_custom_rewrite_rule');

// Add query variable for custom output
function zest_custom_query_vars($vars) {
    $vars[] = 'zest_custom_output';
    return $vars;
}
add_filter('query_vars', 'zest_custom_query_vars');

// Handle custom output
function zest_custom_handle_output() {
    global $wp;

    // Check if the current request is for the custom output
    if ( isset( $wp->query_vars['zest_custom_output'] ) ) {
        // Set headers
        header("Service-Worker-Allowed: /");
        header("Content-Type: application/javascript");
        header("X-Robots-Tag: none");

        // Escape options and variables before echoing
        $zest_domain = esc_attr( get_option("zest_domain") );
        $zest_user_id = esc_attr( get_option("zest_user_id") );
        $zest_site_id = esc_attr( get_option("zest_site_id") );

        $sw_url = "https://".$zest_domain."/scripts/worker.js?userid=".$zest_user_id."&siteid=".$zest_site_id;
		echo 'importScripts("'.$sw_url.'")';
        exit; // Stop further processing
    }
}
add_action('parse_request', 'zest_custom_handle_output');

