<?php
/*
Plugin Name: Mosque Subscriptions
Description: Manage Free vs Paid (yearly) mosque subscriptions with capability gating, admin management, and Stripe integration.
Version: 1.0.0
Author: Mosque Subscriptions Team
License: GPL2+
*/

if (!defined('ABSPATH')) {
	exit;
}

define('MOSQUE_SUBS_VERSION', '1.0.0');
define('MOSQUE_SUBS_DIR', plugin_dir_path(__FILE__));
define('MOSQUE_SUBS_URL', plugin_dir_url(__FILE__));

// Includes
require_once MOSQUE_SUBS_DIR . 'includes/helpers.php';
require_once MOSQUE_SUBS_DIR . 'includes/capabilities.php';
require_once MOSQUE_SUBS_DIR . 'includes/stripe.php';
require_once MOSQUE_SUBS_DIR . 'includes/shortcode.php';
require_once MOSQUE_SUBS_DIR . 'includes/admin-settings.php';
require_once MOSQUE_SUBS_DIR . 'includes/subscribers-list-table.php';
require_once MOSQUE_SUBS_DIR . 'includes/cron.php';
require_once MOSQUE_SUBS_DIR . 'includes/emails.php';
require_once MOSQUE_SUBS_DIR . 'includes/registration.php';

register_activation_hook(__FILE__, 'mosque_subs_on_activation');
register_deactivation_hook(__FILE__, 'mosque_subs_on_deactivation');

/**
 * Plugin activation callback
 */
function mosque_subs_on_activation() {
	// Ensure options exist with defaults
	$opts = mosque_subs_get_options();
	if (empty($opts)) {
		mosque_subs_update_options(mosque_subs_default_options());
	}

	// Add capability to Administrator
	$admin = get_role('administrator');
	if ($admin && !$admin->has_cap('manage_mosque_subscriptions')) {
		$admin->add_cap('manage_mosque_subscriptions');
	}

	// Sync masjid role caps
	if (function_exists('mosque_subs_sync_masjid_caps')) {
		mosque_subs_sync_masjid_caps();
	}

	// Schedule daily cron if not set
	if (!wp_next_scheduled('mosque_subs_daily_event')) {
		wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'mosque_subs_daily_event');
	}
}

/**
 * Plugin deactivation callback
 */
function mosque_subs_on_deactivation() {
	// Clear cron
	$timestamp = wp_next_scheduled('mosque_subs_daily_event');
	if ($timestamp) {
		wp_unschedule_event($timestamp, 'mosque_subs_daily_event');
	}
}

// Admin menu pages
add_action('admin_menu', function() {
	add_menu_page(
		__('Mosque Subscriptions', 'mosque-subs'),
		__('Mosque Subscriptions', 'mosque-subs'),
		'manage_mosque_subscriptions',
		'mosque-subs',
		'mosque_subs_render_settings_page',
		'dashicons-groups',
		58
	);

	add_submenu_page(
		'mosque-subs',
		__('Settings', 'mosque-subs'),
		__('Settings', 'mosque-subs'),
		'manage_mosque_subscriptions',
		'mosque-subs',
		'mosque_subs_render_settings_page'
	);

	add_submenu_page(
		'mosque-subs',
		__('Subscribers', 'mosque-subs'),
		__('Subscribers', 'mosque-subs'),
		'manage_mosque_subscriptions',
		'mosque-subs-subscribers',
		'mosque_subs_render_subscribers_page'
	);
});

// Handle Stripe webhooks via query var
add_action('init', function() {
	if (isset($_GET['mosque_stripe_webhook'])) {
		mosque_subs_handle_webhook();
		exit;
	}
});

// Handle Checkout redirection via query var
add_action('template_redirect', function() {
	if (!is_user_logged_in()) {
		return;
	}
	if (isset($_GET['mosque_subs_checkout']) && $_GET['mosque_subs_checkout'] == '1') {
		$session = mosque_subs_create_checkout_session(get_current_user_id());
		if (is_wp_error($session)) {
			wp_die(esc_html($session->get_error_message()));
		}
		if (!empty($session['url'])) {
			wp_redirect($session['url']);
			exit;
		}
	}
});

// Daily cron task
add_action('mosque_subs_daily_event', 'mosque_subs_cron_scan_expired');

// Shortcode registration
add_shortcode('masjid_subscription_status', 'mosque_subs_render_status_shortcode');

?>

