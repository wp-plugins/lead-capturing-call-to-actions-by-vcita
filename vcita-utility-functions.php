<?php

# Initialize the vCita widget by registering the widget hooks
function vcita_init() {
  if (!function_exists('register_sidebar_widget') || 
      !function_exists('register_widget_control')) {
    return;
  }

  vcita_initialize_data();

  wp_register_sidebar_widget('vcita_widget_id', 'vCita Sidebar Widget', 'vcita_widget_content');
  wp_register_widget_control('vcita_widget_id', 'vCita Sidebar Widget', 'vcita_widget_admin');
  add_filter('plugin_action_links', 'vcita_add_settings_link', 10, 2);

  register_uninstall_hook(VCITA_WIDGET_UNIQUE_LOCATION, 'vcita_uninstall');

  vcita_clear_options_if_needed();
}

# Add jqeury to vcita plugin
function vcita_jqeury_enqueue() {
  wp_enqueue_script('jquery');
}

# Remove the vCita widget and page if available
function vcita_uninstall() {
  delete_option(VCITA_WIDGET_KEY);
}

# Initialiaze the widget data system params
function vcita_initialize_data() {
  $vcita_widget = (array) get_option(VCITA_WIDGET_KEY);

  # Save if this is a new installation or not.
  if (empty($vcita_widget) || (!isset($vcita_widget['uid']) && !isset($vcita_widget['version']))) {
    $vcita_widget = array ('new_install' => 'true');
    update_option(VCITA_WIDGET_KEY.'init', true);
  } else if ($vcita_widget['new_install'] != 'true')  {
    $vcita_widget['new_install'] = 'false';
  }

  # Currently no migration is needed
  $vcita_widget['version'] = VCITA_WIDGET_VERSION;
  $vcita_widget['vcita_init'] = 'true';

  update_option(VCITA_WIDGET_KEY, $vcita_widget);
}

# Check the current user - either by the saved property or from WordPress
function vcita_get_email($widget_params) {
  return empty($widget_params['email']) ? get_option('admin_email') : $widget_params['email'];
}

# Check the current user by uid - either by the saved property or from WordPress
function vcita_get_uid() { 
  $vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
  if (empty($vcita_widget['uid'])) {
    return VCITA_WIDGET_DEMO_UID; 
  } else {
    return $vcita_widget['uid'];
  }
}

# Check the current user is a demo user
function vcita_is_demo_user() {
  return vcita_get_uid() == VCITA_WIDGET_DEMO_UID;
}

/* --- Internal Methods --- */

# Get the edit link to the requested page
function get_page_edit_link($page_id) {
  $page = get_page($page_id);
  return get_edit_post_link($page_id);
}


/**
 * Prepare all the common parameters for creating the vCita settings.
 * It also initializes the widget for the first time and stores the form data after the user saves the changes
 * @param widget_type - The type of widget to be stored for next usage
 */
function vcita_prepare_widget_settings($type) {
  $form_uid = rand();
  $uninitialized = false;

  if(empty($_POST)) {
    //Normal page display
    $vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
    $update_made = false;

    // Create a initial parameters - This form wasn't created yet - set to default
    if (!isset($vcita_widget['created']) ||
        empty($vcita_widget['created'])) {
      $vcita_widget = create_initial_parameters();
      $uninitialized = true;
    }

  } else {
    //Form data sent
    $update_made = true;

    if ($_POST['form_type'] == 'page_control') {
      $vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
    } else {
      $vcita_widget = (array) save_user_params();
    }
  }

  if ($type == 'widget') {
    $config_floating = '';
  } else {
    $config_floating = 'float:left;';
  }

  // In case not empty user
  // Generate the user if he isn't available or update to the latest user data from vCita server
  if (!$uninitialized) {
    if (empty($vcita_widget['uid'])) {
      $vcita_widget = (array) generate_or_validate_user($vcita_widget);
    } else {
      //$vcita_widget = (array) vcita_get_user($vcita_widget);
    }

    update_option(VCITA_WIDGET_KEY, $vcita_widget);
  }

    $config_html = '<div style="clear:both;text-align:left;display:block;padding-top:5px;"></div>';

    if (empty($vcita_widget['uid'])) {
        $disabled = '';
        $first_time = true;
    
  } else {
    $first_time = false;
        $disabled= "disabled=true title='To change your details, ";
    
        if ($vcita_widget['confirmed']) {
      $customize_link = vcita_create_link('Customize', 'widget_implementations', 'key='.$vcita_widget['implementation_key'].'&widget=widget');
      $set_meeting_pref_link = vcita_create_link('Meeting Preferences', 'settings', 'section=meetings');
      $set_profile_link = vcita_create_link('Edit Email/Profile', 'settings', 'section=profile');
      
      $disabled .= "please use the \"Customize\" link below.'";
            $config_html = "<div style='clear:both;text-align:left;display:block;padding:5px 0 10px 0;overflow:hidden;'>
                            <div style='margin-right:5px;".$config_floating."'>".$set_meeting_pref_link."</div>
              <div style='margin-right:5px;".$config_floating."'>".$set_profile_link."</div>";
              
      if ($type == "widget") {
        $config_html .= "<div style='margin-right:5px;".$config_floating."'>".$customize_link."</div>";
      }
      
      $config_html .= "</div>";
        } else {
      $disabled .= "please follow the instructions emailed to ".$vcita_widget["email"]."'";
    }
    }

    vcita_embed_clear_names(array("vcita_first-name_".$form_uid, "vcita_last-name_".$form_uid));

    return compact('vcita_widget', 'disabled', 'config_html', 'form_uid', 'update_made', 'first_time');
}

