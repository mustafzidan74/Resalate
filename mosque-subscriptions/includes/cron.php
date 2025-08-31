<?php
if (!defined('ABSPATH')) {
	exit;
}

function mosque_subs_cron_scan_expired() {
	$args = array(
		'role' => 'masjid',
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'masjid_plan',
				'value' => 'pro',
				'compare' => '=',
			),
			array(
				'key' => 'masjid_expiry',
				'value' => current_time('Y-m-d'),
				'compare' => '<',
				'type' => 'DATE'
			),
		),
		'number' => 500,
	);
	$user_query = new WP_User_Query($args);
	$users = $user_query->get_results();
	foreach ($users as $user) {
		mosque_subs_downgrade_to_free($user->ID);
		mosque_subs_send_on_expiry($user->ID);
		mosque_subs_logger('User downgraded due to expiry', array('user_id' => $user->ID));
	}

	mosque_subs_schedule_reminders();
}

function mosque_subs_schedule_reminders() {
	// For all pro users, schedule emails for 7/3/1 days before expiry if the date is upcoming
	$args = array(
		'role' => 'masjid',
		'meta_query' => array(
			array(
				'key' => 'masjid_plan',
				'value' => 'pro',
				'compare' => '=',
			),
		),
		'number' => 1000,
	);
	$user_query = new WP_User_Query($args);
	$today_ts = current_time('timestamp');
	foreach ($user_query->get_results() as $user) {
		$expiry = get_user_meta($user->ID, 'masjid_expiry', true);
		if (!$expiry) continue;
		$expiry_ts = strtotime($expiry . ' 00:00:00', $today_ts);
		if (!$expiry_ts) continue;
		$days_out = array(7,3,1);
		foreach ($days_out as $d) {
			$send_ts = strtotime('-' . $d . ' days', $expiry_ts);
			if ($send_ts >= $today_ts && $send_ts < ($today_ts + DAY_IN_SECONDS)) {
				// Send now during this day's cron
				if ($d === 7) mosque_subs_send_pre7($user->ID);
				if ($d === 3) mosque_subs_send_pre3($user->ID);
				if ($d === 1) mosque_subs_send_pre1($user->ID);
			}
		}
	}
}

?>

