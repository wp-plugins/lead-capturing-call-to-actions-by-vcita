<?php
/*
*  livesite_widget
*
*  @description: controller for LiveSite Payments plugin
*  @since: 3.6
*  @created: 25/01/13
*/

class livesite_widget {

	/**
	 * Defines the current module name
	 * @since 0.1.0
	 */
	public $module_name;

	/**
	 * Defines the current module name
	 * @since 0.1.0
	 */
	public $module_slug;

	/**
	 * Sets up initial module data
	 * @since 0.1.0
	 */
	public $module_data;

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

		$this->module_name = 'livesite_widget';

		$this->module_data = ls_get_module_data( $this->module_name );

		$this->module_slug = $this->module_data['slug'];

		$this->ls_helpers = new ls_helpers();

		/* Add Settings Page */
		add_action( 'admin_menu', array($this,'settings_page_setup'), 11, 0 );

		/* Add Meta Box */
		add_action( 'add_meta_boxes', array($this,'top_settings_metabox') );
		add_action( 'add_meta_boxes', array($this,'main_metabox') );
		add_action( 'add_meta_boxes', array($this,'ls_advanced_options') );

	}

	/**
	 * Create Settings Page
	 * @since 0.1.0
	 * @link //codex.wordpress.org/Function_Reference/register_setting
	 * @link //codex.wordpress.org/Function_Reference/add_menu_page
	 * @uses get_setings_page_id()
	 */
	function settings_page_setup(){

		/* Add settings menu page */
		$settings_page = add_submenu_page(
			'live-site',
			__('LiveSite Widget','livesite'),
			__('LiveSite Widget','livesite'),
			'manage_options',
			$this->module_slug,
			array($this,'settings_page')
		);

		/* Vars */
		$page_hook_id = $this->get_setings_page_id();

		/* Do stuff in settings page, such as adding scripts, etc. */
		if ( !empty( $settings_page ) ) {

			/* Load the JavaScript needed for the settings screen. */
			add_action( 'admin_enqueue_scripts', array($this,'enqueue_scripts') );
			add_action( 'admin_enqueue_scripts', array($this,'ls_enqueue_styles') );

			/* Set number of column available. */
			// add_filter( 'screen_layout_columns', 'ls_pm_screen_layout_column', 10, 2 );

		}
	}

	/**
	 * Sanitize Basic Settings
	 * This function is defined in register_setting().
	 * @since 0.1.0
	 */
	function basic_sanitize( $settings  ){
		$settings = sanitize_text_field( $settings );
		return $settings ;
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
	 * Load Script Needed For Meta Box
	 * @since 0.1.0
	 */
	function enqueue_scripts( $current_page_hook ){

		$ls_helpers = $this->ls_helpers;

		$path = $ls_helpers->get_dir( __FILE__ );

		$page_hook_id = $this->get_setings_page_id();

		// Make sure we only load the script and it's params on the current page
		if ( $current_page_hook == $page_hook_id ){
			// register scripts
			$ls_helpers->register_scripts(array(
				'toggle_switch'	=> array(
					'path'	 => $path . '../js/toggles.min.js'
				),
				'livesite_widget'	=> array(
					'path'	 => $path . '../js/livesite-widget.js',
					'deps'   => 'toggle_switch',
					// Register javascript global variables for use with external js files
					'params' => array(
						'ls_lw_module_nonce' => wp_create_nonce( 'module-page' )
					)
				)
			));

			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );
			wp_enqueue_script( 'livesite' );
			wp_enqueue_script( 'toggle_switch' );
			wp_enqueue_script( 'livesite_widget' );
		}
	}

	/**
	 * Load Styles
	 * @since 0.1.0
	 */
	function ls_enqueue_styles( $hook_suffix ){

		$page_hook_id = $this->get_setings_page_id();

		if ( $hook_suffix == $page_hook_id ){

			// Styles are defined on main plugin page so just enqueu using keyword
			wp_enqueue_style( 'livesite' );
			wp_enqueue_style( 'livesite_icon_font' );
			wp_enqueue_style( 'toggle_switch' );
		}

	}


	/**
	 * Settings Page Callback
	 * used in settings_page_setup().
	 * @since 0.1.0
	 */
	function settings_page(){

		/* global vars */
		global $hook_suffix;

		$module_icon = $this->module_data['icon'];
		$module_text = $this->module_data['text'];

		/* utility hook */
		do_action( $this->module_slug . '_settings_page_init' );

		/* enable add_meta_boxes function in this page. */
		do_action( 'add_meta_boxes', $hook_suffix );
		?>

		<div class="wrap">

			<div class="ls-module-page-title">
				<span class="ls-module-page-title__icon <?php echo $module_icon; ?>"></span>
				<div class="ls-module-page-title__title-wrapper">
					<strong class="ls-module-page-title__heading"><?php _e('LiveSite Lead Capturing Widget','livesite'); ?></strong>
					<span class="ls-module-page-title__sub-heading"><?php _e( $module_text,'livesite'); ?></span>
				</div>
			</div>

			<?php settings_errors(); ?>

			<div class="ls-meta-box-wrap">

				<?php settings_fields( $this->module_slug ); // options group  ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div id="poststuff">

					<div id="post-body" class="metabox-holder columns-1">

						<div id="postbox-container-2" class="postbox-container">

							<?php do_meta_boxes( $hook_suffix, 'normal', null ); ?>
							<!-- #normal-sortables -->

							<?php do_meta_boxes( $hook_suffix, 'advanced', null ); ?>
							<!-- #advanced-sortables -->

						</div><!-- #postbox-container-2 -->

					</div><!-- #post-body -->

					<br class="clear">

				</div><!-- #poststuff -->

			</div><!-- .ls-meta-box-wrap -->

			<?php ls_render_footer(); ?>

		</div><!-- .wrap -->

	<?php
		// Render the sidebar
		ls_render_sidebar_html();
	}

	/**
	 * Payment Settings meta box
	 * @since 0.1.0
	 * @link //codex.wordpress.org/Function_Reference/add_meta_box
	 */
	function top_settings_metabox(){

		$page_hook_id = $this->get_setings_page_id();

		add_meta_box(
			'payment_settings_mb',                  	/* Meta Box ID */
			__('Lead Capturing Settings', 'livesite'),           /* Title */
			array($this,'top_settings_metabox_markup'), /* Function Callback */
			$page_hook_id,               				/* Screen: Our Settings Page */
			'normal',                 					/* Context */
			'default'                 					/* Priority */
		);
	}

	/**
	 * Payment Settings meta box markup
	 * @since 0.1.0
	 */
	function top_settings_metabox_markup(){

		$ls_helpers = $this->ls_helpers;

		$vcita_params = ls_get_vcita_params();

		$user_email = $vcita_params['email'];

		$show_livesite_widget = $this->module_data['show_livesite'];

	?>

		<table class="form-table">
			<tr>
				<td> <?php _e('Show LiveSite Widget on every page','livesite'); ?>:</td>
				<td>
					<div class="ls-toggle-switch toggle-light js-ls-toggle-switch" data-toggle-on="<?php echo $show_livesite_widget ? 'true' : 'false'; ?>"></div>
				</td>
			</tr>
			<tr>
				<td> <?php _e('New leads and contact requests will be sent to','livesite'); ?>:</td>
				<td>
					<?php echo $user_email; ?>
					<br>
					<a data-open-popup href="<?php echo $ls_helpers->get_settings_page_url('business'); ?>"> <span>+</span> <?php _e('Add additional emails','livesite'); ?></a>
				</td>
			</tr>
			<tr>
				<td> <?php _e('Mobile notifications','livesite'); ?>:</td>
				<td>
					<a data-open-popup href="<?php echo $ls_helpers->get_settings_page_url('messages'); ?>"> <?php _e("Set automated mobile (SMS) notification",'livesite'); ?></a>
				</td>
			</tr>
		</table>

	<?php
	}

	/**
	 * Payment Form and Button meta box
	 * @since 0.1.0
	 * @link //codex.wordpress.org/Function_Reference/add_meta_box
	 */
	function main_metabox(){

		$page_hook_id = $this->get_setings_page_id();

		add_meta_box(
			'main_metabox',                  					/* Meta Box ID */
			__('LiveSite Widget Customization', 'livesite'), 	/* Title */
			array($this,'main_metabox_markup'),  				/* Function Callback */
			$page_hook_id,               						/* Screen: Our Settings Page */
			'normal',                 							/* Context */
			'default'                 							/* Priority */
		);
	}

	/**
	 * Payment Settings meta box markup
	 * @since 0.1.0
	 */
	function main_metabox_markup(){

		$vcita_params = ls_get_vcita_params();

		$implementation_key = $vcita_params['implementation_key'];
	?>

	<iframe class="js-iframe"
			src="//www.vcita.com/widget_implementations?platform=wordpress&widget=active_engage&key=<?php echo $implementation_key; ?>"
			width="980"
			height="100%"></iframe>


	<table class="form-table">
		<tr>
			<td>
				<?php _e('**Use LiveSite SDK to add any LiveSite action to any element on your site','livesite'); ?>
				-
				<a href="//developers.vcita.com/"><?php _e('Access SDK documentation','livesite'); ?></a>
			</td>
		</tr>
	</table>

	<?php
	}

	/**
	 * Payment Settings meta box
	 * @since 0.1.0
	 * @link //codex.wordpress.org/Function_Reference/add_meta_box
	 */
	function ls_advanced_options(){

		$page_hook_id = $this->get_setings_page_id();

		add_meta_box(
			'ls_advanced_options',                  	/* Meta Box ID */
			__('Advanced Options', 'livesite'),         /* Title */
			array($this,'ls_advanced_options_markup'),  /* Function Callback */
			$page_hook_id,               				/* Screen: Our Settings Page */
			'normal',                 					/* Context */
			'default'                 					/* Priority */
		);
	}

	/**
	 * Payment Settings meta box markup
	 * @since 0.1.0
	 */
	function ls_advanced_options_markup(){

		$ls_helpers = $this->ls_helpers;

	?>

		<ul class="ul-disc">
			<li> <?php _e('Remove all vCita promotions','livesite'); ?> </li>
			<li> <?php _e('Keep a complete communication history','livesite'); ?> </li>
			<li> <?php _e('Add your logo and customize the entire livesite color theme and branding','livesite'); ?> </li>
		</ul>

		<a href="https://www.vcita.com/account/upgrade?<?php echo $ls_helpers->get_plugin_identifier(); ?>_livesite_widget" target="_blank" class="button button-primary"> <?php _e('Try vCita Permium','livesite'); ?></a>

	<?php
	}

}

new livesite_widget();

?>
