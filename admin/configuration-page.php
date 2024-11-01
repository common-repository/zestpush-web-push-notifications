<?php
if (!defined("ABSPATH")) {
    exit();
}

$enable_zest = get_option("activate_zest", "");
$zest_active_api = get_option("zest_active_api", "");
$zest_user_id = get_option("zest_user_id", "");
$zest_site_id = get_option("zest_site_id", "");
$zest_endpoint = get_option("zest_endpoint", "");
?>

<div class="zest-view">
    <div class="zest-view-heading zest-block-global">
        <h3 class="zest-view-heading-01">
            Zestpush Dashboard
        </h3>
    </div>

    <form method="post" action="" id="zest_save_configuration_form">
        <?php wp_nonce_field(
            "zest_configuration_api_nonce",
            "zest_configuration_api_nonce_field"
        ); ?>
        <div class="zest-view-block zest-block-global" style="text-align:center;background: #c7c3ff;padding-bottom: 30px;">
                <div class="self-heading" style="padding-bottom: 15px;">
        <h3 style="font-size: 35px;font-family: initial;font-weight: 400;color: #8e0000;line-height: 50px;margin-bottom: 20px;">Ignite Audience Engagement: Just One Click Away!</h3>
        <span style="font-size: 20px;font-weight: 600;">Plugin status:</span><?php echo get_option(
            "zest_status"
        )
            ? '<span class="zest-act">Active</span>'
            : '<span class="zest-dct">Deactive</span>'; ?>
    </div>
            
             <div class="self-detail-chunk">
        <label for="zest_active_api">Zestpush activation key</label>
        <input name="zest_active_api" class="zest_active_api" id="zest_active_api" type="text" value="<?php echo esc_attr(
            $zest_active_api
        ); ?>" placeholder="Enter your activation key">
                            <div style="display:flex;justify-content: center;"><span>enter your zest activation api</span><div class="zest_suggestion_div"><span class="dashicons dashicons-editor-help"></span><p>you will find this in your zestpush intigration dashboard.</p></div></div>
    </div>
    <div class="self-detail-chunk">
        <label for="zest_user_id">User id</label>
        <input name="zest_user_id" class="zest_user_id" id="zest_user_id" type="text" value="<?php echo esc_attr(
            $zest_user_id
        ); ?>"  placeholder="Enter your userid">
            
    </div>
    <form action="/panel/selfhost" method="post">
    <div class="self-detail-chunk">
        <label for="zest_site_id">Site id</label>
         <input name="zest_site_id" class="zest_site_id" id="zest_site_id" type="text" value="<?php echo esc_attr(
             $zest_site_id
         ); ?>" placeholder="Enter your site id">
                            <div style="display:flex;justify-content: center;"><span>enter site id</span><div class="zest_suggestion_div"><span class="dashicons dashicons-editor-help"></span><p>you will find this in your zestpush intigration dashboard.</p></div></div>
    </div>
    <div class="self-detail-chunk">
        <input type="submit" value="Save Settings" class="zest_save_settings self-detail-submit" name="zest_conf_settings">
    </div>
    
    
    </div>        
    </form>
</div>




<script>
document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("zest_save_configuration_form");
  form.addEventListener("submit", function(event) {
    const requiredFields = ["zest_active_api", "zest_user_id", "zest_site_id"];
    let isEmpty = false;

    requiredFields.forEach(function(fieldName) {
      const inputField = form.querySelector(`[name="${fieldName}"]`);
      if (!inputField.value.trim()) {
        isEmpty = true;
        inputField.classList.add("zest-empty-field");
      } else {
        inputField.classList.remove("zest-empty-field");
      }
    });

    if (isEmpty) {
      event.preventDefault(); 
      alert("Please fill in all required fields.");
    }
  });
});
</script>