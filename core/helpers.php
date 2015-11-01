<?php

class ls_helpers {

	function __construct(){}

	/**
	 * Utility: Helper
	 * Returns the current operatings system
	 * @since 0.1.0
	 */
	function get_os( $file = __FILE__ ){
		return ((strpos(strtolower(PHP_OS), 'win') === 0) || (strpos(strtolower(PHP_OS), 'cygwin') !== false)) ? 'win32' : 'unix';
	}

	/**
	 * Utility: Helper
	 * Returns the path for the current directory
	 * @since 0.1.0
	 */
	function get_path( $file = __FILE__ ){
		return trailingslashit(dirname( $file ));
	}

	/**
	 * Utility: Helper
	 * Returns the plugin version
	 * @since 0.1.0
	 */
	function get_plugin_version(){

		$plugin_version = '0.1.3';

		return $plugin_version;
	}

	/**
	 * Utility: Helper
	 * Returns the directory for the given file
	 * @since 0.1.0
	 */
	function get_dir( $file = __FILE__ ){

	    $dir = trailingslashit(dirname($file));
	    $count = 0;


	    // sanitize for Win32 installs
	    $dir = str_replace('\\' ,'/', $dir);


	    // if file is in plugins folder
	    $wp_plugin_dir = str_replace('\\' ,'/', WP_PLUGIN_DIR);
	    $dir = str_replace($wp_plugin_dir, plugins_url(), $dir, $count);


	    if( $count < 1 )
	    {
	        // if file is in wp-content folder
	        $wp_content_dir = str_replace('\\' ,'/', WP_CONTENT_DIR);
	        $dir = str_replace($wp_content_dir, content_url(), $dir, $count);
	    }


	    if( $count < 1 )
	    {
	        // if file is in ??? folder
	        $wp_dir = str_replace('\\' ,'/', ABSPATH);
	        $dir = str_replace($wp_dir, site_url('/'), $dir);
	    }


	    return $dir;
	}

	/**
	 * Utility: Helper
	 * Checks if the array is empty
	 *
	 * @param: array()
	 * @return: Boolean
	 * @since 0.1.0
	 */
	function is_array_empty( $arr ){

		$arr = array_filter( $arr );

		$empty = empty( $arr );

		return $empty;
	}

	/**
	 * Utility: Helper
	 * Shows php values in web console
	 *
	 * @param: array()
	 * @return: Boolean
	 * @since 0.1.0
	 */
	function console_log( $data ){

	    echo '<script>';
	    echo 'console.log('. json_encode( $data ) .')';
	    echo '</script>';

	}

	/**
	 * Utility: Helper
	 * Returns the plugin path for use with other pages
	 *
	 * @param: array()
	 * @return: Boolean
	 * @since 0.1.0
	 */
	function get_plugin_path(){
		return get_admin_url('', '', 'admin').'admin.php?page=live-site';
	}

	/**
	 * Utility: Helper
	 * Adds a page to wordpress
	 *
	 * @param: $title{String}, $content{String}
	 * @return: Int
	 * @since 0.1.0
	 */
	function add_wp_page( $title, $page_content, $unsafe_html = false ){

		if ( $title != '' && $page_content != '' ){

			$user_ID = get_current_user_id();

			// if ( $unsafe_html )
			// 	$content = wp_strip_all_tags( $page_content );
			// else
				$content = $page_content;

			$new_post_settings = array(
				'post_title' => wp_strip_all_tags( $title ),
				'post_content' => $content,
				'post_status' => 'publish',
				'post_date' => date('Y-m-d H:i:s'),
				'post_author' => $user_ID,
				'post_type' => 'page',
				'post_category' => array(0)
			);

			$post_id = false;

			$post_id = wp_insert_post( $new_post_settings );

			return $post_id;

		}

		return false;

	}

