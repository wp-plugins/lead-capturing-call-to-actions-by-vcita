<?php
add_action( 'wp_ajax_vcita_ajax_toggle_ae', 'vcita_ajax_toggle_ae');
add_action( 'wp_ajax_vcita_ajax_toggle_contact', 'vcita_ajax_toggle_contact');

function vcita_ajax_toggle_ae() {
  $vcita_widget = (array)get_option(VCITA_WIDGET_KEY);
  $vcita_widget['engage_active'] = $_POST['activate'];
  update_option(VCITA_WIDGET_KEY, $vcita_widget);
}

function vcita_ajax_toggle_contact() {
  if (!isset($_POST['page_id'])) die('No page ID given.');

  $page_id = @intval($_POST['page_id']);

  $vcita_widget = (array)get_option(VCITA_WIDGET_KEY);
  $vcita_widget['contact_page'] = $page_id;
  update_option(VCITA_WIDGET_KEY, $vcita_widget);

  die('OK');
}

