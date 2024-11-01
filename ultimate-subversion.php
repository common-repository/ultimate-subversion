<?php
/*
Plugin Name: Ultimate-Subversion
Plugin URI: http://code.zero-one.ch/projects/wordpress-ultimate-subversion/
Description: Allows you to show Subversion information about a given Repository
Version: 1.0.3 
Author: zero-one
Author URI: http://www.zero-one.ch
*/

// ********************************************************
// ************************* INIT *************************
// Including additional files

$_basepath = WP_PLUGIN_DIR . "/ultimate-subversion/";
$_baseurl = WP_PLUGIN_URL . "/ultimate-subversion/";

require_once($_basepath."config.php");
require_once($_basepath."admin.php");
require_once($_basepath."class_base.php");
require_once($_basepath."class_ultimate-subversion.php");


$_ultimatesubversion = new UltimateSubversion();

/// *********************************************************
// ************************* HOOKS *************************
add_action('init', 'ZO_US_Plugin_Init');
add_action('wp_head', 'ZO_US_LoadCSS');
add_action('admin_menu', 'ZO_US_Register_AdminMenu');
add_filter('the_content', 'ZO_US_loadfrontend');

// *************************************************************
// ************************* FUNCTIONS *************************

function ZO_US_Plugin_Init() {
	ini_set("display_errors", TRUE);
}

function ZO_US_Register_AdminMenu() {
	global $ZO_US_config;
	
	$pluginname = $ZO_US_config['name_long'];
	
	add_menu_page($ZO_US_config['name_long'],$ZO_US_config['name_short'], 10, $pluginname, 'ZO_US_display_adminmenu'); 
	//add_submenu_page($pluginname, 'Daten Verwaltung', 'Daten Verwaltung', 10, $pluginname."datamgm", 'wedding_display_datamgm');
}

function ZO_US_loadfrontend($content = '') {
	global $_ultimatesubversion;
	return $_ultimatesubversion->loadfrontend($content);
}

function ZO_US_LoadCSS() {
	global $_baseurl;
	echo ( '<link rel="stylesheet" type="text/css" media="all" href="'. $_baseurl . 'style.css">' ); 
}
