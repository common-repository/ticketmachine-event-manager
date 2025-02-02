<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    global $ticketmachine_globals, $ticketmachine_api, $wpdb;
    wp_enqueue_media();

    //defaults
    $timestamp = new DateTime();
    $event = array(
        "state" => array(
            "shown" => 1
        ),
        "approved" => 1,
        "ev_name" => "", 
        "ev_description" => esc_html__('Event Description', 'ticketmachine-event-manager'),
        "event_img_url" => str_replace("/admin/pages", "", plugin_dir_url(__FILE__)) . 'assets/img/none.png',
        
        "ev_location_name" => "",
        "event_location" => array(
            "street" => "",
            "house_number" => "",
            "zip" => "",
            "city" => "",
            "country" => ""
        ),

        "rules" => array(
            "badge" => "",
            "sale_active" => 0,
            "prices_shown" => 0
        ),

        "tags" => array(),

        "entrytime" => date(DATE_ISO8601, strtotime("today 10:00")),
        "ev_date" =>  date(DATE_ISO8601, strtotime("today 11:00")),
        "endtime" =>  date(DATE_ISO8601, strtotime("today 23:59"))
    );
    $organizer = array(
        "og_name" => "",
        "og_street" => "",
        "og_house_number" => "",
        "og_zip" => "",
        "og_city" => "",
        "og_country" => "",
        "og_email" => "",
        "og_phone" => ""
    );

    if(!empty($_GET['mode']) && $_GET['mode'] == "community" && is_plugin_active( 'ticketmachine-community-events/ticketmachine-community-events.php' )) {
        $isCommunityEvent = 1;
    }

    if(!empty($_GET['id'])){
        $params = [ "id" => absint($_GET['id']) ];
        if(isset($isCommunityEvent)) {
                $table = $wpdb->prefix . "ticketmachine_events";
                $event = (array)$wpdb->get_row( "SELECT * FROM $table WHERE `id` = " . $params['id'] );
                if(!empty($event['id'])){
                    $table = $wpdb->prefix . "ticketmachine_organizers_events_match";
                    $event_organizer_match = $wpdb->get_row( "SELECT * FROM $table WHERE `local_event_id` = " . $event['id'] );
                    if(!empty($event_organizer_match)){
                        $table = $wpdb->prefix . "ticketmachine_organizers";
                        $organizer = $wpdb->get_row( "SELECT * FROM $table WHERE `id` = " . $event_organizer_match->organizer_id );
                    }
                    if(!empty($event["event_image"])) {
                        $event["event_img_url"] = $event["event_image"];
                    }
                    $event['old_id'] = $event['id'];
                    $event['id'] = "";
                    $event['event_location']['street'] = $event['street'];
                    $event['event_location']['house_number'] = $event['house_number'];
                    $event['event_location']['zip'] = $event['zip'];
                    $event['event_location']['city'] = $event['city'];
                    $event['event_location']['country'] = $event['country'];
                    $event['approved'] = 1;
                    $event["rules"]["badge"] = "";
                    $event["rules"]["sale_active"] = 0;
                    $event["rules"]["prices_shown"] = 0;
                    $event["rules"]["shown"] = 1;
                }else{
                    unset($isCommunityEvent);
                }
        }else{
            $event = ticketmachine_tmapi_event($params);

            $table = $wpdb->prefix . "ticketmachine_organizers_events_match";
            $event_organizer_match = $wpdb->get_row( "SELECT * FROM $table WHERE `api_event_id` = " . $event->id );
            if(!empty($event_organizer_match)){
                $table = $wpdb->prefix . "ticketmachine_organizers";
                $organizer = $wpdb->get_row( "SELECT * FROM $table WHERE `id` = " . $event_organizer_match->organizer_id );
            }
        }
    }

    $event = (object)$event;
    $organizer = (object)$organizer;

    if(empty($event->event_img_url)) {
        $event->event_img_url = str_replace("/admin/pages", "", plugin_dir_url(__FILE__)) . 'assets/img/none.png';
    }
?>


