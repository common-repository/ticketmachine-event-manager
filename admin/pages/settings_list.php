<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	if (isset($_POST['submit'])) {

		if ( ! isset( $_POST['ticketmachine_settings_page_form_nonce'] ) || ! wp_verify_nonce( $_POST['ticketmachine_settings_page_form_nonce'], 'ticketmachine_action_save_settings' ) ) {
			print 'Sorry, your nonce did not verify.';
			exit;
		} else {
			$ticketmachine_post = (object)$_POST;
			$errors = array();

			//validate
			if (!empty($ticketmachine_post->show_list)){
				$ticketmachine_post->show_list = true;
			}else{
				$ticketmachine_post->show_list = false;
			}

			$save_array = 
				array(
					"show_list" => (bool)$ticketmachine_post->show_list
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
				$ticketmachine_config = $ticketmachine_post;
			}else{
				?>
				<div class="notice notice-error is-dismissable">
					<p><?php esc_html_e('Something went wrong', 'ticketmachine-event-manager'); ?>!</p>
				</div>
				<?php
			}
		}
	}
?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label><?php esc_html_e('Activate List?', 'ticketmachine-event-manager'); ?></label></th>
            <td><input name="show_list" type="checkbox" value=1 class="regular-text" <?php if($ticketmachine_config->show_list){ ?>checked<?php } ?>/></td>
		</tr>

	</tbody>
</table>