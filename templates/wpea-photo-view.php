<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$event_date = get_post_meta( get_the_ID(), 'event_start_date', true );
if( $event_date != '' ){
	$event_date = strtotime( $event_date );
}
$venue_name = get_post_meta( get_the_ID(), 'venue_name', true );

$image_url = array();
if ( '' !== get_the_post_thumbnail() ){
	$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' );
}else{
	$start_date_str = get_post_meta( get_the_ID(), 'start_ts', true );
	$image_date = date_i18n( 'F+d', $start_date_str );
	$image_url[] = 'https://dummyimage.com/600x400/ccc/969696.png&text=' . $image_date;
}

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

<div <?php post_class( array( $css_class, 'wpea-photo-item' ) ); ?>>
	<div class="wpea-photo-card">
		<div class="wpea-photo-image" style="background-image: url('<?php echo esc_url( $image_url[0] ); ?>');">
			<a href="<?php echo esc_url( $event_source_url ); ?>" <?php echo esc_attr( $target ); ?>></a>
			<div class="wpea-photo-overlay">
				<div class="wpea-photo-date">
					<span class="wpea-photo-month"><?php echo esc_attr( date_i18n( 'M', $event_date ) ); ?></span>
					<span class="wpea-photo-day"><?php echo esc_attr( date_i18n( 'd', $event_date ) ); ?></span>
				</div>
			</div>
		</div>
		<div class="wpea-photo-content">
			<h3 class="wpea-photo-title">
				<a href="<?php echo esc_url( $event_source_url ); ?>" <?php echo esc_attr( $target ); ?>>
					<?php the_title(); ?>
				</a>
			</h3>
			<?php if( $venue_name ): ?>
			<div class="wpea-photo-venue">
				<i class="fa fa-map-marker"></i> <?php echo esc_html( $venue_name ); ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
