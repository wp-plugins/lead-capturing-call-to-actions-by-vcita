<?php

/**
 * Render preheader html where needed
 * @since 0.1.0
 */
function ls_render_pre_header(){

    $ls_helpers = new ls_helpers();

    // Check if user has connected his account to vcita
    $is_vcita_connected = ls_is_vcita_connected();

    $settings = ls_get_settings();

	// Only when vcita account is connected
	if ( $is_vcita_connected ){

		// Shortcut to stored vcita params
		$vcita_settings = $settings['vcita_params'];

		$vcita_email = $vcita_settings['email'];
	}

?>
<div class="ls-pre-header">
    <?php if ( $is_vcita_connected ): ?>
        <span>Logged in as: </span>
        <a class="ls-pre-header__email" href="mailto: <?php echo $vcita_email; ?>"> <?php echo $vcita_email; ?></a>
        <span> | </span>
    <?php endif; ?>
    <span><?php _e('Rate us','livesite') ?>: </span>
    <a class="ls-pre-header__rate-link"
       href="https://wordpress.org/support/view/plugin-reviews/contact-form-with-a-meeting-scheduler-by-vcita?filter=5"
       target="_blank">
        <span class="icon-Rate"></span><!--
        --><span class="icon-Rate"></span><!--
        --><span class="icon-Rate"></span><!--
        --><span class="icon-Rate"></span><!--
        --><span class="icon-Rate"></span></a>
    <a href="https://www.vcita.com/home?<?php echo $ls_helpers->get_plugin_identifier(); ?>" target="_blank" class="ls-pre-header__logo"></a>
</div>
<?php } ?>
