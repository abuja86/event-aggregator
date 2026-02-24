<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Event_Aggregator_Venues {

	public function __construct() {
		add_action( 'init', array( $this, 'register_venue_post_type' ) );
		add_action( 'init', array( $this, 'register_organizer_post_type' ) );
		add_action( 'add_meta_boxes', array($this, 'add_venue_meta_boxes' ) );
		add_action( 'add_meta_boxes', array($this, 'add_organizer_meta_boxes' ) );
		add_action( 'save_post', array($this, 'save_venue_meta'), 10, 2);
		add_action( 'save_post', array($this, 'save_organizer_meta'), 10, 2);
		add_shortcode('wpea_venue_search', array( $this, 'venue_search_shortcode' ) );
		add_action( 'wp_ajax_wpea_search_locations', array($this, 'ajax_search_locations') );
		add_action( 'wp_ajax_nopriv_wpea_search_locations', array($this, 'ajax_search_locations') );
	}

	public function register_venue_post_type() {
		if( !wpea_is_pro() ) return;

		$labels = array(
			'name'                  => _x( 'Venues', 'Post Type General Name', 'wp-event-aggregator' ),
			'singular_name'         => _x( 'Venue', 'Post Type Singular Name', 'wp-event-aggregator' ),
			'menu_name'             => __( 'Venues', 'wp-event-aggregator' ),
			'name_admin_bar'        => __( 'Venue', 'wp-event-aggregator' ),
			'all_items'             => __( 'All Venues', 'wp-event-aggregator' ),
			'add_new_item'          => __( 'Add New Venue', 'wp-event-aggregator' ),
			'add_new'               => __( 'Add New', 'wp-event-aggregator' ),
			'new_item'              => __( 'New Venue', 'wp-event-aggregator' ),
			'edit_item'             => __( 'Edit Venue', 'wp-event-aggregator' ),
			'update_item'           => __( 'Update Venue', 'wp-event-aggregator' ),
			'view_item'             => __( 'View Venue', 'wp-event-aggregator' ),
			'search_items'          => __( 'Search Venue', 'wp-event-aggregator' ),
		);

		$args = array(
			'label'                 => __( 'Venue', 'wp-event-aggregator' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail' ),
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=wp_events',
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'publicly_queryable'    => true,
			'rewrite'               => array('slug' => 'venues'),
		);

		register_post_type( 'wpea_venue', $args );
	}

	public function register_organizer_post_type() {
		if( !wpea_is_pro() ) return;

		$labels = array(
			'name'                  => _x( 'Organizers', 'Post Type General Name', 'wp-event-aggregator' ),
			'singular_name'         => _x( 'Organizer', 'Post Type Singular Name', 'wp-event-aggregator' ),
			'menu_name'             => __( 'Organizers', 'wp-event-aggregator' ),
			'name_admin_bar'        => __( 'Organizer', 'wp-event-aggregator' ),
			'all_items'             => __( 'All Organizers', 'wp-event-aggregator' ),
			'add_new_item'          => __( 'Add New Organizer', 'wp-event-aggregator' ),
			'add_new'               => __( 'Add New', 'wp-event-aggregator' ),
			'new_item'              => __( 'New Organizer', 'wp-event-aggregator' ),
			'edit_item'             => __( 'Edit Organizer', 'wp-event-aggregator' ),
			'update_item'           => __( 'Update Organizer', 'wp-event-aggregator' ),
			'view_item'             => __( 'View Organizer', 'wp-event-aggregator' ),
			'search_items'          => __( 'Search Organizer', 'wp-event-aggregator' ),
		);

		$args = array(
			'label'                 => __( 'Organizer', 'wp-event-aggregator' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail' ),
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=wp_events',
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'publicly_queryable'    => true,
			'rewrite'               => array('slug' => 'organizers'),
		);

		register_post_type( 'wpea_organizer', $args );
	}

	public function add_venue_meta_boxes() {
		add_meta_box(
			'wpea_venue_details',
			__( 'Venue Details', 'wp-event-aggregator' ),
			array($this,'render_venue_meta_box'),
			'wpea_venue',
			'normal',
			'high'
		);
	}

	public function render_venue_meta_box( $post ) {
		wp_nonce_field( 'wpea_venue_meta', 'wpea_venue_nonce' );

		$address = get_post_meta( $post->ID, 'venue_address', true );
		$city = get_post_meta( $post->ID, 'venue_city', true );
		$state = get_post_meta( $post->ID, 'venue_state', true );
		$country = get_post_meta( $post->ID, 'venue_country', true );
		$zipcode = get_post_meta( $post->ID, 'venue_zipcode', true );
		$lat = get_post_meta( $post->ID, 'venue_lat', true );
		$lon = get_post_meta( $post->ID, 'venue_lon', true );
		$phone = get_post_meta( $post->ID, 'venue_phone', true );
		$website = get_post_meta( $post->ID, 'venue_website', true );
		?>

		<table class="form-table">
			<tr>
				<th><label for="venue_address"><?php esc_html_e('Address', 'wp-event-aggregator'); ?></label></th>
				<td><input type="text" name="venue_address" id="venue_address" value="<?php echo esc_attr($address); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="venue_city"><?php esc_html_e('City', 'wp-event-aggregator'); ?></label></th>
				<td><input type="text" name="venue_city" id="venue_city" value="<?php echo esc_attr($city); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="venue_state"><?php esc_html_e('State', 'wp-event-aggregator'); ?></label></th>
				<td><input type="text" name="venue_state" id="venue_state" value="<?php echo esc_attr($state); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="venue_country"><?php esc_html_e('Country', 'wp-event-aggregator'); ?></label></th>
				<td><input type="text" name="venue_country" id="venue_country" value="<?php echo esc_attr($country); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="venue_zipcode"><?php esc_html_e('Zipcode', 'wp-event-aggregator'); ?></label></th>
				<td><input type="text" name="venue_zipcode" id="venue_zipcode" value="<?php echo esc_attr($zipcode); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="venue_lat"><?php esc_html_e('Latitude', 'wp-event-aggregator'); ?></label></th>
				<td><input type="text" name="venue_lat" id="venue_lat" value="<?php echo esc_attr($lat); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="venue_lon"><?php esc_html_e('Longitude', 'wp-event-aggregator'); ?></label></th>
				<td><input type="text" name="venue_lon" id="venue_lon" value="<?php echo esc_attr($lon); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="venue_phone"><?php esc_html_e('Phone', 'wp-event-aggregator'); ?></label></th>
				<td><input type="text" name="venue_phone" id="venue_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="venue_website"><?php esc_html_e('Website', 'wp-event-aggregator'); ?></label></th>
				<td><input type="url" name="venue_website" id="venue_website" value="<?php echo esc_url($website); ?>" class="regular-text"></td>
			</tr>
		</table>
		<?php
	}

	public function add_organizer_meta_boxes() {
		add_meta_box(
			'wpea_organizer_details',
			__( 'Organizer Details', 'wp-event-aggregator' ),
			array($this,'render_organizer_meta_box'),
			'wpea_organizer',
			'normal',
			'high'
		);
	}

	public function render_organizer_meta_box( $post ) {
		wp_nonce_field( 'wpea_organizer_meta', 'wpea_organizer_nonce' );

		$email = get_post_meta( $post->ID, 'organizer_email', true );
		$phone = get_post_meta( $post->ID, 'organizer_phone', true );
		$website = get_post_meta( $post->ID, 'organizer_website', true );
		?>

		<table class="form-table">
			<tr>
				<th><label for="organizer_email"><?php esc_html_e('Email', 'wp-event-aggregator'); ?></label></th>
				<td><input type="email" name="organizer_email" id="organizer_email" value="<?php echo esc_attr($email); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="organizer_phone"><?php esc_html_e('Phone', 'wp-event-aggregator'); ?></label></th>
				<td><input type="text" name="organizer_phone" id="organizer_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="organizer_website"><?php esc_html_e('Website', 'wp-event-aggregator'); ?></label></th>
				<td><input type="url" name="organizer_website" id="organizer_website" value="<?php echo esc_url($website); ?>" class="regular-text"></td>
			</tr>
		</table>
		<?php
	}

	public function save_venue_meta($post_id, $post) {
		if (!isset($_POST['wpea_venue_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpea_venue_nonce'])), 'wpea_venue_meta')) {
			return $post_id;
		}

		if (!current_user_can("edit_post", $post_id)) {
			return $post_id;
		}

		if ($post->post_type != 'wpea_venue') {
			return $post_id;
		}

		$fields = array('venue_address', 'venue_city', 'venue_state', 'venue_country', 'venue_zipcode', 'venue_lat', 'venue_lon', 'venue_phone');

		foreach ($fields as $field) {
			if (isset($_POST[$field])) {
				$value = sanitize_text_field(wp_unslash($_POST[$field]));
				update_post_meta($post_id, $field, $value);
			}
		}

		if (isset($_POST['venue_website'])) {
			update_post_meta($post_id, 'venue_website', esc_url_raw(wp_unslash($_POST['venue_website'])));
		}
	}

	public function save_organizer_meta($post_id, $post) {
		if (!isset($_POST['wpea_organizer_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpea_organizer_nonce'])), 'wpea_organizer_meta')) {
			return $post_id;
		}

		if (!current_user_can("edit_post", $post_id)) {
			return $post_id;
		}

		if ($post->post_type != 'wpea_organizer') {
			return $post_id;
		}

		if (isset($_POST['organizer_email'])) {
			update_post_meta($post_id, 'organizer_email', sanitize_email(wp_unslash($_POST['organizer_email'])));
		}

		if (isset($_POST['organizer_phone'])) {
			update_post_meta($post_id, 'organizer_phone', sanitize_text_field(wp_unslash($_POST['organizer_phone'])));
		}

		if (isset($_POST['organizer_website'])) {
			update_post_meta($post_id, 'organizer_website', esc_url_raw(wp_unslash($_POST['organizer_website'])));
		}
	}

	public function venue_search_shortcode($atts) {
		$atts = shortcode_atts(array(
			'placeholder' => __('Search by location...', 'wp-event-aggregator')
		), $atts);

		ob_start();
		?>
		<div class="wpea-location-search">
			<form class="wpea-location-search-form" method="get">
				<input type="text"
					   name="location_search"
					   class="wpea-location-search-input"
					   placeholder="<?php echo esc_attr($atts['placeholder']); ?>"
					   value="<?php echo esc_attr(isset($_GET['location_search']) ? sanitize_text_field(wp_unslash($_GET['location_search'])) : ''); ?>">
				<button type="submit" class="wpea-location-search-button">
					<i class="fa fa-search"></i> <?php esc_html_e('Search', 'wp-event-aggregator'); ?>
				</button>
			</form>
			<div class="wpea-location-results"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function ajax_search_locations() {
		check_ajax_referer('wpea_ajax_nonce', 'nonce');

		$search = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';

		if (empty($search)) {
			wp_send_json_error(array('message' => __('Please enter a search term', 'wp-event-aggregator')));
		}

		global $wpdb;
		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT DISTINCT pm1.post_id, pm1.meta_value as venue_name, pm2.meta_value as venue_city, pm3.meta_value as venue_country
			FROM {$wpdb->postmeta} pm1
			LEFT JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id AND pm2.meta_key = 'venue_city'
			LEFT JOIN {$wpdb->postmeta} pm3 ON pm1.post_id = pm3.post_id AND pm3.meta_key = 'venue_country'
			LEFT JOIN {$wpdb->posts} p ON pm1.post_id = p.ID
			WHERE p.post_type = 'wp_events'
			AND p.post_status = 'publish'
			AND pm1.meta_key = 'venue_name'
			AND (pm1.meta_value LIKE %s OR pm2.meta_value LIKE %s OR pm3.meta_value LIKE %s)
			LIMIT 20",
			'%' . $wpdb->esc_like($search) . '%',
			'%' . $wpdb->esc_like($search) . '%',
			'%' . $wpdb->esc_like($search) . '%'
		));

		wp_send_json_success(array('results' => $results));
	}
}
