<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Default plugin options
 */
function mosque_subs_default_options() {
	return array(
		'duration_value' => 12,
		'duration_unit' => 'months', // 'months' or 'years'
		'allowed_cpts' => array('masjid-to-masjid', 'live-feed', 'donations'),
		'allowed_taxonomies' => array('from-masjid-to-masjid-category'),
		'hide_add_new' => true,
		'email_templates' => array(
			'pre_7' => 'Assalamu Alaikum {user_login}, your subscription at {site_name} will expire on {expiry_date}. Renew: {renew_url}',
			'pre_3' => 'Reminder: your mosque subscription expires on {expiry_date}. Renew: {renew_url}',
			'pre_1' => 'Last reminder: subscription expires tomorrow ({expiry_date}). Renew: {renew_url}',
			'on_expiry' => 'Your subscription expired on {expiry_date}. Renew anytime: {renew_url}',
			'renewal_confirmation' => 'JazakAllahu khairan, your subscription is renewed until {expiry_date}.',
		),
		'email_subjects' => array(
			'pre_7' => 'Subscription expiring in 7 days',
			'pre_3' => 'Subscription expiring in 3 days',
			'pre_1' => 'Subscription expires tomorrow',
			'on_expiry' => 'Subscription expired',
			'renewal_confirmation' => 'Subscription renewed',
		),
		'stripe' => array(
			'publishable_key' => '',
			'secret_key' => '',
			'webhook_secret' => '',
			'currency' => 'usd',
			'price_id' => '',
		),
	);
}

function mosque_subs_get_options() {
	$opts = get_option('mosque_subs_options', array());
	if (!is_array($opts)) {
		$opts = array();
	}
	return wp_parse_args($opts, mosque_subs_default_options());
}

function mosque_subs_update_options($options) {
	if (!is_array($options)) {
		return false;
	}
	$merged = wp_parse_args($options, mosque_subs_get_options());
	update_option('mosque_subs_options', $merged);
	return true;
}

function mosque_subs_allowed_cpts() {
	$opts = mosque_subs_get_options();
	$allowed = isset($opts['allowed_cpts']) && is_array($opts['allowed_cpts']) ? $opts['allowed_cpts'] : array();
	return array_values(array_unique(array_filter($allowed)));
}

function mosque_subs_allowed_taxonomies() {
	$opts = mosque_subs_get_options();
	$allowed = isset($opts['allowed_taxonomies']) && is_array($opts['allowed_taxonomies']) ? $opts['allowed_taxonomies'] : array();
	return array_values(array_unique(array_filter($allowed)));
}

/**
 * Check if user has active Pro subscription
 */
function mosque_subs_is_active($user_id) {
	$user_id = intval($user_id);
	if ($user_id <= 0) return false;
	$plan = get_user_meta($user_id, 'masjid_plan', true);
	$expiry = get_user_meta($user_id, 'masjid_expiry', true);
	if ($plan !== 'pro') return false;
	if (empty($expiry)) return false;
	$today = current_time('Y-m-d');
	return $today <= $expiry;
}

/**
 * Activate or extend Pro plan by configured duration
 * $duration can be array('value'=>12,'unit'=>'months') or null for defaults
 */
function mosque_subs_activate_pro($user_id, $duration = null) {
	$user_id = intval($user_id);
	if ($user_id <= 0) return new WP_Error('invalid_user', 'Invalid user');
	if ($duration === null) {
		$opts = mosque_subs_get_options();
		$duration = array(
			'value' => intval($opts['duration_value']),
			'unit' => $opts['duration_unit'],
		);
	}
	$current_expiry = get_user_meta($user_id, 'masjid_expiry', true);
	$base = current_time('timestamp');
	if (!empty($current_expiry)) {
		$existing_ts = strtotime($current_expiry . ' 23:59:59', current_time('timestamp'));
		if ($existing_ts && $existing_ts > $base) {
			$base = $existing_ts;
		}
	}
	$interval_spec = '+' . intval($duration['value']) . ' ' . sanitize_text_field($duration['unit']);
	$new_ts = strtotime($interval_spec, $base);
	$new_date = gmdate('Y-m-d', $new_ts);
	update_user_meta($user_id, 'masjid_plan', 'pro');
	update_user_meta($user_id, 'masjid_expiry', $new_date);
	return $new_date;
}

function mosque_subs_downgrade_to_free($user_id) {
	$user_id = intval($user_id);
	if ($user_id <= 0) return false;
	update_user_meta($user_id, 'masjid_plan', 'free');
	return true;
}

function mosque_subs_user_is_masjid($user_id) {
	$user = get_user_by('id', $user_id);
	if (!$user) return false;
	return in_array('masjid', (array)$user->roles, true);
}

function mosque_subs_logger($message, $context = array()) {
	$line = '[' . date('c') . '] ' . (is_string($message) ? $message : wp_json_encode($message));
	if (!empty($context)) {
		$line .= ' ' . wp_json_encode($context);
	}
	// Log to debug.log if WP_DEBUG_LOG enabled
	if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
		error_log('[mosque-subs] ' . $line);
	}
}

function mosque_subs_current_site_name() {
	return get_bloginfo('name');
}

function mosque_subs_build_renew_url() {
	return add_query_arg('mosque_subs_checkout', '1', home_url('/'));
}

?>

