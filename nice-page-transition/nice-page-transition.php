<?php

/*

Plugin Name: Nice page transition

Plugin URI: 

Version: 1.05

Description: Create nice page transition!

Author: InfoD74

Author URI: https://www.info-d-74.com/en/shop/

Network: false

Text Domain: nice-page-transition

Domain Path: 

*/

register_activation_hook( __FILE__, 'nice_page_transition_install' );

register_uninstall_hook(__FILE__, 'nice_page_transition_desinstall');

function nice_page_transition_install($network_wide) {

	global $wpdb;

	if (is_multisite() && $network_wide) {

		// get ids of all sites

		$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		foreach ($blogids as $blog_id) {

			switch_to_blog($blog_id);

			// create options for each site
			nice_page_transition_create_option();

			restore_current_blog();

		}

	}
	else
		nice_page_transition_create_option();

}

function nice_page_transition_create_option() {

	//ajoute les options de config

	add_option( 'nice_page_transition_type', 'brightness' );

}

function nice_page_transition_desinstall() {

	if (is_multisite())	{

		global $wpdb;

		// get ids of all sites

		$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		foreach ($blogids as $blog_id) {

			switch_to_blog($blog_id);

			// create tables for each site

			nice_page_transition_delete_option();

			restore_current_blog();

		}

	}
	else

		nice_page_transition_delete_option();

}

function nice_page_transition_delete_option() {

	//suppression des options

	delete_option( 'nice_page_transition_type' );

}

add_action( 'admin_menu', 'register_nice_page_transition_menu' );

function register_nice_page_transition_menu() {

	add_submenu_page( 'options-general.php', 'Nice page transition settings', 'Nice page transition settings', 'edit_pages', 'nice_page_transition_settings', 'nice_page_transition_settings');

}



function nice_page_transition_settings() {

	//formulaire soumis ?

	if(sizeof($_POST))

	{

		check_admin_referer( 'npt_settings' );		

		$type = sanitize_text_field($_POST['type']);

		update_option('nice_page_transition_type', $type);



	}

	else

		$type = get_option('nice_page_transition_type');



	include(plugin_dir_path( __FILE__ ) . 'views/settings.php');

}



add_action( 'wp_head', 'head_nice_page_transition' );

function head_nice_page_transition()

{

	wp_enqueue_style( 'nice_page_transition_css', plugins_url('css/front.css', __FILE__) );

	wp_enqueue_script( 'jquery' );

	wp_register_script( 'nice_page_transition_js', plugins_url( 'js/front.js', __FILE__ ) );



	$settings = array(

		'type' => get_option('nice_page_transition_type')

	);

	wp_localize_script( 'nice_page_transition_js', 'settings', $settings );

	wp_enqueue_script( 'nice_page_transition_js');

}



add_filter( 'body_class', function( $classes ) {

    $type = get_option('nice_page_transition_type');

    return array_merge( $classes, array( $type ) );

} );