<div class="wrap tm-admin-page">
    <h1 class="dont-display"></h1>
    
    <?php 
        if(!empty($event->id)){
            echo "<h1 class='wp-heading-inline'>TicketMachine > " . esc_html__('Edit event', 'ticketmachine-event-manager') . "</h1>";
        } else {
            echo "<h1 class='wp-heading-inline'>TicketMachine > " . esc_html__('Create event', 'ticketmachine-event-manager') . "</h1>";
        }
    ?>
    
    <form name="event" action="?page=ticketmachine_events&action=save<?php if(!empty($event->id)){ echo "&id=" . esc_attr(absint($_GET['id'])); } ?>" method="post" id="event">
		<?php wp_nonce_field( 'ticketmachine_action_save_event', 'ticketmachine_event_edit_form_nonce' ); ?>
        <?php if(isset($event->old_id)) { ?>
            <input type="hidden" name="old_id" data-name="<?php echo esc_html__('Event Details', 'ticketmachine-event-manager'); ?>" value="<?php echo esc_attr($event->old_id); ?>">
        <?php } ?>
        <input type="hidden" name="organizer_id" value="<?php echo esc_attr($ticketmachine_globals->organizer_id); ?>">
        <input type="hidden" name="rules[sale_active]" value="<?php echo esc_attr($event->rules["sale_active"]); ?>">
        <input type="hidden" name="rules[prices_shown]" value="<?php echo esc_attr($event->rules["prices_shown"]); ?>">
        <input type="hidden" name="rules[shown]" value="<?php echo esc_attr($event->rules["shown"]); ?>">
        <input type="hidden" name="vat_id" value="1">
        
        <?php if(!empty($event->id)){ ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($event->id); ?>">
        <?php } ?>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" style="position: relative;">
                    <div id="titlediv">
                        <div id="titlewrap">
                            <label class="screen-reader-text" id="post-name-prompt-text" for="ev_name"><?php esc_html_e('Enter the event name', 'ticketmachine-event-manager') ?></label>
                            <input type="text" placeholder="<?php esc_attr('Event Name', 'ticketmachine-event-manager') ?>" name="ev_name" size="30" id="title" spellcheck="true" autocomplete="off" value="<?php echo esc_attr($event->ev_name); ?>">
                        </div>
                    </div>

                    <?php 
                        $editor_id = 'ev_description';
                        $settings = array( 'media_buttons' => false );
                        $content = $event->ev_description;
                        wp_editor( $content, $editor_id, $settings);
                    ?>
                </div>

                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
                        <div id="submitdiv" class="postbox">
                            <h2 class="hndle px-3 py-2 mt-0">
                                <span><?php esc_html_e('Publish', 'ticketmachine-event-manager') ?></span>
                            </h2>
                            <div class="inside">
                                <div class="submitbox" id="submitpost">
                                    <div id="minor-publishing">
                                        <div id="misc-publishing-actions">
                                            <div class="misc-pub-section misc-pub-post-status">
                                                <span><?php esc_html_e('Status', 'ticketmachine-event-manager') ?>: </span>
                                                <select style="float: right; margin-top: -2px;" name="approved">
                                                    <option value="1" <?php if($event->approved == 1){ echo "selected"; } ?>><?php esc_html_e('Published', 'ticketmachine-event-manager') ?></option>
                                                    <option value="0" <?php if($event->approved != 1){ echo "selected"; } ?>><?php esc_html_e('Draft', 'ticketmachine-event-manager') ?></option>
                                                </select>
                                            </div>
                                            <div class="misc-pub-section misc-pub-section misc-pub-visibility">
                                                <label for="event_edit_locationname"><?php esc_html_e('Hint Text','ticketmachine-event-manager'); ?></label>
                                                <input type="text" name="rules[badge]" class="fullw-input" value="<?php echo esc_attr($event->rules['badge']); ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="major-publishing-actions">
                                        <div id="publishing-action">
                                            <span class="spinner"></span>
                                            
                                            <?php if(isset($isCommunityEvent)) { ?>
                                                <input type="submit" name="reject" class="button button-secondary button-large" id="publish" value="<?php esc_attr_e('Reject', 'ticketmachine-event-manager') ?>">
                                                <input type="submit" name="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e('Accept', 'ticketmachine-event-manager') ?>">
                                            <?php }else{ ?>
                                                <input type="submit" name="submit" class="button button-primary button-large" id="publish" value="<?php empty($event->id) ? esc_attr_e('Save', 'ticketmachine-event-manager') : esc_attr_e('Update', 'ticketmachine-event-manager') ?>">
                                            <?php } ?>
                                                
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="postimagediv" class="postbox">
                            <h2 class="hndle px-3 py-2 mt-0">
                                <span><?php esc_html_e('Event Image', 'ticketmachine-event-manager') ?></span>
                            </h2>
                            <div class="inside">
                                <div class='image-preview-wrapper'>
                                    <img id='image-preview' src='<?php echo esc_url($event->event_img_url); ?>' width='100' height='100' style='max-height: 500px; width: 100%;'>
                                </div>
                                <input id="upload_image_button" type="button" class="button" style="display:block;width:100%;" value="<?php esc_attr_e( 'Add Image', 'ticketmachine-event-manager' ); ?>" />
                                <input type='hidden' name='event_img_url' id='image_attachment_id' value='<?php echo esc_attr($event->event_img_url); ?>'>
                            </div>
                        </div>
                        <div id="tagsdiv-post_tag" class="postbox">
                            <h2 class="hndle px-3 py-2 mt-0">
                                <span><?php esc_html_e('Tags', 'ticketmachine-event-manager') ?></span>
                            </h2>
                            <div class="inside">
                                <div class="tagsdiv" id="post_tag">
                                    <div class="jaxtag">
                                        <div class="ajaxtag hide-if-no-js">
                                            <label class="screen-reader-text" for="new-tag-post_tag"><?php esc_html_e('Create new tag', 'ticketmachine-event-manager') ?></label>
                                            <input type="text" class="form-control" 
                                                value="<?php foreach((array) $event->tags as $tag) { echo esc_attr($tag).","; }?>" 
                                                name="tags" data-role="tagsinput" >
                                        </div>
                                        <p class="howto" id="new-tag-post_tag-desc"><?php esc_html_e('Seperate tags with comma', 'ticketmachine-event-manager') ?>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-2" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle px-3 py-2 mt-0">
                            <span><?php esc_html_e('Location', 'ticketmachine-event-manager') ?></span>
                        </h2>
                        <div class="inside inside-pad">
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label for="event_edit_locationname"><?php esc_html_e('Event Location', 'ticketmachine-event-manager') ?></label>
                                    <input id="event_location_name" name="ev_location_name" type="text" class="form-control" value="<?php echo esc_attr($event->ev_location_name); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-8 form-group">
                                    <label for="event_edit_strasse"><?php esc_html_e('Street', 'ticketmachine-event-manager') ?></label>
                                    <input id="event_edit_strasse" name="event_location[street]" type="text" class="form-control" value="<?php echo esc_attr($event->event_location['street']); ?>">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label for="house_number"><?php esc_html_e('House No.', 'ticketmachine-event-manager') ?></label>
                                    <input id="event_edit_hausnr" name="event_location[house_number]" type="text" class="form-control" value="<?php echo esc_attr($event->event_location['house_number']); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <label for="event_edit_plz"><?php esc_html_e('Zipcode', 'ticketmachine-event-manager') ?></label>
                                    <input id="event_edit_plz" name="event_location[zip]" type="text" class="form-control" value="<?php echo esc_attr($event->event_location['zip']); ?>">
                                </div>
                                <div class="col-sm-8 form-group">
                                    <label for="event_edit_ort"><?php esc_html_e('City', 'ticketmachine-event-manager') ?></label>
                                    <input id="event_edit_ort" name="event_location[city]" type="text" class="form-control" value="<?php echo esc_attr($event->event_location['city']); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label for="event_edit_land"><?php esc_html_e('Country', 'ticketmachine-event-manager') ?></label>
                                    <input id="event_edit_land" name="event_location[country]" type="text" class="form-control" value="<?php echo esc_attr($event->event_location['country']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                

                    <div class="postbox">
                        <h2 class="hndle px-3 py-2 mt-0">
                            <span><?php esc_html_e('Dates & Times', 'ticketmachine-event-manager') ?></span>
                        </h2>
                        <div class="inside inside-pad">
                            <div>
                                <label><?php esc_html_e('Entry Time', 'ticketmachine-event-manager') ?></label>
                            </div>
                            <div class="row">
                                <div class="input-group col-8">
                                    <input type="text" name="entrytime[date]" class="form-control date entrytime" value="<?php echo esc_attr(ticketmachine_i18n_date("d.m.Y", $event->entrytime)); ?>">
                                </div>
                                <div class="input-group col-4">
                                    <input type="text" name="entrytime[time]" class="form-control time" value="<?php echo esc_attr(ticketmachine_i18n_date("H:i", $event->entrytime)); ?>">
                                </div>
                            </div>
                            <div>
                                <label><?php esc_html_e('Event begins at', 'ticketmachine-event-manager') ?></label>
                            </div>
                            <div class="row">
                                <div class="input-group col-8">
                                    <input type="text" name="ev_date[date]" class="form-control date starttime" value="<?php echo esc_attr(ticketmachine_i18n_date("d.m.Y", $event->ev_date)); ?>">
                                </div>
                                <div class="input-group col-4">
                                    <input type="text" name="ev_date[time]" class="form-control time" value="<?php echo esc_attr(ticketmachine_i18n_date("H:i", $event->ev_date)); ?>">
                                </div>
                            </div>
                            <div>
                                <label><?php esc_html_e('Event ends at', 'ticketmachine-event-manager') ?></label>
                            </div>
                            <div class="row">
                                <div class="input-group col-8">
                                    <input type="text" name="endtime[date]" class="form-control date endtime" value="<?php echo esc_attr(ticketmachine_i18n_date("d.m.Y", $event->endtime)); ?>">
                                </div>
                                <div class="input-group col-4">
                                    <input type="text" name="endtime[time]" class="form-control time" value="<?php echo esc_attr(ticketmachine_i18n_date("H:i", $event->endtime)); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <br/>
                                    <input id="full_day" name="full_day" type="checkbox" value="1" class="regular-text"<?php if(ticketmachine_i18n_date("Ymd", $event->ev_date) == ticketmachine_i18n_date("Ymd", $event->endtime) && ticketmachine_i18n_date("H:i", $event->ev_date) == "00:00" && ticketmachine_i18n_date("H:i", $event->endtime) == "23:59") { echo " checked"; } ?>>
                                    <label for="full_day"><?php echo __("Full-Day Event", "ticketmachine-event-manager"); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="postbox">
                        <h2 class="hndle px-3 py-2 mt-0">
                            <span><?php esc_html_e('Organizer Details', 'ticketmachine-event-manager') ?></span>
                        </h2>
                        <div class="inside inside-pad">
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label for="og_name"><?php esc_html_e('Organizer Name', 'ticketmachine-event-manager') ?></label>
                                    <input id="og_name" name="organizer[og_name]" type="text" class="form-control" value="<?php echo esc_attr($organizer->og_name); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-8 form-group">
                                    <label for="og_street"><?php esc_html_e('Street', 'ticketmachine-event-manager') ?></label>
                                    <input id="og_street" name="organizer[og_street]" type="text" class="form-control" value="<?php echo esc_attr($organizer->og_street); ?>">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label for="og_house_number"><?php esc_html_e('House No.', 'ticketmachine-event-manager') ?></label>
                                    <input id="og_house_number" name="organizer[og_house_number]" type="text" class="form-control" value="<?php echo esc_attr($organizer->og_house_number); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <label for="og_zip"><?php esc_html_e('Zipcode', 'ticketmachine-event-manager') ?></label>
                                    <input id="og_zip" name="organizer[og_zip]" type="text" class="form-control" value="<?php echo esc_attr($organizer->og_zip); ?>">
                                </div>
                                <div class="col-sm-8 form-group">
                                    <label for="og_city"><?php esc_html_e('City', 'ticketmachine-event-manager') ?></label>
                                    <input id="og_city" name="organizer[og_city]" type="text" class="form-control" value="<?php echo esc_attr($organizer->og_city); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label for="og_country"><?php esc_html_e('Country', 'ticketmachine-event-manager') ?></label>
                                    <input id="og_country" name="organizer[og_country]" type="text" class="form-control" value="<?php echo esc_attr($organizer->og_country); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <label for="og_email"><?php esc_html_e('Email address', 'ticketmachine-event-manager') ?></label>
                                    <input id="og_email" name="organizer[og_email]" type="email" class="form-control" value="<?php echo esc_attr($organizer->og_email); ?>">
                                </div>
                                <div class="col-6 form-group">
                                    <label for="og_phone"><?php esc_html_e('Phone number', 'ticketmachine-event-manager') ?></label>
                                    <input id="og_phone" name="organizer[og_phone]" type="text" class="form-control" value="<?php echo esc_attr($organizer->og_phone); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function($) {
        jQuery('input.date').datetimepicker({
            format: 'DD.MM.YYYY',
            locale: '<?php echo get_locale(); ?>'
        });
        jQuery('input.time').datetimepicker({
            format: 'HH:mm',
            locale: '<?php echo get_locale(); ?>',
            stepping: 15
        });
        
        jQuery('.bootstrap-tagsinput').on('keypress', function(e){
            if (e.keyCode == 13){
                e.keyCode = 188;
                e.preventDefault();
            };
        });
    });
</script>


<?php $my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );?>
    
<script type='text/javascript'>
    jQuery( document ).ready( function( $ ) {
        // Uploading files
        var file_frame;
        var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
        var set_to_post_id = <?php echo esc_attr($my_saved_attachment_post_id); ?>; // Set this
        jQuery('#upload_image_button').on('click', function( event ){
            event.preventDefault();
            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                // Set the post ID to what we want
                file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                // Open frame
                file_frame.open();
                return;
            } else {
                // Set the wp.media post id so the uploader grabs the ID we want when initialised
                wp.media.model.settings.post.id = set_to_post_id;
            }
            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select a image to upload',
                button: {
                    text: 'Use this image',
                },
                multiple: false	// Set to true to allow multiple files to be selected
            });
            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();
                // Do something with attachment.id and/or attachment.url here
                jQuery( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
                jQuery( '#image_attachment_id' ).val( attachment.url );
                // Restore the main post ID
                wp.media.model.settings.post.id = wp_media_post_id;
            });
                // Finally, open the modal
                file_frame.open();
        });
        // Restore the main ID when the add media button is pressed
        jQuery( 'a.add_media' ).on( 'click', function() {
            wp.media.model.settings.post.id = wp_media_post_id;
        });
    });
</script>