# Utility function to create a link with the correct host and all the required information.
function vcita_create_link($caption, $page, $params = '', $options = array()) {
  $origin = empty($options['origin']) ? 'int.4' : $options['origin'];
  $style = empty($options['style']) ? '' : $options['style'];
  $new_page = empty($options['new_page']) ? true : $options['new_page'];
  
  $params_prefix = empty($params) ? '' : '&';
  $origin_prefix = empty($origin) ? '' : '&';
  
  $link = "http://".VCITA_SERVER_BASE."/".$page."?ref=".VCITA_WIDGET_API_KEY.$params_prefix.$params.$origin_prefix.$origin;
  
  return "<a href=\"".$link."\"".($new_page ? " target='_blank'" : "")." style=".$style.">".$caption."</a>";
}

# Save the form data into the Wordpress variable
function save_user_params() {
    $vcita_widget = (array) get_option(VCITA_WIDGET_KEY);

  if ($_POST['form_type'] == 'engage_enable_control') {
    $vcita_widget['engage_active'] = ($_POST['Submit'] == 'Activate') ? 'true' : 'false';
  
  } else {
    $previous_email = vcita_default_if_non($vcita_widget, 'email');
    $vcita_widget['created'] = 1;
    $vcita_widget['email'] = vcita_default_if_non($_POST, 'vcita_email');
    $vcita_widget['first_name'] = isset($_POST['vcita_first-name']) ? stripslashes($_POST['vcita_first-name']) : '';
    $vcita_widget['last_name'] = isset($_POST['vcita_last-name']) ? stripslashes($_POST['vcita_last-name']) : '';
    $vcita_widget['title'] = isset($_POST['vcita_title']) ? stripslashes($_POST['vcita_title']) : '';

    if ($previous_email != $vcita_widget['email']) { // Email changes - reset id and keys
      $vcita_widget['uid'] = '';
      $vcita_widget['confirmation_token'] = '';
      $vcita_widget['implementation_key'] = '';
    }
  }

    update_option(VCITA_WIDGET_KEY, $vcita_widget);
  
    return $vcita_widget;
}

# Take the received data and parse it
# Returns the newly updated widgets parameters.
function vcita_parse_expert_data($raw_data) {
  $previous_id = '';
  if (!empty($vcita_widget) && (isset($vcita_widget['uid']))) {
    $previous_id = $widget_params['uid'];
  }
  
  $vcita_widget = array('title' => $raw_data['title'], 
               'created' => '1',
               'uid' => $raw_data['uid'], 
               'new_install' => 'false',
               'email' => $raw_data['email'],
               'first_name' => $raw_data['first_name'],
               'last_name' => $raw_data['last_name'],
               'email' => $raw_data['email'],
               'version' => VCITA_WIDGET_VERSION,
               'engage_active' => 'true',
               'confirmation_token' => $raw_data['confirmation_token'],
               'implementation_key' => $raw_data['implementation_key'],
               'confirmed' => $raw_data['confirmed']);

  delete_transient( 'embed_code' );
                 
  update_option(VCITA_WIDGET_KEY, $vcita_widget);

  update_option(VCITA_WIDGET_KEY, $vcita_widget);
}

# Take the received data and parse it
# Returns the newly updated widgets parameters.
function vcita_parse_expert_data_from_api($success, $widget_params, $raw_data) {
  $previous_id = $widget_params['uid'];
  $widget_params['uid'] = '';
  
  if (!$success) {
    $widget_params['last_error'] = 'Temporary problems, please try again';
  } else {
    $data = json_decode($raw_data);

    if ($data->{'success'} == 1) {
      $widget_params['first_name'] = $data->{'first_name'};
      $widget_params['last_name'] = $data->{'last_name'};
      $widget_params['engage_delay'] = $data->{'engage_delay'};
      $widget_params['engage_text'] = $data->{'engage_text'};
      $widget_params['confirmed'] = $data->{'confirmed'};
      $widget_params['last_error'] = '';
      $widget_params['uid'] = $data->{'id'};

      if ($previous_id != $data->{'id'} || !empty($data->{'confirmation_token'})) {
        $widget_params['confirmation_token'] = $data->{'confirmation_token'};
        $widget_params['implementation_key'] = $data->{'implementation_key'};
        
        // Active by Default if not previsouly disabled
        $widget_params['engage_active'] = vcita_default_if_non($widget_params, 'engage_active', 'true');
      }

    } else {
      $widget_params['last_error'] = $data-> {'error'};
    }
  }

  return $widget_params;
}

