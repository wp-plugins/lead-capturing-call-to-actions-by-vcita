<?php

/**
 * Fetch modules
 * @since 0.1.0
 */
function ls_get_main_module(){

    $settings = ls_get_settings();

    return isset( $settings['main_module'] ) ? $settings['main_module'] : false;

}

/**
 * Fetch modules
 * @since 0.1.0
 */
function ls_get_modules(){

    $settings = ls_get_settings();

    return $settings['modules'];

}

/**
 * Fetch vcita user data first_name, uid, etc.
 * @since 0.1.0
 */
function ls_get_vcita_params(){

    $settings = ls_get_settings();

    return isset( $settings['vcita_params'] ) ? $settings['vcita_params'] : false;

}

/**
 * Fetch single module data
 * @since 0.1.0
 */
function ls_get_module_data( $module_name ){

    if ( isset( $module_name ) ){

        $modules = ls_get_modules();

        return $modules[ $module_name ];

    }

    return false;

}


/**
 * Setup default settings when starting plugin
 * @since 0.1.0
 */
function ls_init_default_settings(){

        ls_set_settings(array(
            // Defines the initial module when plugin starts up
            'main_module' => 'livesite_widget',
            // This is setup if the plugin has been upgraded
            // So we know not to auto install module pages
            'plugin_upgraded' => false,
            // Specifies the paths for each modules code and if it's active or not
            'modules' => array(
                'form_builder' => array(
                    'active' => true,
                    'path'   => 'modules'. LS_SLASH .'form_builder.php',
                    'icon' => 'icon-File-Edit',
            		'title' => 'Contact Form',
                    'main_title' => 'Contact Form',
            		'text' => 'Create beautiful forms using a simple Drag &amp; Drop editor.',
                    'slug' => 'ls_cf',
                    'custom_page_id' => false,
                    'custom_page_title' => 'Contact Us',
                    'custom_page_content' => array(
                      '<strong>Fill in the form below and we will get in touch as soon as we can.</strong> [livesite-contact title="Contact request"]',
                      '<strong>We are always interested to hear from anyone who wishes to get in touch with us. Please fill up the contact form below and we\'ll get back to you soon. A confirmation email will be sent to you once the message was sent.</strong> [livesite-contact title="Contact request"]',
                      '<strong>Feel free to contact our team with your inquiries, by using the contact management software form below:</strong> [livesite-contact title="Contact request"]',
                      '<strong>Please complete the contact form below to schedule time with our team.</strong> [livesite-contact title="Contact request"]'
                    ),
                    'custom_page_previously_created' => false,
                    'module_tray_text' => array(
                        'active' => 'Customize your contact form',
                        'disabled' => 'Add a contact form to your site'
                    )
                ),
                'livesite_widget' => array(
                    'active' => true,
                    'path'   => 'modules'. LS_SLASH .'livesite_widget.php',
                    'icon' => 'icon-Livesite',
            		'title' => 'Livesite Widget',
                    'main_title' => 'Lead Capturing Contact Form',
            		'text' => 'Encourage clients to take actions and capture twice as many leads',
                    'slug' => 'ls_lw',
                    'custom_page_id' => false,
                    'show_livesite' => true,
                    'module_tray_text' => array(
                        'active' => 'Customize your lead capturing widget',
                        'disabled' => ''
                    )
                ),
                'payments' => array(
                    'active' => false,
                    'path'   => 'modules'. LS_SLASH .'payments.php',
                    'icon' => 'icon-Money',
            		'title' => 'Payments Button',
                    'main_title' => 'Online Payments',
            		'text' => 'Offer your clients a simple way to pay for your services',
                    'slug' => 'ls_pm',
                    'custom_page_id' => false,
                    'custom_page_title' => 'Pay Online',
                    'custom_page_content' => array(
                        '<strong>You are invited to securely pay online using any Credit Card or PayPal. Simply fill in the form below.</strong> [livesite-pay]',
                        '<p>We accept online payments!</p><p>You may pay securely using any Credit Card or PayPal account.<br>Please use the form below to complete your payment. A confirmation email will be sent to you once the charge has been made.</p> [livesite-pay]',
                        '<strong>Make a payment using our secure and convenient online payment system.</strong> [livesite-pay]',
                        '<strong>We invite you to make a secure online payment.<br>To get started, please fill up the form below:</strong> [livesite-pay]',
                    ),
                    'custom_page_previously_created' => false,
                    'module_tray_text' => array(
                        'active' => 'Customize your payment form',
                        'disabled' => 'Add online payment options to your site'
                    )
                ),
                'scheduler' => array(
                    'active' => false,
                    'path'   => 'modules'. LS_SLASH .'scheduler.php',
                    'icon' => 'icon-Calendar',
            		'title' => 'Scheduler',
                    'main_title' => 'Appointment Scheduler',
            		'text' => 'Self service appointment scheduling for your clients',
                    'slug' => 'ls_sc',
                    'custom_page_id' => false,
                    'custom_page_title' => 'Book Appointment',
                    'custom_page_content' => array(
                      '<strong>We invite you to schedule an appointment online. See our available time below and pick a time that works best for you.</strong> [livesite-schedule title="Contact request"]',
                      '<strong>Use this calendar to schedule an appointment with us.</strong> [livesite-schedule title="Contact request"]',
                      '<strong>Please use the below CRM Software to reach out to us.</strong> [livesite-schedule title="Contact request"]',
                      '<strong>Use our Online Scheduling system to book an appointment, request a service or schedule a meeting.</strong> [livesite-schedule title="Contact request"]',
                    ),
                    'custom_page_previously_created' => false,
                    'module_tray_text' => array(
                        'active' => 'Customize your scheduling options',
                        'disabled' => 'Add online scheduling to your site'
                    )
                )
            )
        ));

}

