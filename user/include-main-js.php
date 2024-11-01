<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Function to add a script tag to the <head> section of the page
function zest_push_non_amp_head() {
    ?>
    <!-- zestpush initigration -->
    <script id="zest-main-script" src="<?php echo esc_url(get_option("zest_domain")=="app.zestpush.com"?"https://cdn.zestpush.com":"https://".get_option("zest_domain")."/user-assets") ?>/frontend/<?php echo esc_attr(get_option("zest_user_id")) ?>-<?php echo esc_attr(get_option("zest_site_id")) ?>.js"></script>
    <?php
}
// Hooking the function to 'wp_head', ensuring it's added in the <head> section
add_action('wp_head', 'zest_push_non_amp_head');
?>
