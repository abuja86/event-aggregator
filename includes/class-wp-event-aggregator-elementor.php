<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WPEA_Elementor_Widget' ) && did_action( 'elementor/loaded' ) ) :

class WPEA_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpea_events';
	}

	public function get_title() {
		return __('WP Events', 'wp-event-aggregator');
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return ['general'];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Content', 'wp-event-aggregator'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __('Layout', 'wp-event-aggregator'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'style1',
				'options' => [
					'style1' => __('Style 1', 'wp-event-aggregator'),
					'style2' => __('Style 2', 'wp-event-aggregator'),
					'style3' => __('Style 3', 'wp-event-aggregator'),
					'style4' => __('Style 4', 'wp-event-aggregator'),
					'photo' => __('Photo View', 'wp-event-aggregator'),
					'summary' => __('Summary View', 'wp-event-aggregator'),
					'week' => __('Week View', 'wp-event-aggregator'),
					'map' => __('Map View', 'wp-event-aggregator'),
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __('Number of Events', 'wp-event-aggregator'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 12,
				'min' => 1,
				'max' => 100,
			]
		);

		$this->add_control(
			'col',
			[
				'label' => __('Columns', 'wp-event-aggregator'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'1' => __('1 Column', 'wp-event-aggregator'),
					'2' => __('2 Columns', 'wp-event-aggregator'),
					'3' => __('3 Columns', 'wp-event-aggregator'),
					'4' => __('4 Columns', 'wp-event-aggregator'),
				],
			]
		);

		$this->add_control(
			'category',
			[
				'label' => __('Categories', 'wp-event-aggregator'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __('cat1,cat2,cat3', 'wp-event-aggregator'),
				'description' => __('Enter category slugs separated by commas', 'wp-event-aggregator'),
			]
		);

		$this->add_control(
			'past_events',
			[
				'label' => __('Show Past Events', 'wp-event-aggregator'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'wp-event-aggregator'),
				'label_off' => __('No', 'wp-event-aggregator'),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __('Order', 'wp-event-aggregator'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC' => __('Ascending', 'wp-event-aggregator'),
					'DESC' => __('Descending', 'wp-event-aggregator'),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __('Style', 'wp-event-aggregator'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label' => __('Accent Color', 'wp-event-aggregator'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#039ED7',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$atts = array(
			'layout' => $settings['layout'],
			'posts_per_page' => $settings['posts_per_page'],
			'col' => $settings['col'],
			'category' => $settings['category'],
			'past_events' => $settings['past_events'],
			'order' => $settings['order'],
		);

		global $importevents;
		if (isset($importevents->cpt)) {
			echo $importevents->cpt->wp_events_archive($atts);
		} else {
			echo do_shortcode('[wp_events ' . http_build_query($atts, '', ' ') . ']');
		}
	}
}

endif;

function wpea_register_elementor_widgets() {
	if (!did_action('elementor/loaded')) {
		return;
	}

	if (!wpea_is_pro()) {
		return;
	}

	if ( class_exists( 'WPEA_Elementor_Widget' ) ) {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new WPEA_Elementor_Widget());
	}
}
add_action('elementor/widgets/widgets_registered', 'wpea_register_elementor_widgets');
