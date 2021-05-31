<?php
/**
* Plugin Name: Google Map Plugin
* Description: Plugin for adding map on web-site
* Version: 1.1
* Author: Vladyslav Kukil
* Author URI: http://yourwebsiteurl.com/
**/
require_once 'class-maps-widget.php';


function stores_post_type(){
	$labels = array(
		'name' => __('Stores'),
		'singular_name' => __('Store'),
		'add_new' => __('Add Store'),
		'add_new_item' => __('Adding Store'),
		'new_item' => __('New Store'),
		'view_item' => __('View Store'),
		'search_items' => __('Search Stores'),
		'not_found' => __('Stores not found'),
		'not_found_in_trash' => __('Stores not fount in trash'),
		'all_items' => __('All Stores'),
		'filter_items_list' => __('Filter Stores'),
		'items_list_navigation' => __('Stores navigation'),
		'items_list' => __('List of Stores'),
		'menu_name' => __('Stores'),
		'name_admin_bar' => __('Store'),
		'archives' => __('Store archives'),
		'attributes' => __('Store attributes'),
		'parent_item_colon' => __('Parent Store'),
		'view_items' => __('View Stores'),
		'item_updated' => __('Store was updated'),
		'item_published' => __('Stores was published'),
		'item_published_privately' => __('Stores was published privately'),
		'item_reverted_to_draft' => __('Stores not fount'),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'menu_position' => 5,
		'has_archive' => true,
		'supports' => array( 'title', 'excerpt', 'author', 'revisions', 'comments', 'thumbnail'),
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'hierarchical' => false,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'capability_type' => __('post'),
		'rewrite' => array('slug' => 'stores'),
	);
	register_post_type('stores', $args);
}

add_action('init', 'stores_post_type', 0);

function rewrite_Stores_flush() {
	stores_post_type();
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'rewrite_stores_flush');

add_action('load-post.php', 'stores_post_meta_boxes_setup');
add_action('load-post-new.php', 'stores_post_meta_boxes_setup');

function stores_post_meta_boxes_setup() {
	add_action( 'add_meta_boxes', 'stores_meta_box' );
}

function Stores_meta_box() {
	add_meta_box(
		'smashing-post-class',      // Unique ID
		esc_html__( 'Store Info'),
		'stores_callback',   // Callback function
		'stores',         // Admin page (or post type)
		'side',         // Context
		'default'         // Priority
	);
}

function stores_callback() {
    // generate a nonce field
	wp_nonce_field( 'stores_meta_box', 'stores_nonce' );
	// get previously saved meta values (if any)

		?>

		<p><label for="stores-name"><?php _e( 'Store Name'); ?>
				<input class="widefat" id="store-name" type="text" name="store-name" required
				       placeholder="Store Name" /></label></p>

		<p><label for="stores-description"> <?php _e( 'Store Description'); ?>
			<input class="widefat" id="store-description" type="text" name="store-description" required
			       placeholder="Store Description" /></label></p>

		<p><label for="stores-address"> <?php _e( 'Store Address'); ?>
				<input class="widefat" id="store-address" type="text" name="store-address" required
				       placeholder="Store Address" /></label></p>
		<?php

}

function stores_save($post_id) {
	// check if nonce is set
	if ( ! isset( $_POST['stores_nonce'] ) ) {
		return;
	}
	// verify that nonce is valid
	if ( ! wp_verify_nonce( $_POST['stores_nonce'], 'stores_meta_box' ) ) {
		return;
	}
	// if this is an autosave, our form has not been submitted, so do nothing
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// check user permissions
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	// checking for the values and save fields
	if ( isset( $_POST['store-name'] ) ) {
		update_post_meta( $post_id, 'store-name', sanitize_text_field( $_POST['store-name'] ) );
	}
	if ( isset( $_POST['store-description'] ) ) {
		update_post_meta( $post_id, 'store-description', sanitize_text_field( $_POST['store-description'] ) );
	}
	if ( isset( $_POST['store-address'] ) ) {
		update_post_meta( $post_id, 'store-address', sanitize_text_field( $_POST['store-address'] ) );
	}
}

add_action( 'save_post', 'stores_save');

add_shortcode('google-maps', 'map_shortcode');


add_action( 'wp_enqueue_scripts', 'add_coords' );

function add_coords() {
	$q_args = array(
		'post_type' => 'stores',
	);
	$coords = array();
	$names  = array();
	$desc   = array();
	$query  = new WP_Query( $q_args );
	while ( $query->have_posts() ) {
		$query->the_post();
		global $post;
		array_push( $coords, get_post_meta( $post->ID, 'store-address', true ) );
		array_push( $names, get_post_meta( $post->ID, 'store-name', true ) );
		array_push( $desc, get_post_meta( $post->ID, 'store-description', true ) );
	}
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('googleapi', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAFKGM4i-IihJp62mQ9sAbHJG0WzfyTJQg');
	wp_enqueue_script( 'axios','https://unpkg.com/axios/dist/axios.min.js' );
	wp_enqueue_script( 'googlemaps', plugins_url()  . '/google-maps-plugin/map.js');

	wp_localize_script( 'googlemaps', 'coords', $coords );
	wp_localize_script( 'googlemaps', 'desc', $desc );
	wp_localize_script( 'googlemaps', 'names', $names );
}

function map_shortcode() {
	return '<style>
                #map{
                    height:400px;
                    width:100%;
                }
            </style>
            <h1>Our stores are located:</h1> 
            <div id="map"></div>';
}


