<?php
/**
* Settings
* Activates module using ajax call
* @since 0.1.0
*/
 function ls_remote_activate_module(){

   $nonce = $_POST['nonce'];

   $return = array(
       'status' => false
   );

   // check to see if the submitted nonce matches with the
   // generated nonce we created earlier
   if ( ! wp_verify_nonce( $nonce, 'activate-module' ) )
       die ( 'Unauthorized attempt');

   // If you need to pass in extra information
   $module_name = $_POST['module_name'];

   if ( isset( $module_name ) && $module_name != '' ){

       ls_activate_module( $module_name );

       $module_data = ls_get_module_data( $module_name );

       $return['module_slug'] = $module_data['slug'];
       $return['status'] = true;

   }

   header( "Content-Type: application/json" );
   echo json_encode( $return );

   exit();

 }

 // Defines the connection between the ajax request and the wordpress backend function
 add_action("wp_ajax_activate-module", "ls_remote_activate_module");

 // This is for users who are not logged in
 //add_action("wp_ajax_nopriv_request-post", "ls_remote_activate_module");


/**
* Settings
* Creates a custom page for a page
* @since 0.1.0
*/
function ls_remote_create_module_page(){

    $nonce = $_POST['nonce'];

    $page_id = false;

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if ( ! wp_verify_nonce( $nonce, 'module-page' ) )
       die ( 'Unauthorized attempt');

    $page_title = $_POST['page_title'];
    $page_content = $_POST['page_content'];
    $module_name = $_POST['module_name'];

    if ( isset( $page_title ) && isset( $page_content ) && isset( $module_name ) ) {

        $helpers = new ls_helpers();

        $page_id = $helpers->add_wp_page( $page_title, $page_content );

        if ( $page_id )
            ls_set_settings( array(
               'modules' => array(
                   $module_name => array(
                       'custom_page_id' => $page_id
                   )
               )
            ));

    }

    header( "Content-Type: application/json" );
    echo json_encode( $page_id );

    exit();

}

 // Defines the connection between the ajax request and the wordpress backend function
 add_action("wp_ajax_create-module-page", "ls_remote_create_module_page");

 // This is for users who are not logged in
 //add_action("wp_ajax_nopriv_request-post", "ls_remote_activate_module");

/**
* Settings
* Removes custom page
* @since 0.1.0
*/
function ls_remote_remove_module_page(){

    $nonce = $_POST['nonce'];

    $page_id = false;

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if ( ! wp_verify_nonce( $nonce, 'module-page' ) )
       die ( 'Unauthorized attempt');

    $page_id = $_POST['page_id'];
    $module_name = $_POST['module_name'];

    if ( isset( $page_id ) && isset( $module_name ) ) {

        if ( $page_id )
            ls_set_settings( array(
               'modules' => array(
                   $module_name => array(
                       'custom_page_id' => false
                   )
               )
            ));

        // Delete page permanantely skip the trash
        $page_id = wp_delete_post( $page_id, true );

    }

    header( "Content-Type: application/json" );
    echo json_encode( $page_id );

    exit();

}

 // Defines the connection between the ajax request and the wordpress backend function
 add_action("wp_ajax_remove-module-page", "ls_remote_remove_module_page");

 // This is for users who are not logged in
 //add_action("wp_ajax_nopriv_request-post", "ls_remote_activate_module");

 /**
 * Settings
 * Activates module using ajax call
 * @since 0.1.0
 */
function ls_update_livesite_status(){

    $nonce = $_POST['nonce'];

    $status = false;

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if ( ! wp_verify_nonce( $nonce, 'module-page' ) )
        die ( 'Unauthorized attempt');

    // If you need to pass in extra information
    $show_livesite = $_POST['show_livesite'];

    if ( isset( $show_livesite ) )
        ls_set_settings( array(
           'modules' => array(
               'livesite_widget' => array(
                   'show_livesite' => filter_var( $show_livesite, FILTER_VALIDATE_BOOLEAN)
               )
           )
        ));

    $status = true;

    header( "Content-Type: application/json" );
    echo json_encode( $status );

    exit();

  }

  // Defines the connection between the ajax request and the wordpress backend function
  add_action("wp_ajax_update-livesite-status", "ls_update_livesite_status");

?>