/**
 * Change settings values
 * @param: $options: Array
 * @since 0.1.0
 */
function ls_set_settings( $options = array() ){

    $ls_settings = ls_get_settings();

    // Combine arrays
    $new_settings = array_replace_recursive( $ls_settings, $options );

    update_option( 'livesite_plugin_settings', $new_settings );

    return $new_settings;
}

// Gets the settings for the plugin
function ls_get_settings(){

    $default_option = array();

    return get_option( 'livesite_plugin_settings', $default_option );

}

/**
 * Settings
 * Checks if the user is connected to vCita
 * @return: Boolean
 * @since 0.1.0
 */
function ls_is_vcita_connected(){

    $settings = ls_get_settings();

    return isset( $settings['vcita_connected'] ) ? $settings['vcita_connected'] : false;

}

/**
 * Settings
 * Used to check if the plugin was upgraded so modules know not to auto intall pages`
 * @return: Boolean
 * @since 2.3.0
 */
function ls_is_plugin_upgraded(){

    $settings = ls_get_settings();

    return isset( $settings['plugin_upgraded'] ) ? $settings['plugin_upgraded'] : false;

}

/**
 * Settings
 * Activates module according to key name
 * @param: $module_name: @String
 * @since 0.1.0
 */
 function ls_activate_module( $module_name ){

     // Set module as active based on array key name
     ls_set_settings( array(
         'modules' => array(
             $module_name => array(
                 'active' => true
             )
         )
     ));

 }

 /**
  * Settings
  * Changes a specific modules settings
  * @param: $module_name{String}, $module_data{Array}
  * @since 0.1.0
  */
  function ls_set_module_data( $module_name, $module_data ){

      // Set module as active based on array key name
      ls_set_settings( array(
          'modules' => array(
              $module_name => $module_data
          )
      ));

  }

 /**
  * Settings
  * Setup user connection based on old plugin params for when we upgrade the old plugin
  * @param: $params{Array}
  * @since 0.1.0
  */
  function ls_parse_old_plugin_params( $params ){

      ls_set_settings( array(
          'vcita_connected' => true,
          'vcita_params' => array(
              'success'              => 1,
              'uid'                  => $params['uid'],
              'first_name'           => $params['first_name'],
              'last_name'            => $params['last_name'],
              'title'                => $params['title'],
              'confirmation_token'   => $params['confirmation_token'],
              'confirmed'            => $params['confirmed'],
              'engage_delay'         => 5,
              'implementation_key'   => $params['implementation_key'],
              'email'                => $params['email'],
                                        // convert to boolean
              'engage_active'        => filter_var( $params['engage_active'], FILTER_VALIDATE_BOOLEAN)
          )
      ));

      delete_option( 'vcita_paypal_payment_button' );

  }

?>
