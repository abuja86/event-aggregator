<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPEA_Upcoming_Events_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'wpea_upcoming_events',
			__('Upcoming Events', 'wp-event-aggregator'),
			array( 'description' => __('Display upcoming events', 'wp-event-aggregator'))
		);
	}

	public function widget( $args, $instance ) {
		$title = !empty($instance['title']) ? $instance['title'] : __('Upcoming Events', 'wp-event-aggregator');
		$number = !empty($instance['number']) ? absint($instance['number']) : 5;

		echo $args['before_widget'];
		if (!empty($title)) {
			echo $args['before_title'] . esc_html($title) . $args['after_title'];
		}

		$current_date = current_time('timestamp');
		$query_args = array(
			'post_type' => 'wp_events',
			'post_status' => 'publish',
			'posts_per_page' => $number,
			'meta_key' => 'start_ts',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'end_ts',
					'compare' => '>=',
					'value' => $current_date,
				)
			)
		);

		$events = new WP_Query($query_args);

		if ($events->have_posts()) {
			echo '<ul class="wpea-upcoming-events-widget">';
			while ($events->have_posts()) {
				$events->the_post();
				$event_date = get_post_meta(get_the_ID(), 'event_start_date', true);
				$venue = get_post_meta(get_the_ID(), 'venue_name', true);
				?>
				<li class="wpea-widget-event">
					<?php if (has_post_thumbnail()): ?>
					<div class="wpea-widget-event-thumb">
						<a href="<?php echo esc_url(get_permalink()); ?>">
							<?php the_post_thumbnail('thumbnail'); ?>
						</a>
					</div>
					<?php endif; ?>
					<div class="wpea-widget-event-info">
						<h4 class="wpea-widget-event-title">
							<a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a>
						</h4>
						<?php if ($event_date): ?>
						<div class="wpea-widget-event-date">
							<i class="fa fa-calendar"></i> <?php echo esc_html(date_i18n('M d, Y', strtotime($event_date))); ?>
						</div>
						<?php endif; ?>
						<?php if ($venue): ?>
						<div class="wpea-widget-event-venue">
							<i class="fa fa-map-marker"></i> <?php echo esc_html($venue); ?>
						</div>
						<?php endif; ?>
					</div>
				</li>
				<?php
			}
			echo '</ul>';
			wp_reset_postdata();
		} else {
			echo '<p>' . esc_html__('No upcoming events found.', 'wp-event-aggregator') . '</p>';
		}

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = !empty($instance['title']) ? $instance['title'] : __('Upcoming Events', 'wp-event-aggregator');
		$number = !empty($instance['number']) ? absint($instance['number']) : 5;
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
				<?php esc_html_e('Title:', 'wp-event-aggregator'); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr($this->get_field_id('title')); ?>"
				   name="<?php echo esc_attr($this->get_field_name('title')); ?>"
				   type="text"
				   value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number')); ?>">
				<?php esc_html_e('Number of events:', 'wp-event-aggregator'); ?>
			</label>
			<input class="tiny-text"
				   id="<?php echo esc_attr($this->get_field_id('number')); ?>"
				   name="<?php echo esc_attr($this->get_field_name('number')); ?>"
				   type="number"
				   step="1"
				   min="1"
				   value="<?php echo esc_attr($number); ?>"
				   size="3">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
		$instance['number'] = !empty($new_instance['number']) ? absint($new_instance['number']) : 5;
		return $instance;
	}
}

class WPEA_Featured_Events_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'wpea_featured_events',
			__('Featured Events', 'wp-event-aggregator'),
			array( 'description' => __('Display featured events', 'wp-event-aggregator'))
		);
	}

	public function widget( $args, $instance ) {
		$title = !empty($instance['title']) ? $instance['title'] : __('Featured Events', 'wp-event-aggregator');
		$number = !empty($instance['number']) ? absint($instance['number']) : 5;

		echo $args['before_widget'];
		if (!empty($title)) {
			echo $args['before_title'] . esc_html($title) . $args['after_title'];
		}

		$query_args = array(
			'post_type' => 'wp_events',
			'post_status' => 'publish',
			'posts_per_page' => $number,
			'meta_query' => array(
				array(
					'key' => 'wpea_featured',
					'value' => '1',
					'compare' => '='
				)
			),
			'orderby' => 'rand'
		);

		$events = new WP_Query($query_args);

		if ($events->have_posts()) {
			echo '<div class="wpea-featured-events-widget">';
			while ($events->have_posts()) {
				$events->the_post();
				$event_date = get_post_meta(get_the_ID(), 'event_start_date', true);
				?>
				<div class="wpea-widget-featured-event">
					<?php if (has_post_thumbnail()): ?>
					<div class="wpea-widget-featured-thumb">
						<a href="<?php echo esc_url(get_permalink()); ?>">
							<?php the_post_thumbnail('medium'); ?>
						</a>
					</div>
					<?php endif; ?>
					<div class="wpea-widget-featured-info">
						<h3 class="wpea-widget-featured-title">
							<a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a>
						</h3>
						<?php if ($event_date): ?>
						<div class="wpea-widget-featured-date">
							<i class="fa fa-calendar"></i> <?php echo esc_html(date_i18n('M d, Y', strtotime($event_date))); ?>
						</div>
						<?php endif; ?>
						<div class="wpea-widget-featured-excerpt">
							<?php echo wp_kses_post(wp_trim_words(get_the_excerpt(), 15)); ?>
						</div>
					</div>
				</div>
				<?php
			}
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p>' . esc_html__('No featured events found.', 'wp-event-aggregator') . '</p>';
		}

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = !empty($instance['title']) ? $instance['title'] : __('Featured Events', 'wp-event-aggregator');
		$number = !empty($instance['number']) ? absint($instance['number']) : 5;
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
				<?php esc_html_e('Title:', 'wp-event-aggregator'); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr($this->get_field_id('title')); ?>"
				   name="<?php echo esc_attr($this->get_field_name('title')); ?>"
				   type="text"
				   value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number')); ?>">
				<?php esc_html_e('Number of events:', 'wp-event-aggregator'); ?>
			</label>
			<input class="tiny-text"
				   id="<?php echo esc_attr($this->get_field_id('number')); ?>"
				   name="<?php echo esc_attr($this->get_field_name('number')); ?>"
				   type="number"
				   step="1"
				   min="1"
				   value="<?php echo esc_attr($number); ?>"
				   size="3">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
		$instance['number'] = !empty($new_instance['number']) ? absint($new_instance['number']) : 5;
		return $instance;
	}
}

function wpea_register_pro_widgets() {
	if (wpea_is_pro()) {
		register_widget('WPEA_Upcoming_Events_Widget');
		register_widget('WPEA_Featured_Events_Widget');
	}
}
add_action('widgets_init', 'wpea_register_pro_widgets');
