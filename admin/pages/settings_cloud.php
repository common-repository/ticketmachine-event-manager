<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    global $ticketmachine_globals, $ticketmachine_api, $wpdb, $ticketmachine_db_version, $wp_version;

    $ticketmachine_config = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ticketmachine_config LIMIT 0,1");
    $ticketmachine_config = $ticketmachine_config[0];

    
	if (isset($_POST['submit'])) {

		if ( ! isset( $_POST['ticketmachine_settings_page_form_nonce'] ) || ! wp_verify_nonce( $_POST['ticketmachine_settings_page_form_nonce'], 'ticketmachine_action_save_settings' ) ) {
			print 'Sorry, your nonce did not verify.';
			exit;
		} else {
			$ticketmachine_post = (object)$_POST;
			$errors = array();

			$save_array = 
				array(
                    "webshop_url" => $ticketmachine_post->webshop_url
                );

                if (!empty($ticketmachine_config) && empty($errors)) {
                    $wpdb->update(
                        $wpdb->prefix . "ticketmachine_config",
                        $save_array,
                        array('id' => $ticketmachine_config->id)
                    );
                    ?>
                    <div class="notice notice-success is-dismissable">
                        <p><?php esc_html_e('Saved', 'ticketmachine-event-manager'); ?>!</p>
                    </div>
                    <?php
                    $ticketmachine_config->webshop_url = $ticketmachine_post->webshop_url;
                }else{
                    ?>
                    <div class="notice notice-error is-dismissable">
                        <p><?php esc_html_e('Something went wrong', 'ticketmachine-event-manager'); ?>!</p>
                            <?php 
                                if(!empty($errors)){
                                    foreach($errors as $error) {
                                        ?>
                                        <p><?php echo $error ?>!</p>
                                        <?php
                                    }
                                }
                            ?>
                        }
                    </div>
                    <?php
                }

        }
    }

    if(!empty($_GET['code'])) {
        //Exchange the auth code for an access token
        $token = ticketmachine_tmapi_get_access_token(sanitize_text_field($_GET['code']), "new");

        $current_organizer = (object)ticketmachine_tmapi_organizers()[0];

        $save_array = array(
            "api_access_token" => $token['access_token'],
            "api_refresh_token" => $token['refresh_token'],
            "api_refresh_last" => time(),
            "api_refresh_interval" => $token['expires_in']/2,
            "organizer_id" => $current_organizer->id,
            "organizer" => $current_organizer->og_abbreviation
        );

        if(!empty($token['access_token']) && !empty($token['refresh_token'])){
            $wpdb->update(
                $wpdb->prefix . "ticketmachine_config",
                $save_array,
                array('id' => $ticketmachine_config->id)
            );

            $ticketmachine_globals->activated = 1;
        ?>

        <div class="notice notice-success is-dismissable">
            <p><?php esc_html_e('Saved', 'ticketmachine-event-manager'); ?>!</p>
        </div>

        <?php
        }
    }

    $current_locale = get_locale();
    $parsed_locale = substr($current_locale, 0, strpos($current_locale, '_'));

    $authorize_url = $ticketmachine_api->auth->proxy;
    $authorize_url .= "?";
    $authorize_url .= http_build_query($ticketmachine_api->auth->data);
?>

<p class="mt-4">
    <?php esc_html_e("If your events are not showing - or you would like to change to a different account, you can synchronize your events here.", "ticketmachine-event-manager"); ?>
</p>

<a class="button button-primary" style="font-size:14px" href="<?php echo esc_url($authorize_url); ?>">
    <i class="fas fa-sync-alt"></i> &nbsp;<?php esc_html_e("Sync events", "ticketmachine-event-manager"); ?>
</a>

<a class="button button-primary" style="font-size:14px" href="<?php echo plugin_dir_url( __DIR__ ) . "debug.php"; ?>" target="_blank" download>
    <i class="fas fa-heartbeat"></i> &nbsp;<?php esc_html_e("Debug Log", "ticketmachine-event-manager"); ?>
</a>

<br/>

<table class="form-table">
    <tbody>
        <tr>
			<th><label><?php esc_html_e('Ticketshop abbreviation', 'ticketmachine-event-manager'); ?></label></th>
            <td>
                    <input name="webshop_url" type="text" value="<?php echo $ticketmachine_config->webshop_url; ?>" class="regular-text" />
                    <br>
                    <?php esc_html_e('Warning: Attention: The Ticketshop abbreviation must correspond to the abbreviation of the sales channel in the TicketMachine admin panel!', 'ticketmachine-event-manager'); ?>
            </td>
        </tr>
    </tbody>
</table>

<br>

<table class="form-table">
    <tbody>
        <tr>
			<th><label><?php esc_html_e('Cloud Connection', 'ticketmachine-event-manager'); ?></label></th>
            <td>
                <?php if($ticketmachine_globals->activated) {
                    if($ticketmachine_config->api_token_failed) {
                        echo "<div style='width: 11px; height: 11px;background-color: orange;border-radius: 100px;'></div>";
                    }else{
                        echo "<div style='width: 11px; height: 11px;background-color: green;border-radius: 100px;'></div>";
                    }
                }else{
                    echo "<div style='width: 11px; height: 11px;background-color: red;border-radius: 100px;'></div>";
                } ?>
           </td>
        </tr>
        <tr>
			<th><label><?php esc_html_e('Wordpress Version', 'ticketmachine-event-manager'); ?></label></th>
            <td>
                <?php echo $wp_version; ?>
           </td>
        </tr>
        <tr>
			<th><label><?php esc_html_e('Plugin Version', 'ticketmachine-event-manager'); ?></label></th>
            <td>
                <?php echo $ticketmachine_db_version; ?>
           </td>
        </tr>
        <tr>
			<th><label><?php esc_html_e('PHP Version', 'ticketmachine-event-manager'); ?></label></th>
            <td>
                <?php echo PHP_VERSION ?? $PHP_VERSION; ?>
           </td>
        </tr>
    </tbody>
</table>