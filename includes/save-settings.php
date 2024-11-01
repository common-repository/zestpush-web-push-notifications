<?php if (!defined("ABSPATH")) {
    exit();
}

// Function to handle form submission and save configuration
function zest_handle_configuration_save() {
    if( isset( $_POST['zest_conf_settings'] )) {
        zest_save_configuration();
    }
	if( isset( $_POST['zest_cust_settings'] ) ) {
		zest_save_customize();
	}
}

add_action('init', 'zest_handle_configuration_save');

function zest_save_configuration()
{
    require_once "request.php";

    if (
        isset($_POST["zest_conf_settings"]) &&
        isset($_POST["zest_configuration_api_nonce_field"]) && // Check if nonce field is set
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["zest_configuration_api_nonce_field"])), // Sanitize and verify nonce
            "zest_configuration_api_nonce"
        )
    ) {
        $zest_active_api = isset($_POST["zest_active_api"]) ? sanitize_text_field($_POST["zest_active_api"]) : ''; // Sanitize the active API
        $zest_user_id = isset($_POST["zest_user_id"]) ? sanitize_text_field($_POST["zest_user_id"]) : ''; // Sanitize the user ID
        $zest_site_id = isset($_POST["zest_site_id"]) ? sanitize_text_field($_POST["zest_site_id"]) : ''; // Sanitize the site ID
        $serviceworker = isset($_POST["zest_serviceworker"]) ? sanitize_text_field($_POST["zest_serviceworker"]) : ''; // Sanitize the service worker URL

        // Validate data if needed (e.g., check if IDs are numeric)

        update_option("zest_active_api", $zest_active_api); // Update options with sanitized data
        update_option("zest_user_id", $zest_user_id);
        update_option("zest_site_id", $zest_site_id);
        update_option("zest_serviceworker", $serviceworker);

        $res = zest_make_request(
            "/api/v1/ext/plugin",
            ["type" => "activation"],
            true
        );
        $res = json_decode($res, true);

        $domain = isset($res["url"]) ? sanitize_text_field($res["url"]) : false; // Sanitize the domain
        $plan = isset($res["plan"]) ? sanitize_text_field($res["plan"]) : false; // Sanitize the plan

        if ($res == null || !$domain || !$plan || (isset($res["plan_status"]) && $res["plan_status"] != "active") || (isset($res["message"]))) {
            add_action("admin_notices", function () use ($res) {
                echo "<div class='notice notice-error is-dismissible'><p>Something went wrong, please check credentials!! " . (isset($res["message"]) ? esc_html(sanitize_text_field($res["message"])) : "") . "</p></div>";
            });
            update_option("zest_status", false);
            return;
        }
		
        add_action("admin_notices", function () use ($res) {
            echo "<div class='notice notice-success is-dismissible'><p>Your Plugin is activated successfully!!</p></div>";
        });

        update_option("zest_domain", $domain);
        update_option("zest_plan", $plan);
        update_option("zest_status", true);
        update_option("zest_init_status", true);
		flush_rewrite_rules();
        
        // File content
        $file_content = 'importScripts("https://' . $domain . '/scripts/worker.js?userid=' . $zest_user_id . '&siteid=' . $zest_site_id . '");';
        
        // Path to WordPress root directory
        $root_directory = ABSPATH;
        $file_path = $root_directory . 'zestpush-worker.js';
        
        // Create or update the file
        if (file_put_contents($file_path, $file_content) !== false) {
            echo 'File created successfully';
        } else {
            echo 'There was an error creating the file';
        }
    }
};

function zest_save_customize(){
    // Nonce verification
    if( isset($_POST['zest_customize_nonce']) && wp_verify_nonce($_POST['zest_customize_nonce'], 'zest_customize_action') ) {
        if(isset($_POST['zest_main_chk']) && $_POST['zest_main_chk'] == 'on') {
            update_option("zest_status", true);
        } else {
            update_option("zest_status", false);
        }
        
        if(isset($_POST['zest_post_chk']) && $_POST['zest_post_chk'] == 'on') {
            update_option("zest_init_status", true);
        } else {
            update_option("zest_init_status", false);
        }
		
		if(isset($_POST['zest_meta_chk']) && $_POST['zest_meta_chk'] == 'on') {
            update_option("zest_metastatus", true);
        } else {
            update_option("zest_metastatus", false);
        }
		
		if(isset($_POST['zest_segment_chk']) && $_POST['zest_segment_chk'] == 'on') {
            update_option("zest_segmentstatus", true);
        } else {
            update_option("zest_segmentstatus", false);
        }
    }
}


?>
