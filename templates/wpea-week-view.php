<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$week_start = isset( $week_start ) ? $week_start : strtotime('monday this week');
$week_end = strtotime('+6 days', $week_start);
$days_of_week = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
?>

<div class="wpea-week-view">
	<div class="wpea-week-header">
		<button class="wpea-week-prev" data-week="<?php echo esc_attr($week_start); ?>">
			<i class="fa fa-chevron-left"></i> Previous Week
		</button>
		<h2 class="wpea-week-title">
			<?php echo date_i18n( 'M d, Y', $week_start ) . ' - ' . date_i18n( 'M d, Y', $week_end ); ?>
		</h2>
		<button class="wpea-week-next" data-week="<?php echo esc_attr($week_start); ?>">
			Next Week <i class="fa fa-chevron-right"></i>
		</button>
	</div>

	<div class="wpea-week-grid">
		<div class="wpea-week-days">
			<?php for ($i = 0; $i < 7; $i++):
				$current_day = strtotime("+{$i} days", $week_start);
				$is_today = date('Y-m-d', $current_day) === date('Y-m-d');
			?>
			<div class="wpea-day-column <?php echo $is_today ? 'is-today' : ''; ?>">
				<div class="wpea-day-header">
					<span class="wpea-day-name"><?php echo $days_of_week[$i]; ?></span>
					<span class="wpea-day-date"><?php echo date_i18n('d', $current_day); ?></span>
				</div>
				<div class="wpea-day-events">
					<?php
					if ( $wp_events->have_posts() ) {
						while ( $wp_events->have_posts() ) {
							$wp_events->the_post();
							$event_start = get_post_meta( get_the_ID(), 'event_start_date', true );
							$event_start_ts = strtotime($event_start);

							if (date('Y-m-d', $event_start_ts) === date('Y-m-d', $current_day)) {
								$start_hour = get_post_meta( get_the_ID(), 'event_start_hour', true );
								$start_minute = get_post_meta( get_the_ID(), 'event_start_minute', true );
								$start_meridian = get_post_meta( get_the_ID(), 'event_start_meridian', true );
								$venue = get_post_meta( get_the_ID(), 'venue_name', true );
								?>
								<div class="wpea-week-event">
									<div class="wpea-week-event-time">
										<?php echo esc_html($start_hour . ':' . $start_minute . ' ' . strtoupper($start_meridian)); ?>
									</div>
									<div class="wpea-week-event-title">
										<a href="<?php echo esc_url(get_permalink()); ?>">
											<?php the_title(); ?>
										</a>
									</div>
									<?php if ($venue): ?>
									<div class="wpea-week-event-venue">
										<i class="fa fa-map-marker"></i> <?php echo esc_html($venue); ?>
									</div>
									<?php endif; ?>
								</div>
								<?php
							}
						}
						$wp_events->rewind_posts();
					}
					?>
				</div>
			</div>
			<?php endfor; ?>
		</div>
	</div>
</div>
