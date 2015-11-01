<?php
/*
*  livesite_parse_callback
*
*  @description: Parses the return callback once the user logged in to vCita
*  @since: 3.6
*  @created: 25/01/13
*/

class livesite_parse_callback
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
        add_action( 'admin_menu', array($this, 'add_parse_vcita_callback_page'), 20 );

    }

    /**
     * Adds a hidden page to allow reseting the plugin (mainly used for degbugging but not exclusive)
     * @since 0.1.0
     */
    function add_parse_vcita_callback_page(){
        add_submenu_page(
            null,
            __('', 'livesite'),
            __('', 'livesite'),
            'edit_posts',
            'live-site-parse-vcita-callback',
            array($this, 'ls_parse_vcita_callback')
        );
    }

    /**
     * Parses the return values from vcita connection
     * @since 0.1.0
     */
    function ls_parse_vcita_callback(){

    	$success = $_GET['success'];
    	$uid = $_GET['uid'];
    	$first_name = $_GET['first_name'];
    	$last_name = $_GET['last_name'];
    	$title = $_GET['title'];
    	$confirmation_token = $_GET['confirmation_token'];
    	$confirmed = $_GET['confirmed'];
    	$engage_delay = $_GET['engage_delay'];
    	$implementation_key = $_GET['implementation_key'];
    	$email = $_GET['email'];

		ls_set_settings( array(
            'vcita_connected' => true,
            'vcita_params' => array(
              'success'              => $success,
            	'uid'                  => $uid,
            	'first_name'           => $first_name,
            	'last_name'            => $last_name,
            	'title'                => $title,
            	'confirmation_token'   => $confirmation_token,
            	'confirmed'            => $confirmed,
            	'engage_delay'         => $engage_delay,
            	'implementation_key'   => $implementation_key,
            	'email'                => $email
            )
        ));

        $ls_helpers = $this->ls_helpers;

		// Replace curly brace tags inside of html code
		// $ls_helpers->ls_replace_default_tags();

    	$redirect_url = $ls_helpers->get_plugin_path();

    ?>
    <script type="text/javascript">
        window.location = "<?php echo $redirect_url; ?>";
    </script>
    <?php }

}

new livesite_parse_callback();

?>
