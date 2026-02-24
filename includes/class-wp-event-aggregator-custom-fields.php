<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Event_Aggregator_Custom_Fields {

	public function __construct() {
		add_action( 'add_meta_boxes', array($this, 'add_custom_fields_meta_box' ), 20 );
		add_action( 'save_post', array($this, 'save_custom_fields'), 10, 2);
		add_action( 'admin_menu', array($this, 'add_custom_fields_menu' ) );
		add_action( 'admin_post_wpea_save_custom_field', array($this, 'handle_save_custom_field' ) );
		add_action( 'admin_post_wpea_delete_custom_field', array($this, 'handle_delete_custom_field' ) );
	}

	public function add_custom_fields_menu() {
		add_submenu_page(
			'edit.php?post_type=wp_events',
			__('Custom Fields', 'wp-event-aggregator'),
			__('Custom Fields', 'wp-event-aggregator'),
			'manage_options',
			'wpea-custom-fields',
			array($this, 'render_custom_fields_page')
		);
	}

	public function render_custom_fields_page() {
		$fields = $this->get_all_custom_fields();
		?>
		<div class="wrap">
			<h1><?php esc_html_e('Event Custom Fields', 'wp-event-aggregator'); ?></h1>

			<div class="wpea-custom-fields-container">
				<div class="wpea-custom-fields-form">
					<h2><?php esc_html_e('Add New Custom Field', 'wp-event-aggregator'); ?></h2>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
						<input type="hidden" name="action" value="wpea_save_custom_field">
						<?php wp_nonce_field('wpea_custom_field_action', 'wpea_custom_field_nonce'); ?>

						<table class="form-table">
							<tr>
								<th scope="row"><label for="field_name"><?php esc_html_e('Field Name', 'wp-event-aggregator'); ?></label></th>
								<td><input type="text" name="field_name" id="field_name" class="regular-text" required></td>
							</tr>
							<tr>
								<th scope="row"><label for="field_label"><?php esc_html_e('Field Label', 'wp-event-aggregator'); ?></label></th>
								<td><input type="text" name="field_label" id="field_label" class="regular-text" required></td>
							</tr>
							<tr>
								<th scope="row"><label for="field_type"><?php esc_html_e('Field Type', 'wp-event-aggregator'); ?></label></th>
								<td>
									<select name="field_type" id="field_type">
										<option value="text"><?php esc_html_e('Text', 'wp-event-aggregator'); ?></option>
										<option value="textarea"><?php esc_html_e('Textarea', 'wp-event-aggregator'); ?></option>
										<option value="number"><?php esc_html_e('Number', 'wp-event-aggregator'); ?></option>
										<option value="email"><?php esc_html_e('Email', 'wp-event-aggregator'); ?></option>
										<option value="url"><?php esc_html_e('URL', 'wp-event-aggregator'); ?></option>
										<option value="date"><?php esc_html_e('Date', 'wp-event-aggregator'); ?></option>
										<option value="checkbox"><?php esc_html_e('Checkbox', 'wp-event-aggregator'); ?></option>
										<option value="select"><?php esc_html_e('Select', 'wp-event-aggregator'); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="is_required"><?php esc_html_e('Required', 'wp-event-aggregator'); ?></label></th>
								<td><input type="checkbox" name="is_required" id="is_required" value="1"></td>
							</tr>
						</table>

						<p class="submit">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Add Custom Field', 'wp-event-aggregator'); ?>">
						</p>
					</form>
				</div>

				<div class="wpea-custom-fields-list">
					<h2><?php esc_html_e('Existing Custom Fields', 'wp-event-aggregator'); ?></h2>
					<?php if (!empty($fields)): ?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e('Field Name', 'wp-event-aggregator'); ?></th>
								<th><?php esc_html_e('Label', 'wp-event-aggregator'); ?></th>
								<th><?php esc_html_e('Type', 'wp-event-aggregator'); ?></th>
								<th><?php esc_html_e('Required', 'wp-event-aggregator'); ?></th>
								<th><?php esc_html_e('Actions', 'wp-event-aggregator'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($fields as $field): ?>
							<tr>
								<td><?php echo esc_html($field->field_name); ?></td>
								<td><?php echo esc_html($field->field_label); ?></td>
								<td><?php echo esc_html($field->field_type); ?></td>
								<td><?php echo $field->is_required ? esc_html__('Yes', 'wp-event-aggregator') : esc_html__('No', 'wp-event-aggregator'); ?></td>
								<td>
									<a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=wpea_delete_custom_field&field_id=' . $field->id), 'wpea_delete_field_' . $field->id)); ?>"
									   onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this field?', 'wp-event-aggregator'); ?>');"
									   class="button button-small">
										<?php esc_html_e('Delete', 'wp-event-aggregator'); ?>
									</a>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php else: ?>
					<p><?php esc_html_e('No custom fields found. Add your first custom field above.', 'wp-event-aggregator'); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function handle_save_custom_field() {
		if (!isset($_POST['wpea_custom_field_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpea_custom_field_nonce'])), 'wpea_custom_field_action')) {
			wp_die(esc_html__('Security check failed', 'wp-event-aggregator'));
		}

		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('Permission denied', 'wp-event-aggregator'));
		}

		$field_name = isset($_POST['field_name']) ? sanitize_title(wp_unslash($_POST['field_name'])) : '';
		$field_label = isset($_POST['field_label']) ? sanitize_text_field(wp_unslash($_POST['field_label'])) : '';
		$field_type = isset($_POST['field_type']) ? sanitize_text_field(wp_unslash($_POST['field_type'])) : 'text';
		$is_required = isset($_POST['is_required']) ? 1 : 0;

		if (empty($field_name) || empty($field_label)) {
			wp_redirect(add_query_arg('error', 'missing_fields', admin_url('edit.php?post_type=wp_events&page=wpea-custom-fields')));
			exit;
		}

		$result = $this->save_custom_field_to_db($field_name, $field_label, $field_type, $is_required);

		if ($result) {
			wp_redirect(add_query_arg('success', 'field_added', admin_url('edit.php?post_type=wp_events&page=wpea-custom-fields')));
		} else {
			wp_redirect(add_query_arg('error', 'save_failed', admin_url('edit.php?post_type=wp_events&page=wpea-custom-fields')));
		}
		exit;
	}

	public function handle_delete_custom_field() {
		$field_id = isset($_GET['field_id']) ? sanitize_text_field(wp_unslash($_GET['field_id'])) : '';

		if (empty($field_id) || !isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'wpea_delete_field_' . $field_id)) {
			wp_die(esc_html__('Security check failed', 'wp-event-aggregator'));
		}

		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('Permission denied', 'wp-event-aggregator'));
		}

		$result = $this->delete_custom_field_from_db($field_id);

		if ($result) {
			wp_redirect(add_query_arg('success', 'field_deleted', admin_url('edit.php?post_type=wp_events&page=wpea-custom-fields')));
		} else {
			wp_redirect(add_query_arg('error', 'delete_failed', admin_url('edit.php?post_type=wp_events&page=wpea-custom-fields')));
		}
		exit;
	}

	private function get_supabase_client() {
		$supabase_url = getenv('SUPABASE_URL');
		$supabase_key = getenv('SUPABASE_ANON_KEY');

		if (!$supabase_url || !$supabase_key) {
			return false;
		}

		return array('url' => $supabase_url, 'key' => $supabase_key);
	}

	private function save_custom_field_to_db($field_name, $field_label, $field_type, $is_required) {
		$client = $this->get_supabase_client();
		if (!$client) {
			return false;
		}

		$data = array(
			'field_name' => $field_name,
			'field_label' => $field_label,
			'field_type' => $field_type,
			'is_required' => $is_required
		);

		$response = wp_remote_post($client['url'] . '/rest/v1/wpea_custom_fields', array(
			'headers' => array(
				'apikey' => $client['key'],
				'Authorization' => 'Bearer ' . $client['key'],
				'Content-Type' => 'application/json'
			),
			'body' => wp_json_encode($data)
		));

		return !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 201;
	}

	private function delete_custom_field_from_db($field_id) {
		$client = $this->get_supabase_client();
		if (!$client) {
			return false;
		}

		$response = wp_remote_request($client['url'] . '/rest/v1/wpea_custom_fields?id=eq.' . urlencode($field_id), array(
			'method' => 'DELETE',
			'headers' => array(
				'apikey' => $client['key'],
				'Authorization' => 'Bearer ' . $client['key']
			)
		));

		return !is_wp_error($response) && in_array(wp_remote_retrieve_response_code($response), array(200, 204), true);
	}

	public function get_all_custom_fields() {
		$client = $this->get_supabase_client();
		if (!$client) {
			return array();
		}

		$response = wp_remote_get($client['url'] . '/rest/v1/wpea_custom_fields?order=field_order.asc', array(
			'headers' => array(
				'apikey' => $client['key'],
				'Authorization' => 'Bearer ' . $client['key']
			)
		));

		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			return array();
		}

		$body = wp_remote_retrieve_body($response);
		return json_decode($body);
	}

	public function add_custom_fields_meta_box() {
		$fields = $this->get_all_custom_fields();
		if (!empty($fields)) {
			add_meta_box(
				'wpea_custom_fields_metabox',
				__( 'Custom Event Fields', 'wp-event-aggregator' ),
				array($this, 'render_custom_fields_meta_box'),
				'wp_events',
				'normal',
				'high'
			);
		}
	}

	public function render_custom_fields_meta_box( $post ) {
		wp_nonce_field( 'wpea_custom_fields_meta', 'wpea_custom_fields_nonce' );

		$fields = $this->get_all_custom_fields();
		if (empty($fields)) {
			echo '<p>' . esc_html__('No custom fields defined.', 'wp-event-aggregator') . '</p>';
			return;
		}

		foreach ($fields as $field) {
			$field_value = get_post_meta($post->ID, 'wpea_cf_' . $field->field_name, true);
			$required = $field->is_required ? 'required' : '';
			?>
			<div class="wpea_form_row">
				<label for="<?php echo esc_attr('wpea_cf_' . $field->field_name); ?>">
					<?php echo esc_html($field->field_label); ?>
					<?php if ($field->is_required): ?><span class="required">*</span><?php endif; ?>:
				</label>
				<div class="wpea_form_input_group">
					<?php
					switch ($field->field_type) {
						case 'textarea':
							?>
							<textarea name="<?php echo esc_attr('wpea_cf_' . $field->field_name); ?>"
									  id="<?php echo esc_attr('wpea_cf_' . $field->field_name); ?>"
									  rows="4"
									  <?php echo esc_attr($required); ?>><?php echo esc_textarea($field_value); ?></textarea>
							<?php
							break;
						case 'checkbox':
							?>
							<input type="checkbox"
								   name="<?php echo esc_attr('wpea_cf_' . $field->field_name); ?>"
								   id="<?php echo esc_attr('wpea_cf_' . $field->field_name); ?>"
								   value="1"
								   <?php checked($field_value, '1'); ?>>
							<?php
							break;
						default:
							?>
							<input type="<?php echo esc_attr($field->field_type); ?>"
								   name="<?php echo esc_attr('wpea_cf_' . $field->field_name); ?>"
								   id="<?php echo esc_attr('wpea_cf_' . $field->field_name); ?>"
								   value="<?php echo esc_attr($field_value); ?>"
								   <?php echo esc_attr($required); ?>>
							<?php
							break;
					}
					?>
				</div>
			</div>
			<?php
		}
	}

	public function save_custom_fields($post_id, $post) {
		if (!isset($_POST['wpea_custom_fields_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpea_custom_fields_nonce'])), 'wpea_custom_fields_meta')) {
			return $post_id;
		}

		if (!current_user_can("edit_post", $post_id)) {
			return $post_id;
		}

		if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
			return $post_id;
		}

		if ($post->post_type != 'wp_events') {
			return $post_id;
		}

		$fields = $this->get_all_custom_fields();
		foreach ($fields as $field) {
			$field_name = 'wpea_cf_' . $field->field_name;
			if (isset($_POST[$field_name])) {
				$field_value = $field->field_type === 'textarea'
					? sanitize_textarea_field(wp_unslash($_POST[$field_name]))
					: sanitize_text_field(wp_unslash($_POST[$field_name]));
				update_post_meta($post_id, $field_name, $field_value);
			} else {
				delete_post_meta($post_id, $field_name);
			}
		}
	}
}
