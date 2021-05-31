<?php
require_once 'google-maps.php';

class store_Widget extends WP_Widget {
	public function __construct()
	{
		$widget_options = array(
			'classname'   => __('store_widget'),
			'description' => __( 'Stores location widget' )
		);
		parent::__construct( 'store_widget', __( 'Stores Location' ), $widget_options );
	}

	public function widget($args, $instance) {

	}

	public function form($instance) {

	}

	public function update( $new_instance, $old_instance ) {

	}
}

function store_register_widget() {
	register_widget( 'store_Widget' );
}
add_action( 'widgets_init', 'store_register_widget' );




