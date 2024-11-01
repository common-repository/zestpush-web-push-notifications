<?php

function zest_make_request($url, $data, $option = false) {
    $userid = get_option('zest_user_id');
    $activation_key = get_option('zest_active_api');
    $site_id = get_option('zest_site_id');
    $domain = get_option('zest_domain');
    
    if ($option) {
        $domain = "app.zestpush.com";
    }
    
    $headers = array();
    $headers['Content-Type'] = 'application/json';
    $headers['userid'] = $userid;
    $headers['key'] = $activation_key;
    $headers['siteid'] = $site_id;
    
    $response = wp_remote_post('https://' . $domain . $url, array(
        'method'      => 'POST',
        'timeout'     => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => $headers,
        'body'        => json_encode($data),
        'cookies'     => array()
    ));
    
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        // For now, I'll return false to indicate a failed request
        return null;
    } else {
        $response_body = wp_remote_retrieve_body($response);
        return $response_body;
    }
}

?>
