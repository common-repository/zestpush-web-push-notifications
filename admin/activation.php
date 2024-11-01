<?php

function zest_push_activation() {
	// set existing activation status to false
    if (!get_option('my_plugin_option')) {
        update_option('zest_status', false);
		update_option("zest_plan", "");
		update_option("zest_metastatus", true);
		update_option("zest_segmentstatus", false);
		update_option("zest_serviceworker","/zestpush-worker.js");
    };
}
?>