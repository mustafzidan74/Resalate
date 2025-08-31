<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Create Stripe Checkout Session for current plan price
 * Returns array with 'id' and 'url' or WP_Error
 */
function mosque_subs_create_checkout_session($user_id) {
	$user = get_user_by('id', $user_id);
	if (!$user) return new WP_Error('invalid_user', 'User not found');
	$opts = mosque_subs_get_options();
	$stripe = isset($opts['stripe']) ? $opts['stripe'] : array();
	$secret = isset($stripe['secret_key']) ? trim($stripe['secret_key']) : '';
	$price_id = isset($stripe['price_id']) ? trim($stripe['price_id']) : '';
	if (!$secret || !$price_id) {
		return new WP_Error('stripe_not_configured', 'Stripe is not configured');
	}
	$success_url = add_query_arg('mosque_subs_success', '1', home_url('/'));
	$cancel_url = add_query_arg('mosque_subs_cancel', '1', home_url('/'));

	$body = array(
		'mode' => 'subscription',
		'success_url' => $success_url,
		'cancel_url' => $cancel_url,
		'line_items[0][price]' => $price_id,
		'line_items[0][quantity]' => 1,
		'client_reference_id' => (string)$user_id,
		'customer_email' => $user->user_email,
	);

	$response = wp_remote_post('https://api.stripe.com/v1/checkout/sessions', array(
		'timeout' => 30,
		'headers' => array(
			'Authorization' => 'Bearer ' . $secret,
		),
		'body' => $body,
	));

	if (is_wp_error($response)) return $response;
	$code = wp_remote_retrieve_response_code($response);
	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);
	if ($code >= 200 && $code < 300 && isset($data['id'])) {
		return array('id' => $data['id'], 'url' => $data['url']);
	}
	mosque_subs_logger('Stripe session create failed', array('code' => $code, 'body' => $body));
	return new WP_Error('stripe_error', 'Failed to create Stripe session');
}

/**
 * Handle Stripe webhook events
 */
function mosque_subs_handle_webhook() {
	$payload = file_get_contents('php://input');
	$sig_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';
	$opts = mosque_subs_get_options();
	$secret = isset($opts['stripe']['webhook_secret']) ? trim($opts['stripe']['webhook_secret']) : '';
	if (!$secret) {
		http_response_code(400);
		echo 'Webhook secret not configured';
		return;
	}
	if (!mosque_subs_verify_stripe_signature($payload, $sig_header, $secret)) {
		mosque_subs_logger('Stripe signature verification failed');
		http_response_code(400);
		echo 'Invalid signature';
		return;
	}
	$event = json_decode($payload, true);
	$type = isset($event['type']) ? $event['type'] : '';
	$object = isset($event['data']['object']) ? $event['data']['object'] : array();

	if ($type === 'checkout.session.completed') {
		$user_id = intval(isset($object['client_reference_id']) ? $object['client_reference_id'] : 0);
		if ($user_id > 0) {
			$new_date = mosque_subs_activate_pro($user_id, null);
			mosque_subs_send_renewal_confirmation($user_id);
			mosque_subs_logger('Checkout completed - user upgraded', array('user_id' => $user_id, 'expiry' => $new_date));
		}
	}

	if ($type === 'invoice.payment_succeeded') {
		// Attempt to locate user via subscription/customer email
		$email = isset($object['customer_email']) ? $object['customer_email'] : '';
		if (!$email && isset($object['customer'])) {
			// Could fetch customer object if needed
		}
		if ($email) {
			$user = get_user_by('email', $email);
			if ($user) {
				$new_date = mosque_subs_activate_pro($user->ID, null);
				mosque_subs_send_renewal_confirmation($user->ID);
				mosque_subs_logger('Invoice succeeded - user extended', array('user_id' => $user->ID, 'expiry' => $new_date));
			}
		}
	}

	http_response_code(200);
	echo 'OK';
}

function mosque_subs_parse_stripe_signature($sig_header) {
	$parts = array();
	$items = explode(',', $sig_header);
	foreach ($items as $item) {
		list($k, $v) = array_map('trim', explode('=', $item, 2));
		$parts[$k] = $v;
	}
	return $parts;
}

function mosque_subs_verify_stripe_signature($payload, $sig_header, $secret) {
	if (!$sig_header) return false;
	$parts = mosque_subs_parse_stripe_signature($sig_header);
	if (empty($parts['t']) || empty($parts['v1'])) return false;
	$signed_payload = $parts['t'] . '.' . $payload;
	$computed = hash_hmac('sha256', $signed_payload, $secret);
	// Compare signatures (allow multiple v1 values)
	$valid = false;
	$v1s = explode(' ', $parts['v1']);
	foreach ($v1s as $v1) {
		if (hash_equals($v1, $computed)) {
			$valid = true;
			break;
		}
	}
	// Optional: timestamp tolerance check (5 minutes)
	if ($valid) {
		$tolerance = 300;
		if (abs(time() - intval($parts['t'])) > $tolerance) {
			return false;
		}
	}
	return $valid;
}

?>

