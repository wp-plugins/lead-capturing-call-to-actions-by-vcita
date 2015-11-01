<?php

/**
 * Render footer html where needed
 * @since 0.1.0
 */
function ls_render_footer(){

    $ls_helpers = new ls_helpers();

    // Check if user has connected his account to vcita
    $is_vcita_connected = ls_is_vcita_connected();

?>
<div class="ls-footer">
    <ul class="ls-footer-menu">
        <?php if ( $is_vcita_connected ): ?>
        <li class="ls-footer-menu__item">
            <a href="//www.vcita.com/account?<?php echo $ls_helpers->get_plugin_identifier(); ?>" target="_blank" class="ls-footer-menu__link">Account</a>
        </li>
        <?php endif; ?>
        <li class="ls-footer-menu__item">
            <a data-open-popup href="<?php echo $ls_helpers->get_settings_page_url('business'); ?>" class="ls-footer-menu__link">Settings</a>
        </li>
        <?php if ( $is_vcita_connected ): ?>
        <li class="ls-footer-menu__item">
            <a href="?page=live-site-reset-plugin" class="ls-footer-menu__link js-vcita-disconnect">Disconnect</a>
        </li>
        <?php endif; ?>
        <li class="ls-footer-menu__item">
            <a href="https://wordpress.org/support/view/plugin-reviews/lead-capturing-call-to-actions-by-vcita?filter=5" target="_blank" class="ls-footer-menu__link">Rate US</a>
        </li>
        <li class="ls-footer-menu__item">
            <a href="https://support.vcita.com/home" target="_blank" class="ls-footer-menu__link">Support</a>
        </li>
    </ul>
</div>
<?php } ?>
