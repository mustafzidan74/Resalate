<?php
if (!defined('ABSPATH')) {
	exit;
}

function mosque_subs_render_settings_page() {
	if (!current_user_can('manage_mosque_subscriptions')) {
		wp_die(__('You do not have permission to access this page.', 'mosque-subs'));
	}

	if (isset($_POST['mosque_subs_save_settings'])) {
		check_admin_referer('mosque_subs_save_settings');
		$opts = mosque_subs_get_options();
		$opts['duration_value'] = max(1, intval($_POST['duration_value']));
		$opts['duration_unit'] = in_array($_POST['duration_unit'], array('months', 'years'), true) ? $_POST['duration_unit'] : 'months';
		$opts['allowed_cpts'] = isset($_POST['allowed_cpts']) ? array_map('sanitize_text_field', (array)$_POST['allowed_cpts']) : array();
		$opts['allowed_taxonomies'] = isset($_POST['allowed_taxonomies']) ? array_map('sanitize_text_field', (array)$_POST['allowed_taxonomies']) : array();
		$opts['hide_add_new'] = !empty($_POST['hide_add_new']);
		$opts['stripe']['publishable_key'] = sanitize_text_field($_POST['stripe_publishable_key']);
		$opts['stripe']['secret_key'] = sanitize_text_field($_POST['stripe_secret_key']);
		$opts['stripe']['webhook_secret'] = sanitize_text_field($_POST['stripe_webhook_secret']);
		$opts['stripe']['currency'] = sanitize_text_field($_POST['stripe_currency']);
		$opts['stripe']['price_id'] = sanitize_text_field($_POST['stripe_price_id']);
		// Email templates
		foreach (array('pre_7','pre_3','pre_1','on_expiry','renewal_confirmation') as $key) {
			if (isset($_POST['email_templates'][$key])) {
				$opts['email_templates'][$key] = wp_kses_post($_POST['email_templates'][$key]);
			}
			if (isset($_POST['email_subjects'][$key])) {
				$opts['email_subjects'][$key] = sanitize_text_field($_POST['email_subjects'][$key]);
			}
		}
		mosque_subs_update_options($opts);
		if (function_exists('mosque_subs_sync_masjid_caps')) {
			mosque_subs_sync_masjid_caps();
		}
		add_settings_error('mosque_subs', 'settings_saved', __('Settings saved.', 'mosque-subs'), 'updated');
	}

	$opts = mosque_subs_get_options();
	$cpts = get_post_types(array('public' => true), 'names', 'and');
	$taxes = get_taxonomies(array(), 'names');
	settings_errors('mosque_subs');
	?>
	<div class="wrap">
		<h1><?php echo esc_html__('Mosque Subscriptions - Settings', 'mosque-subs'); ?></h1>
		<form method="post">
			<?php wp_nonce_field('mosque_subs_save_settings'); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php echo esc_html__('Default duration', 'mosque-subs'); ?></th>
					<td>
						<input type="number" min="1" name="duration_value" value="<?php echo esc_attr($opts['duration_value']); ?>" />
						<select name="duration_unit">
							<option value="months" <?php selected($opts['duration_unit'], 'months'); ?>><?php echo esc_html__('Months', 'mosque-subs'); ?></option>
							<option value="years" <?php selected($opts['duration_unit'], 'years'); ?>><?php echo esc_html__('Years', 'mosque-subs'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__('Allowed CPTs', 'mosque-subs'); ?></th>
					<td>
						<select name="allowed_cpts[]" multiple size="6" style="min-width:280px;">
							<?php foreach ($cpts as $cpt) : ?>
								<option value="<?php echo esc_attr($cpt); ?>" <?php selected(in_array($cpt, $opts['allowed_cpts'], true)); ?>><?php echo esc_html($cpt); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__('Allowed Taxonomies', 'mosque-subs'); ?></th>
					<td>
						<select name="allowed_taxonomies[]" multiple size="6" style="min-width:280px;">
							<?php foreach ($taxes as $tax) : ?>
								<option value="<?php echo esc_attr($tax); ?>" <?php selected(in_array($tax, $opts['allowed_taxonomies'], true)); ?>><?php echo esc_html($tax); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__('Hide Add New for expired/non-pro', 'mosque-subs'); ?></th>
					<td>
						<label><input type="checkbox" name="hide_add_new" value="1" <?php checked(!empty($opts['hide_add_new'])); ?> /> <?php echo esc_html__('Enable', 'mosque-subs'); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__('Stripe', 'mosque-subs'); ?></th>
					<td>
						<p><label><?php echo esc_html__('Publishable key', 'mosque-subs'); ?> <input type="text" name="stripe_publishable_key" value="<?php echo esc_attr($opts['stripe']['publishable_key']); ?>" class="regular-text" /></label></p>
						<p><label><?php echo esc_html__('Secret key', 'mosque-subs'); ?> <input type="text" name="stripe_secret_key" value="<?php echo esc_attr($opts['stripe']['secret_key']); ?>" class="regular-text" /></label></p>
						<p><label><?php echo esc_html__('Webhook secret', 'mosque-subs'); ?> <input type="text" name="stripe_webhook_secret" value="<?php echo esc_attr($opts['stripe']['webhook_secret']); ?>" class="regular-text" /></label></p>
						<p><label><?php echo esc_html__('Currency', 'mosque-subs'); ?> <input type="text" name="stripe_currency" value="<?php echo esc_attr($opts['stripe']['currency']); ?>" class="small-text" /></label></p>
						<p><label><?php echo esc_html__('Price ID', 'mosque-subs'); ?> <input type="text" name="stripe_price_id" value="<?php echo esc_attr($opts['stripe']['price_id']); ?>" class="regular-text" /></label></p>
						<p><?php echo esc_html__('Webhook endpoint:', 'mosque-subs'); ?> <code><?php echo esc_html(add_query_arg('mosque_stripe_webhook', '1', home_url('/'))); ?></code></p>
					</td>
				</tr>
			</table>

			<h2><?php echo esc_html__('Email Templates', 'mosque-subs'); ?></h2>
			<p><?php echo esc_html__('Available tags: {site_name}, {user_login}, {expiry_date}, {renew_url}', 'mosque-subs'); ?></p>
			<table class="form-table" role="presentation">
				<?php foreach (array('pre_7'=>'7 days before','pre_3'=>'3 days before','pre_1'=>'1 day before','on_expiry'=>'On expiry','renewal_confirmation'=>'Renewal confirmation') as $key=>$label) : ?>
				<tr>
					<th scope="row"><?php echo esc_html($label); ?></th>
					<td>
						<p><input type="text" name="email_subjects[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($opts['email_subjects'][$key]); ?>" class="regular-text" placeholder="<?php echo esc_attr__('Subject', 'mosque-subs'); ?>" /></p>
						<p><textarea name="email_templates[<?php echo esc_attr($key); ?>]" rows="4" cols="60" class="large-text code"><?php echo esc_textarea($opts['email_templates'][$key]); ?></textarea></p>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>

			<p class="submit"><button type="submit" name="mosque_subs_save_settings" class="button button-primary"><?php echo esc_html__('Save Changes', 'mosque-subs'); ?></button></p>
		</form>
	</div>
	<?php
}

?>

