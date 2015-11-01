<?php

/**
 * Render sidebar html where needed
 * @since 0.1.0
 */
function ls_render_sidebar_html(){

    $ls_helpers = new ls_helpers();

    $modules = ls_get_modules();

?>
<div class="ls-module-wrapper">
    <ul class="ls-modules-slim">
        <?php foreach ( $modules as $module_key => $module ):
        ?><li class="ls-modules-slim__rack <?php echo $module['active'] ? 'ls-modules-slim__rack--active js-ls-modules-slim__rack--active' : 'js-ls-modules-slim__rack--disabled'; ?>">
            <div class="ls-modules-slim__module-button">
                <span class="ls-modules-slim__module-icon <?php echo $module['icon']; ?>"></span>
                <span class="ls-modules-slim__module-title"><?php echo $module['title']; ?></span>
            </div>
            <?php
                if ( $module['active'] )
                    $data_attribute = 'data-href="' . $ls_helpers->get_plugin_page_url( $module['slug'] ) . '"';
                else
                    $data_attribute = 'data-module-name="' . $module_key . '"';
            ?>
            <div class="ls-modules-slim__module js-ls-modules-slim__module" <?php echo $data_attribute; ?>>
                <div class="ls-modules-slim__activate">
                    <div class="ls-module-slim__activate__text">
                        <?php
                            if ( ! $module['active'] )
                                echo $module['module_tray_text']['disabled'];
                            else
                                echo $module['module_tray_text']['active'];
                        ?>
                    </div>
                </div>
            </div>
        </li><?php
        endforeach; ?>
        <li class="ls-modules-slim__rack ls-modules-slim__rack--extras">
            <a href="<?php echo $ls_helpers->get_plugin_page_url('live-site-backoffice'); ?>" class="ls-modules-slim__module-button">
                <span class="ls-modules-slim__module-icon icon-Optimization"></span>
                <span class="ls-modules-slim__module-title"><?php _e('Back Office','livesite'); ?></span>
            </a>
        </li>
        <li class="ls-modules-slim__rack ls-modules-slim__rack--extras">
            <a href="//developers.vcita.com" target="_blank" class="ls-modules-slim__module-button">
                <span class="ls-modules-slim__module-icon icon-Code-Window"></span>
                <span class="ls-modules-slim__module-title"><?php _e('SDK','livesite'); ?></span>
            </a>
        </li>
    </ul>
</div>
<?php } ?>
