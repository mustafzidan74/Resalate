<?php
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Mosque_Subs_Subscribers_Table extends WP_List_Table {
	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox" />',
			'user' => __('User', 'mosque-subs'),
			'email' => __('Email', 'mosque-subs'),
			'plan' => __('Plan', 'mosque-subs'),
			'expiry' => __('Expiry', 'mosque-subs'),
			'status' => __('Status', 'mosque-subs'),
		);
	}

	public function get_bulk_actions() {
		return array(
			'renew' => __('Renew +1 year', 'mosque-subs'),
			'set_free' => __('Set Free', 'mosque-subs'),
			'set_pro' => __('Set Pro', 'mosque-subs'),
			'set_expiry' => __('Set custom expiry', 'mosque-subs'),
		);
	}

	protected function column_cb($item) {
		return '<input type="checkbox" name="users[]" value="' . esc_attr($item['ID']) . '" />';
	}

	public function column_user($item) {
		$actions = array(
			'renew' => sprintf('<a href="%s">%s</a>', esc_url(wp_nonce_url(add_query_arg(array('action' => 'renew', 'user' => $item['ID'])), 'mosque_subs_row_action')), __('Renew +1 year', 'mosque-subs')),
			'set_free' => sprintf('<a href="%s">%s</a>', esc_url(wp_nonce_url(add_query_arg(array('action' => 'set_free', 'user' => $item['ID'])), 'mosque_subs_row_action')), __('Set Free', 'mosque-subs')),
			'set_pro' => sprintf('<a href="%s">%s</a>', esc_url(wp_nonce_url(add_query_arg(array('action' => 'set_pro', 'user' => $item['ID'])), 'mosque_subs_row_action')), __('Set Pro', 'mosque-subs')),
		);
		return sprintf('%1$s %2$s', esc_html($item['display_name']), $this->row_actions($actions));
	}

	public function column_default($item, $column_name) {
		switch ($column_name) {
			case 'email':
				return esc_html($item['user_email']);
			case 'plan':
				return esc_html($item['plan']);
			case 'expiry':
				return esc_html($item['expiry']);
			case 'status':
				return esc_html($item['status']);
			default:
				return '';
		}
	}

	public function prepare_items() {
		$per_page = 20;
		$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
		$offset = ($paged - 1) * $per_page;

		$args = array(
			'role' => 'masjid',
			'number' => $per_page,
			'offset' => $offset,
			'orderby' => 'display_name',
			'order' => 'ASC',
		);
		$user_query = new WP_User_Query($args);
		$users = $user_query->get_results();

		$data = array();
		foreach ($users as $u) {
			$plan = get_user_meta($u->ID, 'masjid_plan', true);
			$expiry = get_user_meta($u->ID, 'masjid_expiry', true);
			$active = mosque_subs_is_active($u->ID);
			$data[] = array(
				'ID' => $u->ID,
				'display_name' => $u->display_name,
				'user_email' => $u->user_email,
				'plan' => $plan ? $plan : 'free',
				'expiry' => $expiry ? $expiry : '—',
				'status' => $active ? __('Active', 'mosque-subs') : __('Expired', 'mosque-subs'),
			);
		}

		$this->items = $data;
		$this->set_pagination_args(array(
			'total_items' => intval($user_query->get_total()),
			'per_page' => $per_page,
		));
	}
}

function mosque_subs_render_subscribers_page() {
	if (!current_user_can('manage_mosque_subscriptions')) {
		wp_die(__('You do not have permission to access this page.', 'mosque-subs'));
	}

	// Handle actions
	$custom_expiry = isset($_POST['custom_expiry']) ? sanitize_text_field($_POST['custom_expiry']) : '';
	if (!empty($_REQUEST['action'])) {
		$action = sanitize_text_field($_REQUEST['action']);
		if (in_array($action, array('renew','set_free','set_pro','set_expiry'), true)) {
			check_admin_referer('mosque_subs_row_action');
			$users = isset($_REQUEST['users']) ? (array)$_REQUEST['users'] : array();
			if (isset($_REQUEST['user'])) {
				$users[] = $_REQUEST['user'];
			}
			$users = array_map('intval', $users);
			foreach ($users as $uid) {
				if ($action === 'renew') {
					mosque_subs_activate_pro($uid, null);
				} elseif ($action === 'set_free') {
					mosque_subs_downgrade_to_free($uid);
				} elseif ($action === 'set_pro') {
					mosque_subs_activate_pro($uid, null);
				} elseif ($action === 'set_expiry' && $custom_expiry) {
					update_user_meta($uid, 'masjid_expiry', $custom_expiry);
				}
			}
			add_settings_error('mosque_subs', 'subs_updated', __('Subscribers updated.', 'mosque-subs'), 'updated');
		}
	}

	settings_errors('mosque_subs');
	$table = new Mosque_Subs_Subscribers_Table();
	$table->prepare_items();
	?>
	<div class="wrap">
		<h1><?php echo esc_html__('Mosque Subscriptions - Subscribers', 'mosque-subs'); ?></h1>
		<form method="post">
			<?php wp_nonce_field('mosque_subs_row_action'); ?>
			<p>
				<label><?php echo esc_html__('Custom expiry (Y-m-d) for Set custom expiry:', 'mosque-subs'); ?>
					<input type="text" name="custom_expiry" placeholder="YYYY-MM-DD" value="" />
				</label>
			</p>
			<?php $table->display(); ?>
		</form>
	</div>
	<?php
}

?>