# Clear vcita params
# Returns the newly updated widgets parameters.
function vcita_clean_expert_data() {
  delete_option(VCITA_WIDGET_KEY);
}

/**
 * Perform an HTTP GET Call to retrieve the data for the required content.
 * @param $url
 * @return array - raw_data and a success flag
 */
function vcita_get_contents($url) {
  $response = wp_remote_get($url,
      array('header' => array('Accept' => 'application/json; charset=utf-8'),
        'timeout' => 10));

  return vcita_parse_response($response);
}

/**
 * Perform an HTTP POST Call to retrieve the data for the required content.
 * @param $url
 * @return array - raw_data and a success flag
 */
function vcita_post_contents($url) {
  $response  = wp_remote_post($url,
      array('header' => array('Accept' => 'application/json; charset=utf-8'),
        'timeout' => 10));

  return vcita_parse_response($response);
}

# Parse the HTTP response and return the data and if was successful or not.
function vcita_parse_response($response) {
  $success = false;
  $raw_data = 'Unknown error';
  
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

# Initials the vCita Widget parameters
function create_initial_parameters() {
  $old_params = (array) get_option(VCITA_WIDGET_KEY);
  
  $vcita_widget = array('title' => 'Contact me using vCita',
    'uid' => '',
    'new_install' =>
      isset($old_params['new_install'])
        ? $old_params['new_install']
        : 'false',
    'version' => VCITA_WIDGET_VERSION,
    'engage_active' => 'true',
    'contact_page' => 0,
    'confirmation_token' => '',
    'implementation_key' => '',
    'dismiss' => vcita_default_if_non($old_params, 'dismiss'),
  );
  update_option(VCITA_WIDGET_KEY, $vcita_widget);
  
  return $vcita_widget;
}

/**
 * Generic method to return default value if index doesn't exist in the array
 * Default value for the default is empty string
 */
function vcita_default_if_non($arr_obj, $index, $default_value = '') {
  return isset($arr_obj) && isset($arr_obj[$index]) ? $arr_obj[$index] : $default_value;
}

# CSS file loading
function vcita_add_stylesheet() {
  // Respects SSL, Style.css is relative to the current file
  wp_register_style( 'vcita-style', plugins_url('style.css', __FILE__) );
  wp_enqueue_style( 'vcita-style' );
}


/**
 * Use the vCita API to get a user, either create a new one or get the id of an available user
 *
 * @return array of the user name, id and if he finished the registration or not
 */
function generate_or_validate_user($widget_params) {
  extract(vcita_post_contents("http://".VCITA_SERVER_BASE."/api/experts?".
          "&id=" .urlencode($widget_params['uid']).
          "&email=" .urlencode($widget_params['email']).
                      "&first_name=" .urlencode($widget_params['first_name']).
                      "&last_name=" . urlencode($widget_params['last_name']).
          "&confirmation_token=".urlencode($widget_params['confirmation_token']).
                      "&ref=".VCITA_WIDGET_API_KEY.""));
          
  return vcita_parse_expert_data_from_api($success, $widget_params, $raw_data);
}

# Clears vCita option entry if "vcita-clear-options" GET parameter is specified in wp-admin
function vcita_clear_options_if_needed() {
  if (is_admin() && isset($_GET['vcita-clear-options'])) {
    delete_option(VCITA_WIDGET_KEY);
  }
}

# Identifies the contact page and returns it's ID.
function vcita_identify_contact_page() {
  global $wpdb;

  $wheres = array(
    '`post_title` LIKE "%Contact Us%"',
    '`post_title` LIKE "%Contact us%"',
    '`post_title` LIKE "%contact us%"',
    '`post_title` LIKE "%Contact%"',
    '`post_title` LIKE "%contact%"',
    '`post_name` LIKE "%contact-us%"',
    '`post_name` LIKE "%contact%"',
  );

  foreach ($wheres as $where) {
    $sql =
      'SELECT `ID` '.
      'FROM `'.$wpdb->posts.'` '.
      'WHERE '.$where;
    $page_id = $wpdb->get_var($sql);
    if (is_numeric($page_id)) {
      $page_id = @intval($page_id);
      return $page_id;
    }
  }
  return 0;
}

# Identifies the contact page and then saves it in settings
function vcita_set_contact_page() {
  $contact_page = vcita_identify_contact_page();
  if ($contact_page) {
    $vcita_widget = (array)get_option(VCITA_WIDGET_KEY);
    $vcita_widget['contact_page'] = $contact_page;
    update_option(VCITA_WIDGET_KEY, $vcita_widget);
  }
}

