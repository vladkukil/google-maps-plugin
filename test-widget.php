<?php
require_once 'google-maps.php';

class Test_Widget extends WP_Widget {
	public function __construct()
	{
		$widget_options = array(
			'classname'   => 'test_widget',
			'description' => __( 'Test' )
		);
		parent::__construct( 'test_widget', __( 'Test' ), $widget_options );
	}

	public function widget($args, $instance) {
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
        <h1>My Google Map</h1>
        <div id="map"></div>
        <script>
            function initMap() {
                let coords = [];
                let names = [];
                let desc = [];
                <?php $query_args = array(
	                'post_type' => 'stores',
                );
	                $query = new WP_Query( $query_args );
	                while ( $query->have_posts() ) {
		                $query->the_post();
		                global $post;
		                ?>
                coords.push('<?php echo get_post_meta( $post->ID, 'store-address', true )?>');
                names.push('<?php echo get_post_meta( $post->ID, 'store-name', true )?>');
                desc.push('<?php echo get_post_meta( $post->ID, 'store-description', true )?>');
		                <?php
	                }
	                ?>

                let options = {
                    zoom: 8,
                    center: {lat: 49.5850, lng: 36.1409},
                }
                let map = new google.maps.Map(document.getElementById('map'), options);
                let i;
                for (i = 0; i < coords.length; i++) {
                    let description = desc[i];
                    let name = names[i];

                    axios.get('https://maps.googleapis.com/maps/api/geocode/json', {
                        params: {
                            address: coords[i],
                            key: 'AIzaSyAFKGM4i-IihJp62mQ9sAbHJG0WzfyTJQg'
                        }
                    }).then(function (response) {
                        // Log full response
                        console.log(response);
                        let lat = response.data.results[0].geometry.location.lat;
                        let lng = response.data.results[0].geometry.location.lng;

                        let coord = {lat: lat, lng: lng};
                        for (i = 0; i < coords.length; i++) {
                            const infowindow = new google.maps.InfoWindow({
                                content: 'Store Name: ' + name + '<br>' + description,
                            });

                            let marker;
                            marker = new google.maps.Marker({
                                position: coord,
                                map: map,
                                title: name,
                            });
                            marker.addListener("click", () => {
                                infowindow.open(map, marker);
                            });

                        }

                    });
                }
            }



        </script>
        <script async
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFKGM4i-IihJp62mQ9sAbHJG0WzfyTJQg&callback=initMap">
        </script>

        </body>
        </html>

            <?php
	}

	public function form($instance) {
		$name = $instance['name'] ?? 'not found';
		$description = $instance['description'] ?? 'not found';
		$address = $instance['address'] ?? 'not found';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>"><?php _e( 'Name of Store:' ); ?></label>
			<input type="text" value="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" class="widefat" />
			<br />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'address' ); ?>"><?php _e( 'address:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>" type="text" step="1" min="1" value="<?php echo $address; ?>" size="3" />
			<br />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'description:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" type="text" step="1" min="1" value="<?php echo $description; ?>" size="3" />
			<br />
		</p>

<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['name'] = sanitize_text_field( $new_instance['name'] );
		$instance['address'] = sanitize_text_field( $new_instance['address'] );
		$instance['description'] = sanitize_text_field( $new_instance['description'] );
		return $instance;
	}
}
add_action('wp_ajax_getposttitle', 'get_title_func');
add_action('wp_ajax_nopriv_getposttitle', 'get_title_func');


function test_register_widget() {
	register_widget( 'test_Widget' );
}
add_action( 'widgets_init', 'test_register_widget' );

