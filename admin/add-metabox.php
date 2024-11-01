<?php

if (!defined("ABSPATH")) {
    exit();
}

add_action('add_meta_boxes', function () {
    if(get_option('zest_status') === "1" && get_option('zest_metastatus') === "1") {
        add_meta_box('zestpush-selectbox', 'Zest push notification', 'zest_push_box', 'post', 'side', 'high');
    }
});

function zest_push_box($post)
{
    $zest_init_enable = get_option('zest_init_status', '');
    $nonce = wp_create_nonce('zest_push_nonce'); // Generating nonce
    $nonce_escaped = esc_attr($nonce); // Escape the nonce value
    ?>
    <input type="hidden" name="zest_push_varify" value="<?php echo $nonce_escaped; ?>">
    <label for="zest-all" class="zest-switch">
      <input type="radio" id="zest-all" name="zest-selectbox" value="yes" <?php checked($zest_init_enable, 1); ?>>
      <span class="round"></span>
    </label>
    <span> &nbsp;Send push</span><br><br>

    <label for="zest-none" class="zest-switch">
      <input type="radio" id="zest-none" name="zest-selectbox" value="no" <?php checked(!$zest_init_enable, 1); ?>>
      <span class="round"></span>
    </label>
    <span> &nbsp;Do not send</span><br><br>

    <label for="zest-custom" class="zest-switch">
      <input type="radio" id="zest-custom" name="zest-selectbox" value="custom" >
      <span class="round"></span>
    </label>
    <span> &nbsp;Custom push</span>

    <div class="zest-meta-hidden" style="display:none">

        <div class="zest-meta-pushbox">
            <label for="zest-meta-title" class="zest-push-title-label">push title</label>
            <textarea name="zest-meta-title" class="zest-meta-title" placeholder="Leave empty for default post title"></textarea>
        </div>

        <div class="zest-meta-pushbox">
            <label for="zest-meta-body" class="zest-push-body-label">push body</label>
            <textarea name="zest-meta-body" class="zest-meta-body" rows="4" placeholder="Leave empty for default post excerpt"></textarea>
        </div>

        <div class="zest-meta-pushbox">
            <label for="zest-meta-user" class="zest-push-user-label">Select user:</label>
            <select name="zest-meta-user" style="width:100%">
                <option value="false">To all user</option>
                <option value="zest110">To this website only</option>
                <option value="zest111">Mobile Subscribers</option>
                <option value="zest112">Desktop Subscribers</option>
                <option value="zest113">Subscribed Yesterday</option>
                <option value="zest114">Subscribed In Last 7 Days</option>
                <option value="zest115">Subscribed In Last 15 Days</option>
                <option value="zest116">Subscribed In Last 30 Days</option>
            </select>
        </div>

    </div>

    <script>
        const radioButtons = document.getElementsByName("zest-selectbox");
        radioButtons.forEach((elem) => {
            elem.addEventListener("click", () => {
                if(elem.value=="custom"){
                    document.getElementsByClassName("zest-meta-hidden")[0].style.display = "block";
                }
                else{
                    document.getElementsByClassName("zest-meta-hidden")[0].style.display = "none";
                }
            });
        });
    </script>

    <?php
};