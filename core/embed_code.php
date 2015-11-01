<?php

class livesite_embed_code {

	function __construct(){}

	/**
	 * Perform an HTTP GET Call to retrieve the data for the required content.
	 *
	 * @param $url
	 * @return array - raw_data and a success flag
	 */
	function get_contents($url) {
	    $response = wp_remote_get($url,
			array(
				'header' => array('Accept' => 'application/json; charset=utf-8'),
	        	'timeout' => 10
			)
		);

	    return $this->parse_response($response);
	}

	/**
	 * Parse the HTTP response and return the data and if was successful or not.
	 */
	function parse_response($response) {
	    $success = false;
	    $raw_data = 'Unknown Error';

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

			$return = array( 'success' => $success, 'raw_data' => $raw_data );

	    return $return;
	}


	/**
	 * Settings
	 * Create the The iframe HTML Tag according to the given paramters
	 * @param: $type{String}, $uid{String}, $width{String}, $height{String}
	 * @since 0.1.0
	 */
	// create_embed_code( 'contact', $helpers->uid, '100%', '450px' )
	function create_embed_code( $type, $uid, $width, $height ) {

	    // Only present if UID is available
	    if ( isset($uid) && !empty($uid) ) {

			$transient_key = 'livesite_embed_code' . $type . $uid . $width . $height;

			$code = get_transient( $transient_key );

			// Load embed code from the cache if possible
			if ( ! $code ) {

				$response = $this->get_contents(
					"https://www.vcita.com/api/experts/" . urlencode($uid) . "/embed_code?type=" . $type . "&width=" . urlencode($width) . "&height=" . urlencode($height)
				);

				$data = json_decode($response['raw_data']);

				if ($response['success']) {

					$code = $data->code;

					// Set the embed code to be cached for an hour
					set_transient( $transient_key, $code, HOUR_IN_SECONDS);

				} else {

					$code = '<iframe frameborder="0" src="https://www.vcita.com/' . urlencode($uid) . '/' . $type . '/" width="'. $width .'" height="'. $height .'"></iframe>';

				}
			}
	    }

		return $code;

	}

}

new livesite_embed_code();

?>
