<?php

defined('ABSPATH');

/**
 * Plugin Name: iClew Business Kit
 * Plugin URI: https://iclew.com
 * Description: Free tools and resources you need for Wordpress-based business success. To get started, 1) Click "Activate" link to the left to enable this plugin; 2) Click "Settings" link to the left to register your website. You're then ready to bring your business to an even higher level!
 * Version: 1.0
 * Author: iClew
 * Author URI: https://iclew.com
 * License: GPL2
 */
 
/*  Copyright 2014  iClew.com  (email : supprt@iclew.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/ 

/**
 * Admin UI
 */

add_action('admin_menu', 'iclew_admin_menu');
 
function iclew_admin_menu() {
  add_options_page("iClew Business Kit", "iClew Business Kit", "manage_options", "iclew_business_kit", "iclew_admin_options");
}

function iclew_admin_options() {
  if (get_option('iclew_connected')) { 
    include('iclew_account_link.php');
  }
  else {
    include('iclew_account_new.php');
  }
}
 
/*
 * Add settings link on plugin page 
 */
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'iclew_settings_link' );

function iclew_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=iclew_business_kit">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

/**
 * Responds to update request from iClew server
 */
add_action( 'parse_request', function() {
  $requests = parse_url($_SERVER['REQUEST_URI']);  
  parse_str($requests['query'], $queries);
  if (isset($queries['q'])) {
    if ($queries['q'] == 'iclew') {
      iclew_callback($queries);
      exit();
    }
  }  
}, 0 );

function iclew_callback($queries) {
  if (!isset($queries['token']) or !isset($queries['uid'])) {
    wp_send_json('Parameter missed');
    return;
  }  
  if (isset($queries['email'])) {
    update_option('iclew_email', $queries['email']);
  }
  if (get_option('iclew_connected') <> 1) {
      update_option('iclew_connected', 1);
  }  
  $msg = array();  
  global $wp_version;
  $msg['web cms'] = 'wordpress'; 
  $msg['wordpress'] = $wp_version; 
  $msg['site created'] = iclew_site_created();
  $msg['user count'] = iclew_user_count();
  $msg['wordpress themes'] = array_keys(wp_get_themes());
  $msg['wordpress active theme'] = get_option('template');
  $plugins =  iclew_get_plugins();
  foreach ($plugins as $plugin) {
    $msg[$plugin] = '';
  }  
  $results = iclew_callback_fsockopen($msg, $queries);
  wp_send_json($results);
  return;
}

function iclew_callback_fsockopen($msg, $queries) {
  $fp = fsockopen('ssl://iclew.com', 443, $errno, $errstr, 15);
  if (!$fp) {
    return ' Error: ' . $errno . ' ' . $errstr;
  } 
  
  $content = http_build_query($msg);

  fwrite($fp, "POST /sbk/?uid=".$queries['uid'].'&token='.$queries['token']." HTTP/1.0\r\n");
  fwrite($fp, "Host: acobot.com\r\n");
  fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
  fwrite($fp, "Content-Length: ".strlen($content)."\r\n");
  fwrite($fp, "Connection: close\r\n");
  fwrite($fp, "\r\n");

  fwrite($fp, $content);

  while (!feof($fp)) {
    @fgets($fp, 1024);
  }
  return 'Posted';
}

function iclew_site_created() {
  $account = get_user_by('id', 1);
  if (!$account) {
    return 0;
  }
  return $account->user_registered;
}

function iclew_user_count() {
  $result = count_users();
  return $result['total_users'];  
}

function iclew_get_plugins() {
  if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }
  $plugins = get_plugins();
  $names = array();
  foreach ($plugins as $path=>$data) {
    $parts = explode('/', $path);
    $names[] = 'wordpress plugin '.$parts[0];
  }
  $names = array_unique($names);
  return $names;
}
