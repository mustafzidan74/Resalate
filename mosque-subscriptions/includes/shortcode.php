<?php
if (!defined('ABSPATH')) {
	exit;
}

function mosque_subs_render_status_shortcode() {
	if (!is_user_logged_in()) {
		return '<p>' . esc_html__('Please log in to view your subscription status.', 'mosque-subs') . '</p>';
	}
	$user_id = get_current_user_id();
	$plan = get_user_meta($user_id, 'masjid_plan', true);
	$expiry = get_user_meta($user_id, 'masjid_expiry', true);
	$status = mosque_subs_is_active($user_id) ? __('Active', 'mosque-subs') : __('Expired', 'mosque-subs');
	$renew_url = mosque_subs_build_renew_url();
	$opts = mosque_subs_get_options();
	$stripe_enabled = !empty($opts['stripe']['secret_key']) && !empty($opts['stripe']['price_id']);

	ob_start();
	?>
	<div class="mosque-subs-status">
		<p><strong><?php echo esc_html__('Plan:', 'mosque-subs'); ?></strong> <?php echo esc_html($plan ? strtoupper($plan) : 'FREE'); ?></p>
		<p><strong><?php echo esc_html__('Status:', 'mosque-subs'); ?></strong> <?php echo esc_html($status); ?></p>
		<p><strong><?php echo esc_html__('Expiry:', 'mosque-subs'); ?></strong> <?php echo esc_html($expiry ? $expiry : __('N/A', 'mosque-subs')); ?></p>
		<?php if ($stripe_enabled) : ?>
			<p><a class="button button-primary" href="<?php echo esc_url($renew_url); ?>"><?php echo esc_html__('Renew', 'mosque-subs'); ?></a></p>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

?>

