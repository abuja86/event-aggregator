<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$events_with_location = array();
if ( $wp_events->have_posts() ) {
	while ( $wp_events->have_posts() ) {
		$wp_events->the_post();
		$lat = get_post_meta( get_the_ID(), 'venue_lat', true );
		$lon = get_post_meta( get_the_ID(), 'venue_lon', true );

		if ( !empty($lat) && !empty($lon) ) {
			$venue_name = get_post_meta( get_the_ID(), 'venue_name', true );
			$venue_address = get_post_meta( get_the_ID(), 'venue_address', true );
			$event_date = get_post_meta( get_the_ID(), 'event_start_date', true );

			$events_with_location[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'url' => get_permalink(),
				'lat' => floatval($lat),
				'lon' => floatval($lon),
				'venue' => $venue_name,
				'address' => $venue_address,
				'date' => $event_date ? date_i18n( 'M d, Y', strtotime($event_date) ) : '',
				'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' )
			);
		}
	}
	$wp_events->rewind_posts();
}
?>

<div class="wpea-map-view-container">
	<div id="wpea-map" class="wpea-map" data-events='<?php echo esc_attr( wp_json_encode($events_with_location) ); ?>'></div>

	<div class="wpea-map-sidebar">
		<h3 class="wpea-map-sidebar-title"><?php esc_html_e('Events List', 'wp-event-aggregator'); ?></h3>
		<div class="wpea-map-events-list">
			<?php
			if ( $wp_events->have_posts() ) {
				while ( $wp_events->have_posts() ) {
					$wp_events->the_post();
					$lat = get_post_meta( get_the_ID(), 'venue_lat', true );
					$lon = get_post_meta( get_the_ID(), 'venue_lon', true );
					$venue_name = get_post_meta( get_the_ID(), 'venue_name', true );
					$event_date = get_post_meta( get_the_ID(), 'event_start_date', true );
					$has_location = !empty($lat) && !empty($lon);
					?>
					<div class="wpea-map-event-item <?php echo $has_location ? 'has-location' : ''; ?>" data-event-id="<?php echo esc_attr(get_the_ID()); ?>">
						<?php if ( has_post_thumbnail() ): ?>
						<div class="wpea-map-event-thumb">
							<?php the_post_thumbnail('thumbnail'); ?>
						</div>
						<?php endif; ?>
						<div class="wpea-map-event-info">
							<h4 class="wpea-map-event-title">
								<a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a>
							</h4>
							<?php if ( $event_date ): ?>
							<div class="wpea-map-event-date">
								<i class="fa fa-calendar"></i> <?php echo esc_html( date_i18n( 'M d, Y', strtotime($event_date) ) ); ?>
							</div>
							<?php endif; ?>
							<?php if ( $venue_name ): ?>
							<div class="wpea-map-event-venue">
								<i class="fa fa-map-marker"></i> <?php echo esc_html( $venue_name ); ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
</div>
