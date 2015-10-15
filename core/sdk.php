<?php

class ls_sdk {

	/**
	 * Sets up helpers as global
	 * @since 0.1.1
	 */
	public $ls_helpers;

	function __construct(){

		$this->ls_helpers = new ls_helpers();

		// Load sdk when vcita is connected on front end and not in the admin area
		if ( ! is_admin() && ls_is_vcita_connected() ){

			add_action( 'wp_head', array($this, 'enqueue_front_end_script') );
			add_action( 'wp_enqueue_scripts', array($this, 'init_register_scripts') );
		}

	}

	/**
	 * Load front facing stylesheet
	 * @since 0.1.0
	 */
	function init_register_scripts(){

		$ls_helpers = $this->ls_helpers;

		$path = $ls_helpers->get_dir( __FILE__ );

		$vcita_params = ls_get_vcita_params();
		$livesite_widget_module = ls_get_module_data('livesite_widget');

		$ls_helpers->register_scripts(array(
			'livesite_sdk'	=> array(
				'path'	 => $path . '../js/livesite-include-sdk.js',
				'params' => array(
					'ls_sdk_uid' => $vcita_params['uid'],
					'ls_sdk_show_livesite' => $livesite_widget_module['show_livesite']
				)
			)
		));

	}
	/**
	 * Load front facing stylesheet
	 * @since 0.1.0
	 */
	function enqueue_front_end_script(){

		wp_enqueue_script( 'livesite_sdk' );

	}

}

new ls_sdk();

?>
