<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function zest_push_amp_head() {
?>
<script async custom-element="amp-web-push" src="https://cdn.ampproject.org/v0/amp-web-push-0.1.js"></script>

<?php
}
add_action( 'wp_head', 'zest_push_amp_head' );

function zest_push_amp_body() {
	$website_url = get_site_url();
?>


<amp-web-push id="amp-web-push" layout="nodisplay"
    helper-iframe-url="<?php echo esc_url( ZESTPUSH_URL.'user/zest-amp-helper.html' );?>"
    permission-dialog-url="<?php echo esc_url( ZESTPUSH_URL.'user/zest-amp-permission.html' );?>"
    service-worker-url="<?php echo esc_url( ZESTPUSH_URL.'user/zestpush-worker.js.php' ); ?>">
</amp-web-push>

<amp-web-push-widget layout="fixed" id="amp-web-w" visibility="unsubscribed" width="0px" height="0px">
    <div id="popupContainer">
        <div id="popupContainerchild">
            <div id="popupContainerchild2">
                <p style="font-weight: 600;">Do you want to subscribe our notifications ?</p>
                <div id="popupContainerchild3">
                    <button id="amppushbutton2" on="tap:popupContainer.hide,amp-web-w.hide"
                        style="background: #ff5a5a;">Block</button>
                    <button id="amppushbutton1" on="tap:amp-web-push.subscribe"
                        style="background: #4f45ec;">Allow</button>
                </div>
            </div>
        </div>
    </div>
</amp-web-push-widget>
<style amp-custom>
#amp-web-w {
    display: block;
}

#popupContainer {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
}

#popupContainerchild {
    display: flex;
    justify-content: center;
    align-items: center;
}

#popupContainerchild2 {
    background-color: #fff;
    padding: 15px;
    border-radius: 5px;
    text-align: center;
    border: 1px solid #686868;
    max-width: 400px;
    margin: 10px;
    box-shadow: 3px 4px 10px -2px #6c6c6c;
}

#popupContainerchild3 {
    display: flex;
    gap: 10px;
    justify-content: center;
}

div#popupContainerchild3 button {
    line-height: 18px;
    border: unset;
    border-radius: 5px;
}
</style>

<?php
}
add_action( 'wp_body_open', 'zest_push_amp_body' );
?>