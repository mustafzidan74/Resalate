<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Sync masjid role capabilities for allowed CPTs
 */
function mosque_subs_sync_masjid_caps() {
	$role = get_role('masjid');
	if (!$role) return;
	$allowed_cpts = mosque_subs_allowed_cpts();
	$known_cpts = array_values(array_unique(array_merge(array('masjid-to-masjid','live-feed','donations'), $allowed_cpts)));
	$cap_sets = array(
		'edit_single' => 'edit_%s',
		'edit' => 'edit_%ss',
		'edit_others' => 'edit_others_%ss',
		'publish' => 'publish_%ss',
		'create' => 'create_%ss',
		'read_private' => 'read_private_%ss',
		'edit_private' => 'edit_private_%ss',
		'edit_published' => 'edit_published_%ss',
		'delete' => 'delete_%ss',
		'delete_private' => 'delete_private_%ss',
		'delete_published' => 'delete_published_%ss',
		'delete_others' => 'delete_others_%ss',
	);

	// Remove capabilities for all known CPTs first
	foreach ($known_cpts as $cpt) {
		foreach ($cap_sets as $fmt) {
			$cap = sprintf($fmt, $cpt);
			if ($role->has_cap($cap)) {
				$role->remove_cap($cap);
			}
		}
	}
	// Add capabilities for allowed CPTs
	foreach ($allowed_cpts as $cpt) {
		foreach ($cap_sets as $fmt) {
			$cap = sprintf($fmt, $cpt);
			$role->add_cap($cap);
		}
	}
}

/**
 * Enforce capability gating for masjid users on configured CPTs
 */
add_filter('user_has_cap', function($allcaps, $caps, $args, $user) {
	// $args: [0] requested cap, [1] user_id, ...
	if (empty($user) || empty($user->ID)) {
		return $allcaps;
	}
	$user_id = intval($user->ID);
	if (!mosque_subs_user_is_masjid($user_id)) {
		return $allcaps;
	}
	$active = mosque_subs_is_active($user_id);
	if ($active) {
		return $allcaps;
	}
	$allowed_cpts = mosque_subs_allowed_cpts();
	if (empty($allowed_cpts)) {
		return $allcaps;
	}
	// Remove publishing/editing caps for selected CPTs
	foreach ($allowed_cpts as $cpt) {
		$caps_to_block = array(
			"publish_{$cpt}",
			"edit_{$cpt}",
			"edit_{$cpt}s",
			"edit_others_{$cpt}s",
			"delete_{$cpt}",
			"delete_{$cpt}s",
			"delete_others_{$cpt}s",
			"create_{$cpt}s",
		);
		foreach ($caps_to_block as $cap_name) {
			if (isset($allcaps[$cap_name])) {
				$allcaps[$cap_name] = false;
			}
		}
	}
	return $allcaps;
}, 20, 4);

/**
 * Prevent publishing for restricted CPTs when non-active
 */
add_filter('wp_insert_post_data', function($data, $postarr, $unsanitized_postarr) {
	if (empty($data['post_type'])) return $data;
	$post_type = $data['post_type'];
	$allowed_cpts = mosque_subs_allowed_cpts();
	if (!in_array($post_type, $allowed_cpts, true)) return $data;
	if (current_user_can('manage_mosque_subscriptions')) return $data;
	$user_id = get_current_user_id();
	if (!$user_id || !mosque_subs_user_is_masjid($user_id)) return $data;
	if (!mosque_subs_is_active($user_id)) {
		if ($data['post_status'] === 'publish') {
			$data['post_status'] = 'draft';
			add_filter('redirect_post_location', function($location) {
				return add_query_arg('mosque_subs_notice', 'expired', $location);
			});
		}
	}
	return $data;
}, 10, 3);

/**
 * Admin notice on attempted publish when expired
 */
add_action('admin_notices', function() {
	if (!isset($_GET['mosque_subs_notice'])) return;
	if ($_GET['mosque_subs_notice'] !== 'expired') return;
	echo '<div class="notice notice-error"><p>' . esc_html__('Your mosque subscription is not active. Publishing is disabled.', 'mosque-subs') . '</p></div>';
});

/**
 * Hide Add New for restricted CPTs when enabled
 */
add_action('admin_menu', function() {
	$opts = mosque_subs_get_options();
	if (empty($opts['hide_add_new'])) return;
	$user_id = get_current_user_id();
	if (!$user_id || current_user_can('manage_mosque_subscriptions')) return;
	if (!mosque_subs_user_is_masjid($user_id) || mosque_subs_is_active($user_id)) return;
	$allowed_cpts = mosque_subs_allowed_cpts();
	foreach ($allowed_cpts as $cpt) {
		remove_submenu_page('edit.php?post_type=' . $cpt, 'post-new.php?post_type=' . $cpt);
	}
}, 100);

?>

