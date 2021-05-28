<?php
require_once 'google-maps.php';

class store_Widget extends WP_Widget {
	public function __construct()
	{
		$widget_options = array(
			'classname'   => 'store_widget',
			'description' => __( 'Stores location widget' )
		);
		parent::__construct( 'store_widget', __( 'Stores Location' ), $widget_options );
	}

	public function widget($args, $instance) {
		$q_args = array(
			'post_type' => 'stores',
		);
		$coords = array();
		$names = array();
		$desc = array();
		$query = new WP_Query($q_args);
		while ( $query->have_posts() ) {
			$query->the_post();
			global $post;
			array_push($coords, get_post_meta( $post->ID, 'store-address', true));
			array_push($names, get_post_meta( $post->ID, 'store-name', true ));
			array_push($desc, get_post_meta( $post->ID, 'store-description', true ));
		}
	    ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
            <title>My Google Map</title>
            <style>
                #map{
                    height:400px;
                    width:100%;
                }
            </style>
        </head>
        <body>
        <div class="coords" data-attr="<?php echo $coords ?>"></div>
        <div class="names" data-attr="<?php echo $names ?>"></div>
        <div class="desc" data-attr="<?php echo $desc ?>"></div>
        </body>
        </html>


		<?php
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

