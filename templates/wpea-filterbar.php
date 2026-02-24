<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$categories = get_terms(array(
	'taxonomy' => 'event_category',
	'hide_empty' => true,
));
?>

<div class="wpea-filterbar">
	<form class="wpea-filter-form" method="get" action="">
		<div class="wpea-filter-row">
			<div class="wpea-filter-group">
				<label for="wpea-filter-category"><?php esc_html_e('Category', 'wp-event-aggregator'); ?></label>
				<select name="event_category" id="wpea-filter-category" class="wpea-filter-select">
					<option value=""><?php esc_html_e('All Categories', 'wp-event-aggregator'); ?></option>
					<?php foreach ($categories as $category): ?>
					<option value="<?php echo esc_attr($category->slug); ?>" <?php selected(isset($_GET['event_category']) ? sanitize_text_field(wp_unslash($_GET['event_category'])) : '', $category->slug); ?>>
						<?php echo esc_html($category->name); ?>
					</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="wpea-filter-group">
				<label for="wpea-filter-start-date"><?php esc_html_e('Start Date', 'wp-event-aggregator'); ?></label>
				<input type="date" name="start_date" id="wpea-filter-start-date" class="wpea-filter-input" value="<?php echo esc_attr(isset($_GET['start_date']) ? sanitize_text_field(wp_unslash($_GET['start_date'])) : ''); ?>">
			</div>

			<div class="wpea-filter-group">
				<label for="wpea-filter-end-date"><?php esc_html_e('End Date', 'wp-event-aggregator'); ?></label>
				<input type="date" name="end_date" id="wpea-filter-end-date" class="wpea-filter-input" value="<?php echo esc_attr(isset($_GET['end_date']) ? sanitize_text_field(wp_unslash($_GET['end_date'])) : ''); ?>">
			</div>

			<div class="wpea-filter-group">
				<label for="wpea-filter-location"><?php esc_html_e('Location', 'wp-event-aggregator'); ?></label>
				<input type="text" name="location" id="wpea-filter-location" class="wpea-filter-input" placeholder="<?php esc_attr_e('City or Venue', 'wp-event-aggregator'); ?>" value="<?php echo esc_attr(isset($_GET['location']) ? sanitize_text_field(wp_unslash($_GET['location'])) : ''); ?>">
			</div>

			<div class="wpea-filter-group">
				<label for="wpea-filter-search"><?php esc_html_e('Search', 'wp-event-aggregator'); ?></label>
				<input type="text" name="s" id="wpea-filter-search" class="wpea-filter-input" placeholder="<?php esc_attr_e('Search events...', 'wp-event-aggregator'); ?>" value="<?php echo esc_attr(isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : ''); ?>">
			</div>

			<div class="wpea-filter-actions">
				<button type="submit" class="wpea-filter-button wpea-filter-apply">
					<i class="fa fa-search"></i> <?php esc_html_e('Apply Filters', 'wp-event-aggregator'); ?>
				</button>
				<a href="<?php echo esc_url(strtok(esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'] ?? '')), '?')); ?>" class="wpea-filter-button wpea-filter-reset">
					<i class="fa fa-times"></i> <?php esc_html_e('Reset', 'wp-event-aggregator'); ?>
				</a>
			</div>
		</div>
	</form>
</div>
