<?php
/*
*  livesite_settings_page
*
*  @description: Parses the return callback once the user logged in to vCita
*  @since: 3.6
*  @created: 25/01/13
*/

class livesite_settings_page
{

	/**
	 * Defines the current module url slug
	 * @since 0.1.0
	 */
	public $module_slug;

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

		$this->module_slug = 'live-site-settings';

		$this->ls_helpers = new ls_helpers();

        // Uses priority 20 to laod after plugin init
        add_action( 'admin_menu', array($this, 'add_settings_page'), 20 );

    }

    /**
     * Adds a hidden page to allow reseting the plugin (mainly used for degbugging but not exclusive)
     * @since 0.1.0
     */
    function add_settings_page(){
        add_submenu_page(
            'live-site',
            __('Settings', 'livesite'),
            __('Settings', 'livesite'),
            'edit_posts',
            $this->module_slug,
            array($this, 'settings_page_html')
        );

		add_action( 'admin_enqueue_scripts', array($this,'enqueue_styles') );
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_scripts') );
    }

	/**
	 * Load Styles
	 * @since 0.1.0
	 */
	function enqueue_styles( $hook_suffix ){

		$page_hook_id = $this->get_setings_page_id();

		if ( $hook_suffix == $page_hook_id ){

			// Styles are defined on main plugin page so just enqueu using keyword
			wp_enqueue_style( 'livesite' );
			wp_enqueue_style( 'livesite_icon_font' );
		}

	}

	/**
	 * Load Scripts
	 * @since 0.1.3
	 */
	function enqueue_scripts( $hook_suffix ){

		$page_hook_id = $this->get_setings_page_id();

		if ( $hook_suffix == $page_hook_id ){

			// Styles are defined on main plugin page so just enqueu using keyword
			wp_enqueue_script( 'livesite' );

		}

	}

	/**
	 * Utility: Page Hook
	 * The Settings Page Hook, it's the same with global $hook_suffix.
	 * @since 0.1.0
	 */
	function get_setings_page_id(){
		return 'livesite_page_' . $this->module_slug;
	}


    /**
     * Parses the return values from vcita connection
     * @since 0.1.0
     */
    function settings_page_html(){

		$ls_helpers = $this->ls_helpers;

		// Get subpage parameter from url
		$sub_page = isset( $_GET['subpage'] ) ? $_GET['subpage'] : false;

		if ( $sub_page )
			$settings_url = esc_url( 'https://www.vcita.com/settings/' . $sub_page );
		else
			$settings_url = 'https://www.vcita.com/settings';
    ?>

	<div class="wrap">

		<div class="ls-module-page-title">
			<strong class="ls-module-page-title__heading"><?php _e('LiveSite Settings page','livesite'); ?></strong>
		</div>

		<div class="ls-meta-box-wrap">

			<iframe class="js-iframe ls-iframe"
					src="<?php echo $settings_url; ?>"
					width="980"
					height="1040"></iframe>

		</div><!-- .ls-pm-settings-meta-box-wrap -->

		<?php ls_render_footer(); ?>

	</div><!-- .wrap -->

	<?php // Render the sidebar
		  ls_render_sidebar_html(); ?>

    <?php }
}

new livesite_settings_page();

?>
