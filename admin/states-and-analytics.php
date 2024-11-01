<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$activation_status = get_option('zest_status');
	if($activation_status){
		$enable_zest = get_option('zest_status', '');
        $zest_init_enable = get_option('zest_init_status', '');
		$enable_zestmeta = get_option('zest_metastatus', '');
		$enable_zestsegment = get_option('zest_segmentstatus', '');
?>
<div class="zest-view">
	    <div class="zest-view-heading zest-block-global">
        <h3 class="zest-view-heading-01">
            Zestpush Dashboard
        </h3>
    </div>
<div class="zest-view-block zest-block-global" style="background:#292323;">
	<h3 style="color:white;">
		Last 30 days users overview:
	</h3>
	
<div style="height: 342px;" id="chart-container-month">
    <div class="loader">
        <span>Z</span>
        <span>E</span>
        <span>S</span>
        <span>T</span>
        <span>P</span>
        <span>U</span>
        <span>S</span>
        <span>H</span>
    </div>
</div>
</div>
	
<form method="post" action="" id="zest_save_configuration_form">
    <?php wp_nonce_field('zest_customize_action', 'zest_customize_nonce'); ?>
    <div class="zest-view-block zest-block-global">

        <div class="zest-view-block-heading">
            <h4 class="zest-view-block-heading-01">
                Zestpush configuration
            </h4>
        </div>
        <table class="zest-form-table" role="presentation" style="width:100%">
            <tbody>
                <tr style="padding-bottom:10px;">
                    <th scope="row"><div style="display:flex;"><span>Enable zestpush</span><div class="zest_suggestion_div"><span                           class="dashicons dashicons-editor-help"></span><p>zestpush service will completely shut down in your website.</p></div></div></th>
                    <td><label for="zest_main_chk" class="zest-switch">
                           <input type="checkbox" id="zest_main_chk" name="zest_main_chk" <?php checked($enable_zest, 1); ?>>
                              <span class="round"></span>
                        </label>
                    </td>
                </tr>
				<tr style="padding-bottom:10px;">
                    <th scope="row"><div style="display:flex;"><span>Enable metabox</span><div class="zest_suggestion_div"><span                           class="dashicons dashicons-editor-help"></span><p>zestpush metabox will be hidden in new and update post page.</p></div></div></th>
                    <td><label for="zest_meta_chk" class="zest-switch">
                           <input type="checkbox" id="zest_meta_chk" name="zest_meta_chk" <?php checked($enable_zestmeta, 1); ?>>
                              <span class="round"></span>
                        </label>
                    </td>
                </tr>
				<tr style="padding-bottom:10px;">
                    <th scope="row"><div style="display:flex;"><span>Send push to (All users)</span><div class="zest_suggestion_div"><span                           class="dashicons dashicons-editor-help"></span><p>if unchecked push will deliver to only "this websites users" (segment).</p></div></div></th>
                    <td><label for="zest_segment_chk" class="zest-switch">
                           <input type="checkbox" id="zest_segment_chk" name="zest_segment_chk" <?php checked($enable_zestsegment, 1); ?>>
                              <span class="round"></span>
                        </label>
                    </td>
                </tr>
                <tr style="padding-bottom:10px;">
                    <th scope="row"><div style="display:flex;"><span>Select (send push) on publish</span><div class="zest_suggestion_div"><span                           class="dashicons dashicons-editor-help"></span><p>Default send to all user option will select</p></div></div></th>
                    <td><label for="zest_post_chk" class="zest-switch">
                            <input type="checkbox" id="zest_post_chk" name="zest_post_chk" <?php checked($zest_init_enable, 1); ?>>
                               <span class="round"></span>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="zest-view-block-footer">
        <div>
            <input type="submit" value="Save Settings" class="zest_save_settings" name="zest_cust_settings" />
        </div>
    </div>
</form>

	
</div>

<script>
async function loadChart30days() {
  try {
	let zestPushAPI = '<?php echo esc_url(get_option("zest_domain")); ?>/api/v1/ext/rest-analytics';
    let headers = {
                "userid": '<?php echo esc_attr(get_option("zest_user_id")); ?>',
                "siteid": '<?php echo esc_attr(get_option("zest_site_id")); ?>',
                "key": '<?php echo esc_attr(get_option("zest_active_api")); ?>',
                "Content-Type": "application/json",
            }
    const requestOptions = {
                        method: 'POST',
                        headers: headers
                    }; 
    await fetch(zestPushAPI, requestOptions).then((response)=>{if (response.status !=200){return({message:"server request error"})};return response.json()}).then((data)=>{
    const chartdiv = document.getElementById('chart-container-month');
    if(data.message){
		console.log(data)
        chartdiv.innerHTML = '<div id="user-round-chart-state" style="height: 342px;">'+data.message + '</div>';
        return;
    }
    chartdiv.innerHTML = '<canvas id="chart-container-month-canvas" ></canvas>';
    const chartContainer = document.getElementById('chart-container-month-canvas');
    chartContainer.style.position = 'static';
    const ctx = chartContainer.getContext('2d');
    var gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgb(195, 25, 238)');   
    gradient.addColorStop(1, 'rgba(250,174,50,0)');
  
    Chart.defaults.borderColor = '#463c3c';
    Chart.defaults.color = '#fff';
  
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.date,
        datasets: [{
          label: 'user',
          data: data.count,
          borderWidth: 2,
          tension: 0.3,
          pointRadius: 0,
          borderColor: "#c319ee",
          backgroundColor: gradient,
          fill:true
        }]
      },
      options: {
        elements: {point:{radius: 2.4}},
        plugins: {
       legend: {
         display: false
       }
    },
        maintainAspectRatio: false,
        scales: {
          x: {
            grid: {
               display: false
            }
             },
          y: {
            grid: {
               display: true
            }
             }
        }
      }
    })  
  })} catch (error) {
    console.error('Error:', error);
}
}
loadChart30days();
</script>

<?php
	}else{
?>
<style>
    .main-div {
      width: 100%;
      height: 100%;
      min-height: 450px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background-color: #f2f2f2;
    }
    .inner-div {
        text-align: center;
        padding: 55px;
        background-color: #dbddff;
        border: 1px solid #ccc;
        border-radius: 8px;
        max-width: 400px;
    }
    .zest-warning-message {
        color: #282828;
        font-weight: bold;
        font-size: 17px;
        margin-bottom: 40px;
    }
    .zest-button {
        padding: 12px 20px;
        background-color: #f46767;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
</style>
<div class="main-div">
  <div class="inner-div">
    <p class="zest-warning-message">You cannot access this page before activation.</p>
    <a href="/wp-admin/admin.php?page=zest-configuration" class="zest-button">Go to Configuration Page</a>
  </div>
</div>

<?php
		
	}

?>
