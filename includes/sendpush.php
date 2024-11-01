<?php

if (!defined("ABSPATH")) {
    exit();
}

require_once "request.php";

// Function to save zest meta box data

function zest_get_post_details_on_publish($post_ID) {
    $post = get_post($post_ID);

    if ($post->post_status !== 'publish' && $post->post_status !== 'future') {
        return;
    }

    $post_title = $post->post_title;

    // Get the post excerpt or content if excerpt is empty
    if (empty($post->post_excerpt)) {
        $post_content = wp_strip_all_tags($post->post_content);
        $post_excerpt = wp_trim_words($post_content, 100, '');
    } else {
        $post_excerpt = wp_strip_all_tags($post->post_excerpt);
    }

    // Get the full permalink
    $post_permalink = get_permalink($post_ID);

    // Get the post thumbnail URL
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    $post_thumbnail_url = wp_get_attachment_url($post_thumbnail_id);

    // Ensure the permalink is not a shortlink
    if (strpos($post_permalink, '?p=') !== false) {
        $post_permalink = esc_url(get_permalink($post_ID));
    }

    // Check if post is scheduled and get the 13-digit epoch time
    if ($post->post_status === 'future') {
        // Convert post date to WordPress timezone
        $post_date_gmt = strtotime(get_gmt_from_date($post->post_date));
        $post_publish_time = $post_date_gmt * 1000; // Convert to milliseconds
    } else {
        $post_publish_time = null; // If not scheduled, return null
    }

    $post_details = array(
        'title' => $post_title,
        'excerpt' => $post_excerpt,
        'thumbnail_url' => $post_thumbnail_url,
        'permalink' => $post_permalink,
        'schedule' => $post_publish_time // Add the schedule time or null
    );

    return $post_details;
}


function zest_save_push_meta($post_ID, $post, $update) {
    // Check if this is an autosave
    if (!$update || !$post->post_status === 'publish') {
        return;
    }
	
	// Validating the nonce

    if (isset($_POST['zest_push_varify'])) {
        $nonce = sanitize_text_field(wp_unslash($_POST['zest_push_varify'])); // Sanitize and unsanitize nonce field
    
        if (!wp_verify_nonce($nonce, 'zest_push_nonce')) {
            // Nonce verification failed
            return;
        }
    } else {
        // Nonce field not set, abort processing
        return;
    }
    

    // Initialize variables
    $selected_option = '';
    $meta_title = '';
    $meta_body = '';
    $link = '';
    $img = '';
	$resp = '';
	$schedule = false;
	$segm = false;
    
    // Check radio button value
    if (isset($_POST['zest-selectbox'])) {
        $selected_option = sanitize_text_field($_POST['zest-selectbox']);
    }
    
    $post_details = zest_get_post_details_on_publish($post_ID); // Assuming this function retrieves post details on publish
    
    // Check meta box values if 'custom' option is selected
    if ($selected_option === 'custom' && (isset($_POST['zest-meta-title']) || isset($_POST['zest-meta-body']))) {
        $meta_title = isset($_POST['zest-meta-title']) && $_POST['zest-meta-title'] !=""  ? sanitize_text_field($_POST['zest-meta-title']) : $post_details["title"];
        $meta_body = isset($_POST['zest-meta-body']) && $_POST['zest-meta-body'] !="" ? sanitize_text_field($_POST['zest-meta-body']) : $post_details["excerpt"];
		$segm = isset($_POST['zest-meta-user']) ? sanitize_text_field($_POST['zest-meta-user']) : false;
    } else {
        $meta_title = isset($post_details["title"]) ? $post_details["title"] : "";
        $meta_body = isset($post_details["excerpt"]) ? $post_details["excerpt"] : "";
		$segm = get_option('zest_segmentstatus', false) ? false : 'zest110';
    }
	
	$link = isset($post_details["permalink"]) ? $post_details["permalink"] : "";
	$schedule = isset($post_details["schedule"]) ? $post_details["schedule"] : false;
    $img = isset($post_details["thumbnail_url"]) ? $post_details["thumbnail_url"] : "";
	$custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo = get_site_icon_url() ? get_site_icon_url() : ( wp_get_attachment_image_src( $custom_logo_id , 'full' ) ? wp_get_attachment_image_src( $custom_logo_id , 'full' )[0] : '' );

    // Display additional meta box values if 'custom' option is selected
    $message = 'Meta title: ' . $meta_title . '<br>Meta body: ' . $meta_body;
	
    $push_notification = array(
        "title" => $meta_title,
        "body" => $meta_body,
		"link" => $link,
        "img" => $img,
        "logo" => $logo,
		"segment" => $segm,
		"schedule" =>$schedule
    );
if(isset($selected_option) && $selected_option !="" && $selected_option != "no"){
	$res = zest_make_request(
    "/api/v1/ext/rest-notification",
    $push_notification,
    false
    );
}else{
	$res == null;
}
	
	$res = json_decode($res, true);
	
	if($res != null && isset($res['pushid']) ){
		$resp = "Push notification send successfully!!";
	}else{
		$resp = "Something went wrong during send push.";
	}
	
	if(isset($selected_option) && $selected_option !="" && $selected_option != "no"){
		update_option('zestpush_push_response', $resp);
	}
    
}
add_action('save_post', 'zest_save_push_meta', 10, 3);



add_action('admin_notices', function () {
	global $pagenow;
	$respo_value = get_option('zestpush_push_response');

	if ($respo_value != "empty" && $pagenow == 'post.php' && isset($_GET['message']) && ($_GET['message'] == '1' || $_GET['message'] == '6')) {
		$respo_value_escaped = esc_html($respo_value); // Escape the response value

		echo '<div class="notice notice-success is-dismissible"><p>'.$respo_value_escaped.'</p></div>';
	}
	update_option('zestpush_push_response', "empty");
});