	/**
	 * Utility: Helper
	 * Registers a number of javascript files through wordpress
	 * so that they can be used with `wp_enqueue_script`
	 *
	 * @param: array()
	 * @return: Boolean
	 * @since 0.1.0
	 */
	function register_scripts( $scripts ){

		$version = $this->get_plugin_version();

		if ( ! $this->is_array_empty( $scripts ) ){

			$dependancy = isset($scripts['deps']) ? $scripts['deps'] : false;

			foreach( $scripts as $key => $val ) {
				wp_register_script( $key, $val['path'], $dependancy, $version );

				// Pass php variables to javascript (creates javscript array of params)
				if ( isset( $val['params'] ) )
					wp_localize_script(
						$key,
						'ls_PHPVAR_' . $key,
						$val['params'],
						$version,
						true
					);
			}
		}

	}

	/**
	 * Utility: Helper
	 * Replaces double curly braces {{ }} tokens with a set of values in a string
	 *
	 * @param: $string{String}, $replacements{Array}
	 * @return: String
	 * @since 0.1.0
	 */
	function tag_replace ( $string, $replacements ){

		$keys = array_map(function($key){ return '{{' . $key . '}}'; }, array_keys($replacements));

		return str_replace($keys, $replacements, $string);

	}

	/**
	 * Utility: Helper
	 * Get custom page url
	 *
	 * @param: $module_name{String},
	 * @return: String
	 * @since 0.1.0
	 */
	function get_custom_page_url ( $module_name = false ){

		$custom_page_url = false;

		if ( $module_name ){

			$module_data = ls_get_module_data( $module_name );

			// Get custom page id
			$custom_page_id = $module_data['custom_page_id'];

			// Setup edit url if custom page id exists
			if ( $custom_page_id )
				$custom_page_url = get_admin_url('', '', 'admin') . 'post.php?action=edit&post=' . $custom_page_id;

		}

		return $custom_page_url;

	}

	/**
	 * Utility: Helper
	 * Get plugin page url
	 *
	 * @param: $module_name{String},
	 * @return: String
	 * @since 0.1.0
	 */
	function get_plugin_page_url ( $page_name = '' ){

		$custom_page_url = $page_name != '' ? get_admin_url() . 'admin.php?page=' . $page_name : false;

		return $custom_page_url;

	}

	/**
	 * Utility: Helper
	 * Get the settings page url.
	 * If a sub page name is provided we add it as a query string parameter
	 *
	 * @param: $sub_page_name{String},
	 * @return: String
	 * @since 0.1.0
	 */
	function get_settings_page_url ( $sub_page_name = '' ){

		// Default settings page url
		$settings_page_url = 'https://www.vcita.com/settings';

		if ( $sub_page_name != '' ){
			$settings_page_url .= '/' . $sub_page_name;
		}

		//$settings_page_url = get_admin_url() . 'admin.php?page=live-site-settings';

		// Only if sub page is provided
		// if ( $sub_page_name != '' ){
		//
		// 	$custom_page_url = $settings_page_url;
		//
		// 	// Add the sub page as a query string parameter for the iframe on the settings page
		// 	$settings_page_url = esc_url(
		// 		add_query_arg(
		// 			array( 'subpage' => $sub_page_name ),
		// 			$custom_page_url
		// 		)
		// 	);
		//
		// }

		return $settings_page_url;

	}

	/**
	 * Utility: Helper
	 * Create a custom page for the given module once per plugin init
	 *
	 * @param: $module_name{String},
	 * @return: String
	 * @since 0.1.0
	 */
	function generate_custom_page_once ( $module_name = false ){

		if ( $module_name ){

			$module_data = ls_get_module_data( $module_name );

			// Get custom page id returns false if does not exist
			$custom_page_id = $module_data['custom_page_id'];
			$custom_page_previously_created = $module_data['custom_page_previously_created'];

			// If custom page id does not exist and was not previously created
			if ( ! $custom_page_id && ! $custom_page_previously_created ){

				// Get random content method knows how to handle single string and array
  			$custom_page_content = $this->get_random_page_content( $module_data['custom_page_content'] );

				// Add the modules custom page
        $page_id = $this->add_wp_page( $module_data['custom_page_title'], $custom_page_content );

				// Only if page was added define the new page id in the plugins settings
        if ( $page_id ){

					ls_set_settings( array(
          	'modules' => array(
						  $module_name => array(
                'custom_page_id' => $page_id,
					   		'custom_page_previously_created' => true
              )
            )
          ));

				}

			}

			return $custom_page_previously_created;

		}

	}

