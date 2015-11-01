<?php
/*
*  reset_plugin
*
*  @description: Creates a hidden page that resets the plugins data
*  @since: 3.6
*  @created: 25/01/13
*/

class reset_plugin
{

	/**
	 * Sets up helpers as global
	 * @since 0.1.1
	 */
	public $ls_helpers;

	/*
	*  __construct
	*
	*  @description:
	*  @since 3.1.8
	*  @created: 23/06/12
	*/

	function __construct(){

		$this->ls_helpers = new ls_helpers();

        // Uses priority 20 to laod after plugin init
        add_action( 'admin_menu', array($this, 'add_reset_plugin_page'), 20 );

    }

    /**
     * Adds a hidden page to allow reseting the plugin (mainly used for degbugging but not exclusive)
     * @since 0.1.0
     */
    function add_reset_plugin_page(){
        add_submenu_page(
            null,
            __('', 'livesite'),
            __('', 'livesite'),
            'edit_posts',
            'live-site-reset-plugin',
            array($this, 'reset_plugin')
        );
    }

	/**
     * Remove custom plugin pages from wordpress
     * @since 0.1.0
     */
	function remove_custom_pages(){

		$modules = ls_get_modules();

		// Run over all modules and start them up
		foreach ( $modules as $module ){

			$page_id = $module['custom_page_id'];

			// If custom page has been created delete page permanantely and skip the trash
			if ( $page_id )
	        	wp_delete_post( $page_id, true );

		}

	}

    /**
     * Remove options from db and cause plugin to fallback to defaults when it restarts
     * @since 0.1.0
     */
    function reset_plugin(){

			$ls_helpers = $this->ls_helpers;

			$this->remove_custom_pages();

      // Remove plugin settings from db
      delete_option( 'livesite_plugin_settings' );

      $redirect_url = $ls_helpers->get_plugin_path();

    ?>
    <script type="text/javascript">
        window.location = "<?php echo $redirect_url; ?>";
    </script>
    <?php }

}

new reset_plugin();

?>
