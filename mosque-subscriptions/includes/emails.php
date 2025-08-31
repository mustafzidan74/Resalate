<?php
if (!defined('ABSPATH')) {
	exit;
}

function mosque_subs_mail_tags($user_id) {
	$user = get_user_by('id', $user_id);
	$expiry = get_user_meta($user_id, 'masjid_expiry', true);
	$map = array(
		'{site_name}' => mosque_subs_current_site_name(),
		'{user_login}' => $user ? $user->user_login : '',
		'{expiry_date}' => $expiry ? $expiry : '',
		'{renew_url}' => mosque_subs_build_renew_url(),
	);
	return $map;
}

function mosque_subs_send_templated_mail($user_id, $subject_key, $template_key) {
	$opts = mosque_subs_get_options();
	$subject = isset($opts['email_subjects'][$subject_key]) ? $opts['email_subjects'][$subject_key] : '';
	$body = isset($opts['email_templates'][$template_key]) ? $opts['email_templates'][$template_key] : '';
	$user = get_user_by('id', $user_id);
	if (!$user || empty($subject) || empty($body)) return false;
	$replacements = mosque_subs_mail_tags($user_id);
	$final_subject = strtr($subject, $replacements);
	$final_body = wpautop(strtr($body, $replacements));
	$sent = wp_mail($user->user_email, $final_subject, $final_body, array('Content-Type: text/html; charset=UTF-8'));
	return $sent;
}

function mosque_subs_send_pre7($user_id) { return mosque_subs_send_templated_mail($user_id, 'pre_7', 'pre_7'); }
function mosque_subs_send_pre3($user_id) { return mosque_subs_send_templated_mail($user_id, 'pre_3', 'pre_3'); }
function mosque_subs_send_pre1($user_id) { return mosque_subs_send_templated_mail($user_id, 'pre_1', 'pre_1'); }
function mosque_subs_send_on_expiry($user_id) { return mosque_subs_send_templated_mail($user_id, 'on_expiry', 'on_expiry'); }
function mosque_subs_send_renewal_confirmation($user_id) { return mosque_subs_send_templated_mail($user_id, 'renewal_confirmation', 'renewal_confirmation'); }

?>

