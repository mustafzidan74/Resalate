<?php
if (!defined('ABSPATH')) {
	exit;
}

// Add plan choice to registration form
add_action('register_form', function() {
	$choice = isset($_POST['masjid_plan_choice']) ? sanitize_text_field($_POST['masjid_plan_choice']) : 'free';
	?>
	<p>
		<label for="masjid_plan_choice"><?php echo esc_html__('Mosque Plan', 'mosque-subs'); ?></label><br/>
		<label><input type="radio" name="masjid_plan_choice" value="free" <?php checked($choice, 'free'); ?> /> <?php echo esc_html__('Free', 'mosque-subs'); ?></label>
		<label style="margin-left:1em;"><input type="radio" name="masjid_plan_choice" value="pro" <?php checked($choice, 'pro'); ?> /> <?php echo esc_html__('Paid (Pro)', 'mosque-subs'); ?></label>
	</p>
	<?php
});

// Optionally validate (not mandatory)
add_filter('registration_errors', function($errors, $sanitized_user_login, $user_email) {
	return $errors;
}, 10, 3);

// Set plan meta on user creation
add_action('user_register', function($user_id) {
	$plan = isset($_POST['masjid_plan_choice']) ? sanitize_text_field($_POST['masjid_plan_choice']) : 'free';
	if ($plan === 'pro') {
		mosque_subs_activate_pro($user_id, null);
	} else {
		update_user_meta($user_id, 'masjid_plan', 'free');
	}
});

?>

