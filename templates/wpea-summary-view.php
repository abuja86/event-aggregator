<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$event_date = get_post_meta( get_the_ID(), 'event_start_date', true );
if( $event_date != '' ){
	$event_date = strtotime( $event_date );
}
$start_hour = get_post_meta( get_the_ID(), 'event_start_hour', true );
$start_minute = get_post_meta( get_the_ID(), 'event_start_minute', true );
$start_meridian = get_post_meta( get_the_ID(), 'event_start_meridian', true );
$venue_name = get_post_meta( get_the_ID(), 'venue_name', true );

$target = '';
$event_source_url = esc_url( get_permalink() );
if ('yes' === $direct_link) {
	$event_origin = get_post_meta( get_the_ID(), 'wpea_event_origin', true );
    if ( $event_origin =='facebook' ) {
        $facebook_event_id = get_post_meta(get_the_ID(), 'wpea_event_id', true);
        $event_source_url = "https://www.facebook.com/events/". $facebook_event_id;
    } elseif( $event_origin =='eventbrite' ) {
        $eventbrite_event_id = get_post_meta(get_the_ID(), 'wpea_event_id', true);
        $event_source_url = "https://www.eventbrite.com/e/". $eventbrite_event_id;
    } elseif($event_origin =='meetup') {
        $meetup_organizer_link = get_post_meta(get_the_ID(), 'organizer_url', true);
        $event_source_url = $meetup_organizer_link .'events/'.get_post_meta(get_the_ID(), 'wpea_event_id', true);
    } elseif($event_origin =='ical') {
        $event_source_url = get_post_meta(get_the_ID(), 'wpea_event_link', true);
	}
	if( empty($event_source_url )){
		$event_source_url = esc_url( get_permalink() );
	}
    $target = 'target="_blank"';
}
?>

<div <?php post_class( array( 'wpea-summary-item' ) ); ?>>
	<div class="wpea-summary-content">
		<div class="wpea-summary-date">
			<div class="wpea-summary-month"><?php echo esc_attr( date_i18n( 'M', $event_date ) ); ?></div>
			<div class="wpea-summary-day"><?php echo esc_attr( date_i18n( 'd', $event_date ) ); ?></div>
		</div>
		<div class="wpea-summary-details">
			<h3 class="wpea-summary-title">
				<a href="<?php echo esc_url( $event_source_url ); ?>" <?php echo esc_attr( $target ); ?>>
					<?php the_title(); ?>
				</a>
			</h3>
			<div class="wpea-summary-meta">
				<?php if ( $start_hour ): ?>
				<span class="wpea-summary-time">
					<i class="fa fa-clock-o"></i> <?php echo esc_html($start_hour . ':' . $start_minute . ' ' . strtoupper($start_meridian)); ?>
				</span>
				<?php endif; ?>
				<?php if ( $venue_name ): ?>
				<span class="wpea-summary-venue">
					<i class="fa fa-map-marker"></i> <?php echo esc_html( $venue_name ); ?>
				</span>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
