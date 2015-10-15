<?php
/*
Plugin Name: Lead Capturing Call-To-Actions by vCita
Plugin URI: http://www.vcita.com
Description: vCita Lead Capturing Call-To-Actions plugin for WordPress helps you capture twice as many leads!
Version: 3.0.1
Author: vCita.com
Author URI: http://www.vcita.com
*/

/*
*  livesite_main_lw
*
*  @description: controller for main init of LiveSite Plugin
*  @since: 2.0.0
*  @created: 01/10/15
*/

class livesite_main_lw {

    /**
     * Defines the plugin settings for the init instance
     * Is only used in this class for settings relaying
     * @since 0.1.0
     */
    public $old_plugin_settings;

    function __construct() {

        // Low level get option
        $this->old_plugin_settings = get_option( 'livesite_plugin_settings', false );
        $run_plugin = true;

        // If this is not a fresh install
        if ( $this->old_plugin_settings ){

        	// Check if this plugin is not the active one
        	if ( $this->old_plugin_settings['main_module'] != 'livesite_widget' ){

        		add_action('admin_notices', array($this,'other_plugin_installed'));
        		add_action('admin_init', array($this,'deactivate_plugin'));

        		$run_plugin = false;

        	}

        }

        if ( $run_plugin ){
            require_once( __DIR__ . '/plugin_init.php' );

            new ls_plugin_init();
        }


    }

    // Shows message that another plugin is installed
    function other_plugin_installed() {
        $settings = $this->old_plugin_settings;
        $main_module = $settings['main_module'];
        $module_title = $settings['modules'][$main_module]['title'];
        ?>
    	<div id="message" class="error notice is-dismissible">
    		<p>
    			<?php _e('vCita LiveSite Pack is already installed. Please use <a href="'. get_admin_url('', '', 'admin').'plugins.php' .'">'. $module_title .'</a>','livesite'); ?>
    		</p>
    	</div>
    <?php
    }

    function deactivate_plugin() {
    	deactivate_plugins( plugin_basename( __FILE__ ) );
    }

}

new livesite_main_lw();
?>