	/**
	 * Settings
	 * Replaces double curly braces with vcita data in modules
	 * @param: $vcita_data{Array}
	 * @since 0.1.0
	 */

	 function ls_replace_default_tags(){

	     $modules = ls_get_modules();

	     $settings = ls_get_settings();

	//     $ls_helpers->console_log( $settings );

	     // Run over all modules
	     foreach ( $modules as $module_name => $module ){

	         if ( isset( $module['custom_page_content'] ) ) {

	            // Replace data inside of custom page content
	            $custom_page_content_parsed = $this->tag_replace( $module['custom_page_content'], $settings['vcita_params'] );

	            // TODO: Fix this. For some reason ls_set_settings overrides the vcita_params and does not merge the arrays correctly

	            // Set module as active based on array key name
	            ls_set_module_data( $module_name, array( 'custom_page_content' => $custom_page_content_parsed ) );

	//            $ls_helpers->console_log( $ );

	        }

	     }

	    //  $ls_helpers->console_log( $modules );

	 }

	/**
	 * Settings
	 * Parse the HTTP response and return the data and if was successful or not.
	 * @param: url{String}
	 * @since 0.1.0
	 */
	function parse_response($response) {
	    $success = false;
	    $raw_data = "Unknown error";

	    if (is_wp_error($response)) {
	        $raw_data = $response->get_error_message();

	    } elseif (!empty($response['response'])) {
	        if ($response['response']['code'] != 200) {
	            $raw_data = $response['response']['message'];
	        } else {
	            $success = true;
	            $raw_data = $response['body'];
	        }
	    }

	    return compact('raw_data', 'success');
	}

	/**
	 * Settings
	 * Perform an HTTP GET Call to retrieve the data for the required content.
	 * @param: url{String}
	 * @since 0.1.0
	 */
	 function get_contents( $url ){

		$response = wp_remote_get( $url,
			array(
				'header' => array(
					'Accept' => 'application/json; charset=utf-8'
			),
			'timeout' => 10
		));

	    return $this->vcita_parse_response( $response );

	 }

	/**
	 * Settings
	 * Perform an HTTP POST Call to retrieve the data for the required content.
	 * @param: url{String}
	 * @since 0.1.0
	 */
	function vcita_post_contents( $url ) {
	    $response  = wp_remote_post( $url,
			array(
				'header' => array(
					'Accept' => 'application/json; charset=utf-8'
			),
            'timeout' => 10)
		);

	    return $this->vcita_parse_response( $response );
	}

	/**
	 * Settings
	 * Return the plugin identifier for analytics
	 * @since 3.0.2
	 */
	function get_plugin_identifier() {
	    return 'o=WP-v-ae';
	}

	/**
	 * Settings
	 * Return the old plugin db key for upgrades
	 * @since 3.0.2
	 */
	function get_old_plugin_db_key() {
	    return 'vcita_scheduler';
	}

	/**
	 * Settings
	 * Return a random item from an array
 	 * @param: page_content_list{String|Array}
	 * @since 3.0.2
	 */
	function get_random_page_content( $page_content_list ) {

		// Initially the list is just one string
		$random_sentence = $page_content_list;

		// If the list is an array
		if ( is_array( $page_content_list ) ){

			// Get a random number up to the length of the custom sentences
			$random_sentence_number = mt_rand( 0, count($page_content_list) - 1 );

			$random_sentence = $page_content_list[ $random_sentence_number ];

		}

		return $random_sentence;

	}

}

?>
