<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Resalate
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function resalate_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'resalate_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function resalate_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'resalate_pingback_header' );


/*********** Custom Code ***********/
function enqueue_custom_assets() {
    // Define the base URL for the assets
    $assets_url = get_template_directory_uri() . '/assets';

    // Define a single version for all assets
    $version = rand();

    // Enqueue CSS files
    wp_enqueue_style('select2', $assets_url . '/dashboard/css/select2.min.css', array(), $version);
    // wp_enqueue_style('bootstrap', $assets_url . '/css/bootstrap.min.css', array(), $version);
    wp_enqueue_style('magnific-popup', $assets_url . '/css/magnific-popup.css', array(), $version);
    wp_enqueue_style('swiper-bundle', $assets_url . '/css/swiper-bundle.min.css', array(), $version);
    wp_enqueue_style('animate', $assets_url . '/css/animate.css', array(), $version);
    // wp_enqueue_style('custom-font', $assets_url . '/css/custom-font.css', array(), $version);
    wp_enqueue_style('fontawesome', $assets_url . '/css/fontawesome.css', array(), $version);
    wp_enqueue_style('aos', $assets_url . '/css/aos.css', array(), $version);
    wp_enqueue_style('icomoon', $assets_url . '/css/icomoon.css', array(), $version);
    // wp_enqueue_style('main', $assets_url . '/css/main.css', array(), $version);
    // wp_enqueue_style('app-min', $assets_url . '/css/app.min.css', array(), $version);
    wp_enqueue_style('dashboard-style', $assets_url . '/dashboard/css/style.css', array(), $version);
    wp_enqueue_style('dashboard-select', $assets_url . '/dashboard/css/tom-select.css', array(), $version);
    wp_enqueue_style('dashboard-snow', $assets_url . '/dashboard/css/quill.snow.css', array(), $version);
    wp_enqueue_style('dashboard-min', $assets_url . '/dashboard/css/main.css', array(), $version);


    // Enqueue JavaScript files
    wp_enqueue_script('jquery', $assets_url . '/js/jquery-3.6.0.min.js', array(), $version, true);
    wp_enqueue_script('fontawesome', $assets_url . '/js/fontawesome.js', array(), $version, true);
    wp_enqueue_script('select2', $assets_url . '/dashboard/js/select2.min.js', array('jquery'), $version, true);
    // wp_enqueue_script('bootstrap-bundle', $assets_url . '/js/bootstrap.bundle.min.js', array('jquery'), $version, true);
    wp_enqueue_script('aos', $assets_url . '/js/aos.js', array(), $version, true);
    // wp_enqueue_script('menu', $assets_url . '/js/menu/menu.js', array(), $version, true);
    wp_enqueue_script('gsap', $assets_url . '/js/gsap.min.js', array(), $version, true);
    wp_enqueue_script('isotope', $assets_url . '/js/isotope.pkgd.min.js', array(), $version, true);
    wp_enqueue_script('magnific-popup', $assets_url . '/js/jquery.magnific-popup.min.js', array('jquery'), $version, true);
    wp_enqueue_script('swiper-bundle', $assets_url . '/js/swiper-bundle.min.js', array(), $version, true);
    wp_enqueue_script('countdown', $assets_url . '/js/countdown.js', array(), $version, true);
    wp_enqueue_script('wow', $assets_url . '/js/wow.min.js', array(), $version, true);
    wp_enqueue_script('split-text', $assets_url . '/js/SplitText.min.js', array(), $version, true);
    wp_enqueue_script('scroll-trigger', $assets_url . '/js/ScrollTrigger.min.js', array(), $version, true);
    wp_enqueue_script('scroll-smoother', $assets_url . '/js/ScrollSmoother.min.js', array(), $version, true);
    wp_enqueue_script('skill-bar', $assets_url . '/js/skill-bar.js', array(), $version, true);
    wp_enqueue_script('infinite-slider', $assets_url . '/js/infinite-slider.js', array(), $version, true);
    wp_enqueue_script('image-resizing', $assets_url . '/js/image-resizing.js', array(), $version, true);
    wp_enqueue_script('faq', $assets_url . '/js/faq.js', array('jquery'), $version, true);
    // Google Maps API
    // wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyArZVfNvjnLNwJZlLJKuOiWHZ6vtQzzb1Y', array(), $version, true);

    // Enqueue your main script (app.js)
    // wp_enqueue_script('app', $assets_url . '/js/app.js', array('jquery'), $version, true);
    wp_enqueue_script('dashboard-tom-select.complete.min.js', $assets_url . '/dashboard/js/tom-select.complete.min.js', array('jquery'), $version, true);
    wp_enqueue_script('dashboard-quill.js', $assets_url . '/dashboard/js/quill.js', array('jquery'), $version, true);
    wp_enqueue_script('dashboard-TW.js', $assets_url . '/dashboard/js/TW.js', array('jquery'), $version, false);
    wp_enqueue_script('dashboard-main.js', $assets_url . '/dashboard/js/main.js', array('jquery'), $version, true);

    // Localize the script to pass ajaxurl
    wp_localize_script('app', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_assets');

// Register Menus
function theme_setup() {
    register_nav_menus(array(
        'primary-menu'        => __('Primary Menu', 'resalate'),
        'footer_menu'         => __('Footer Menu', 'resalate'),
        'masjid_dashboard_menu' => __('Masjid Dashboard Menu', 'resalate'),
        'user_dashboard_menu'     => __('User Dashboard Menu', 'resalate'),
    ));
}
add_action('after_setup_theme', 'theme_setup');
// Dynamic Add Data
function add_dynamic_styles() {
    // Get the values of the ACF option fields
    $primary_color = get_field('primary_color', 'option'); // Replace with your actual field name
    $secondary_color = get_field('secondary_color', 'option'); // Replace with your actual field name
    $font_family = get_field('font_family', 'option'); // Replace with your actual field name

    // Default values if fields are empty
    $primary_color = $primary_color ? $primary_color : '#000000'; // Default to black
    $secondary_color = $secondary_color ? $secondary_color : '#ffffff'; // Default to white
    $font_family = $font_family ? $font_family : 'Cairo'; // Default font

    // Output the CSS in <style> tags
    echo "<style>
        @import url('https://fonts.googleapis.com/css2?family=" . urlencode($font_family) . ":wght@400..700&display=swap');

        :root {
            --mainColor: {$primary_color};
            --secondColor: {$secondary_color};
            --fontFamily: '{$font_family}', sans-serif;
        }
    </style>";
}
add_action('wp_head', 'add_dynamic_styles');

// Disable Gutenberg editor
add_filter('use_block_editor_for_post', '__return_false', 10);

// Enable Classic Editor
add_filter('the_content', 'classic_editor_content');

// SVG Support
function enable_svg_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'enable_svg_uploads');
function sanitize_svg_file($file) {
    if ($file['type'] === 'image/svg+xml') {
        $file_contents = file_get_contents($file['tmp_name']);
        $safe_content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $file_contents);
        file_put_contents($file['tmp_name'], $safe_content);
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'sanitize_svg_file');
function display_svg_thumbnails($response, $attachment, $meta) {
    if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement')) {
        try {
            $path = get_attached_file($attachment->ID);
            if (@file_exists($path)) {
                $svg = simplexml_load_file($path);
                if ($svg !== false) {
                    $attributes = $svg->attributes();
                    $viewbox = explode(' ', $attributes->viewBox);
                    if (count($viewbox) === 4) {
                        $response['sizes'] = [
                            'full' => [
                                'url' => $response['url'],
                                'width' => (int) $viewbox[2],
                                'height' => (int) $viewbox[3],
                                'orientation' => $viewbox[3] > $viewbox[2] ? 'portrait' : 'landscape',
                            ],
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            // Handle SVG parsing errors
        }
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'display_svg_thumbnails', 10, 3);


// Handle AJAX Request to Save Sponsor Requests
function handle_sponsor_request() {
    $form_data = $_POST;

    // Validation
    if (empty($form_data['name']) || empty($form_data['email']) || empty($form_data['message'])) {
        wp_send_json_error("Please fill all required fields.");
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'sponsor_requests';

    $inserted = $wpdb->insert($table_name, [
        'name'    => sanitize_text_field($form_data['name']),
        'email'   => sanitize_email($form_data['email']),
        'phone'   => sanitize_text_field($form_data['phone']),
        'message' => sanitize_textarea_field($form_data['message']),
        'date'    => current_time('mysql'),
    ]);

    if (!$inserted) {
        wp_send_json_error("Database error, please try again.");
    }

    // Email
    $admin_email = get_option('admin_email');
    $subject = 'New Sponsor Request Submitted';
    $message = "Name: " . $form_data['name'] . "\n";
    $message .= "Email: " . $form_data['email'] . "\n";
    $message .= "Phone: " . $form_data['phone'] . "\n";
    $message .= "Message:\n" . $form_data['message'] . "\n";
    $message .= "Date: " . current_time('mysql') . "\n";

    wp_mail($admin_email, $subject, $message);

    wp_send_json_success("Your sponsor request has been submitted successfully.");
}
add_action('wp_ajax_submit_sponsor_request', 'handle_sponsor_request');
add_action('wp_ajax_nopriv_submit_sponsor_request', 'handle_sponsor_request');
// function create_sponsor_requests_table() {
//     global $wpdb;
//     $table_name = $wpdb->prefix . 'sponsor_requests';
//     $charset_collate = $wpdb->get_charset_collate();

//     $sql = "CREATE TABLE $table_name (
//         id mediumint(9) NOT NULL AUTO_INCREMENT,
//         name tinytext NOT NULL,
//         email varchar(100) NOT NULL,
//         phone varchar(20) NOT NULL,
//         message text NOT NULL,
//         date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
//         PRIMARY KEY  (id)
//     ) $charset_collate;";

//     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//     dbDelta($sql);
// }
// add_action('after_setup_theme', 'create_sponsor_requests_table');
function sponsor_requests_menu() {
    add_menu_page(
        'Sponsor Requests',
        'Sponsor Requests',
        'manage_options',
        'sponsor-requests',
        'display_sponsor_requests',
        'dashicons-list-view',
        20
    );
}
add_action('admin_menu', 'sponsor_requests_menu');

function display_sponsor_requests() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sponsor_requests';
    $requests = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<div class="wrap"><h1>Sponsor Requests</h1>';
    echo '<table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($requests as $request) {
        echo '<tr>
                <td>' . esc_html($request->name) . '</td>
                <td>' . esc_html($request->email) . '</td>
                <td>' . esc_html($request->phone) . '</td>
                <td>' . esc_html($request->message) . '</td>
                <td>' . esc_html($request->date) . '</td>
                <td><a href="?page=sponsor-requests&delete=' . $request->id . '">Delete</a></td>
              </tr>';
    }
    echo '</tbody></table></div>';

    // Handle delete
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $wpdb->delete($table_name, ['id' => $id]);
        echo '<script>location.href="?page=sponsor-requests";</script>';
    }
}


// ****************** Login AJAX ************************* //
add_action('wp_ajax_masjid_login', 'handle_masjid_login');
add_action('wp_ajax_nopriv_masjid_login', 'handle_masjid_login');

function handle_masjid_login() {
    $response = ['success' => false];

    if (!isset($_POST['log'], $_POST['pwd'])) {
        $response['message'] = 'Both username/email and password are required.';
        wp_send_json($response);
    }

    $login_input = sanitize_text_field($_POST['log']);
    $password = $_POST['pwd'];
    $user = is_email($login_input) ? get_user_by('email', $login_input) : get_user_by('login', $login_input);

    if (!$user) {
        $response['message'] = is_email($login_input)
            ? 'No account found with this email address.'
            : 'Invalid username. Please check your username.';
    } elseif (!wp_check_password($password, $user->user_pass, $user->ID)) {
        $response['message'] = 'The password you entered is incorrect. Please try again.';
    } else {
        wp_set_auth_cookie($user->ID, true);
        wp_set_current_user($user->ID);

        $response['success'] = true;
        $response['message'] = 'Login successful! Redirecting...';

        if (in_array('administrator', $user->roles)) {
            $response['redirect'] = admin_url();
        } elseif (in_array('masjid', $user->roles)) {
            $response['redirect'] = home_url('/masjid-dashboard');
        } else {
            $response['redirect'] = home_url('/user-dashboard');
        }
    }

    wp_send_json($response);
}


// ******************** Froget Password *********************** //
// Step 1: Send OTP Code to Email
add_action('wp_ajax_nopriv_send_reset_otp', 'send_reset_otp');
function send_reset_otp() {
    $email = sanitize_email($_POST['email'] ?? '');
    if (empty($email) || !is_email($email)) {
        wp_send_json_error('Please enter a valid email address.');
    }
    $user = get_user_by('email', $email);
    if (!$user) {
        wp_send_json_error('No user found with this email.');
    }
    $otp = wp_rand(100000, 999999);
    update_user_meta($user->ID, '_reset_password_otp', $otp);
    update_user_meta($user->ID, '_reset_password_otp_time', time());
    wp_mail($email, 'Your Password Reset Code', "Your verification code is: $otp");
    wp_send_json_success('Verification code sent to your email.');
}
add_action('wp_ajax_nopriv_verify_reset_otp', 'verify_reset_otp');
function verify_reset_otp() {
    $email = sanitize_email($_POST['email'] ?? '');
    $otp = sanitize_text_field($_POST['otp'] ?? '');
    $user = get_user_by('email', $email);
    if (!$user) {
        wp_send_json_error('Invalid user.');
    }
    $saved_otp = get_user_meta($user->ID, '_reset_password_otp', true);
    $otp_time = get_user_meta($user->ID, '_reset_password_otp_time', true);
    if (!$saved_otp || $saved_otp !== $otp) {
        wp_send_json_error('Invalid or expired verification code.');
    }
    if (time() - intval($otp_time) > 15 * 60) { // 15 mins expiry
        wp_send_json_error('Verification code expired.');
    }
    update_user_meta($user->ID, '_reset_password_verified', true);
    wp_send_json_success('Verification code is correct.');
}
add_action('wp_ajax_nopriv_save_new_password', 'save_new_password');
function save_new_password() {
    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (empty($password) || strlen($password) < 6) {
        wp_send_json_error('Password must be at least 6 characters.');
    }
    if ($password !== $confirm) {
        wp_send_json_error('Passwords do not match.');
    }
    $user = get_user_by('email', $email);
    if (!$user) {
        wp_send_json_error('User not found.');
    }
    $verified = get_user_meta($user->ID, '_reset_password_verified', true);
    if (!$verified) {
        wp_send_json_error('Verification not completed.');
    }
    wp_set_password($password, $user->ID);
    delete_user_meta($user->ID, '_reset_password_otp');
    delete_user_meta($user->ID, '_reset_password_otp_time');
    delete_user_meta($user->ID, '_reset_password_verified');
    wp_send_json_success('Password has been updated.');
}
add_action('wp_enqueue_scripts', function () {
    wp_localize_script('jquery', 'resetPassAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
});

// ************* Register ****************** //
// Register Masjid AJAX
add_action('wp_ajax_nopriv_register_masjid_account', 'register_masjid_account');
function register_masjid_account() {
    check_ajax_referer('register_nonce', 'security');
    
    $errors = [];
    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $name = sanitize_text_field($_POST['masjid_name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $country = sanitize_text_field($_POST['country'] ?? '');
    $province = sanitize_text_field($_POST['province'] ?? '');
    $city = sanitize_text_field($_POST['city'] ?? '');

    // Validation
    if (empty($name)) $errors['masjid_name'] = 'Masjid name is required';
    if (empty($email)) $errors['email'] = 'Email is required';
    if (!is_email($email)) $errors['email'] = 'Please enter a valid email address';
    if (email_exists($email)) $errors['email'] = 'This email is already registered';
    if (empty($password)) $errors['password'] = 'Password is required';
    if (strlen($password) < 6) $errors['password'] = 'Password must be at least 6 characters';
    if ($password !== $confirm_password) $errors['confirm_password'] = 'Passwords do not match';
    if (empty($phone)) $errors['phone'] = 'Phone number is required';
    if (empty($country)) $errors['country'] = 'Country is required';
    if (empty($province)) $errors['province'] = 'Province/State is required';
    if (empty($city)) $errors['city'] = 'City is required';

    if (!empty($errors)) {
        wp_send_json_error([
            'message' => 'Please correct the errors below',
            'errors' => $errors
        ]);
    }

    $user_id = wp_create_user($email, $password, $email);
    if (is_wp_error($user_id)) {
        wp_send_json_error([
            'message' => 'Registration failed: ' . $user_id->get_error_message()
        ]);
    }

    wp_update_user([
        'ID' => $user_id,
        'display_name' => $name,
        'role' => 'masjid',
    ]);

    update_user_meta($user_id, 'phone', $phone);
    update_user_meta($user_id, 'country', $country);
    update_user_meta($user_id, 'province', $province);
    update_user_meta($user_id, 'city', $city);

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    wp_send_json_success([
        'redirect' => home_url('/masjid-dashboard')
    ]);
}

// Register User AJAX
add_action('wp_ajax_nopriv_register_user_account', 'register_user_account');
function register_user_account() {
    check_ajax_referer('register_nonce', 'security');
    
    $errors = [];
    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');

    // Validation
    if (empty($name)) $errors['name'] = 'Full name is required';
    if (empty($email)) $errors['email'] = 'Email is required';
    if (!is_email($email)) $errors['email'] = 'Please enter a valid email address';
    if (email_exists($email)) $errors['email'] = 'This email is already registered';
    if (empty($password)) $errors['password'] = 'Password is required';
    if (strlen($password) < 6) $errors['password'] = 'Password must be at least 6 characters';
    if ($password !== $confirm_password) $errors['confirm_password'] = 'Passwords do not match';
    if (empty($phone)) $errors['phone'] = 'Phone number is required';

    if (!empty($errors)) {
        wp_send_json_error([
            'message' => 'Please correct the errors below',
            'errors' => $errors
        ]);
    }

    $user_id = wp_create_user($email, $password, $email);
    if (is_wp_error($user_id)) {
        wp_send_json_error([
            'message' => 'Registration failed: ' . $user_id->get_error_message()
        ]);
    }

    wp_update_user([
        'ID' => $user_id,
        'display_name' => $name,
        'role' => 'subscriber',
    ]);

    update_user_meta($user_id, 'phone', $phone);

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    wp_send_json_success([
        'redirect' => home_url('/user-dashboard')
    ]);
}

// ******************* masjid Lessons ************** //
add_action('admin_post_delete_lesson', 'handle_delete_lesson');
function handle_delete_lesson() {
    if (!is_user_logged_in()) {
        wp_die('Unauthorized');
    }

    $lesson_id = intval($_POST['lesson_id'] ?? 0);
    if (!$lesson_id || !wp_verify_nonce($_POST['_wpnonce'], 'delete_lesson_' . $lesson_id)) {
        wp_die('Security check failed');
    }

    $post = get_post($lesson_id);
    if ($post && $post->post_author == get_current_user_id()) {
        wp_delete_post($lesson_id, true);
    }

    wp_redirect(wp_get_referer());
    exit;
}
function custom_content_type() {
    header('Content-Type: text/html; charset=utf-8');
}
add_action('init', 'custom_content_type');
add_filter('user_has_cap', function ($allcaps, $caps, $args, $user) {
    if (! isset($args[0], $args[2])) return $allcaps;

    $cap  = $args[0];      // delete_post أو edit_post
    $post = get_post($args[2]);

    if ($post && $post->post_type === 'lessons' && (int) $post->post_author === (int) $user->ID) {
        if (in_array($cap, ['edit_post', 'delete_post'], true)) {
            $allcaps[$cap] = true; // اسمح للمؤلف يعدّل ويحذف دروسه
        }
    }
    return $allcaps;
}, 10, 4);

// ******************* masjid Funerals ************** //
add_action('admin_post_delete_funeral', 'handle_delete_funeral');
function handle_delete_funeral() {
    if (!is_user_logged_in()) {
        wp_die('Unauthorized');
    }

    $funeral_id = intval($_POST['funeral_id'] ?? 0);
    if (!$funeral_id || !wp_verify_nonce($_POST['_wpnonce'], 'delete_funeral_' . $funeral_id)) {
        wp_die('Security check failed');
    }

    $post = get_post($funeral_id);
    if ($post && $post->post_author == get_current_user_id()) {
        wp_delete_post($funeral_id, true);
    }

    wp_redirect(wp_get_referer());
    exit;
}
add_filter('user_has_cap', function ($allcaps, $caps, $args, $user) {
    if (isset($args[0]) && $args[0] === 'edit_post') {
        $post = get_post($args[2]);
        if ($post && $post->post_type === 'funerals') {
            $allcaps['edit_post'] = true;
        }
    }
    return $allcaps;
}, 10, 4);


// ******************* masjid live Feed ************** //
add_action('admin_post_delete_live_feed', 'handle_delete_live_feed');
function handle_delete_live_feed() {
    if (!is_user_logged_in()) {
        wp_die('Unauthorized');
    }

    $live_feed_id = intval($_POST['live_feed_id'] ?? 0);
    if (!$live_feed_id || !wp_verify_nonce($_POST['_wpnonce'], 'delete_live_feed_' . $live_feed_id)) {
        wp_die('Security check failed');
    }

    $post = get_post($live_feed_id);
    if ($post && $post->post_author == get_current_user_id()) {
        wp_delete_post($live_feed_id, true);
    }

    wp_redirect(wp_get_referer());
    exit;
}
add_filter('user_has_cap', function ($allcaps, $caps, $args, $user) {
    if (isset($args[0]) && $args[0] === 'edit_post') {
        $post = get_post($args[2]);
        if ($post && $post->post_type === 'live-feed') {
            $allcaps['edit_post'] = true;
        }
    }
    return $allcaps;
}, 10, 4);

// ******************* masjid from masjid to masjid ************** //
add_action('admin_post_delete_masjid_to_masjid', 'handle_delete_masjid_to_masjid');
function handle_delete_masjid_to_masjid() {
    if (!is_user_logged_in()) {
        wp_die('Unauthorized');
    }

    $masjid_to_masjid_id = intval($_POST['masjid_to_masjid_id'] ?? 0);
    if (!$masjid_to_masjid_id || !wp_verify_nonce($_POST['_wpnonce'], 'delete_masjid_to_masjid_' . $masjid_to_masjid_id)) {
        wp_die('Security check failed');
    }

    $post = get_post($masjid_to_masjid_id);
    if ($post && $post->post_author == get_current_user_id()) {
        wp_delete_post($masjid_to_masjid_id, true);
    }

    wp_redirect(wp_get_referer());
    exit;
}
add_filter('user_has_cap', function ($allcaps, $caps, $args, $user) {
    if (isset($args[0]) && $args[0] === 'edit_post') {
        $post = get_post($args[2]);
        if ($post && $post->post_type === 'masjid-to-masjid') {
            $allcaps['edit_post'] = true;
        }
    }
    return $allcaps;
}, 10, 4);

// ******************* masjid Donations ************** //
add_action('admin_post_delete_donation', 'handle_delete_donation'); // كله small

function handle_delete_donation() {
    if (!is_user_logged_in()) {
        wp_die('Unauthorized');
    }

    $donation_id = intval($_POST['donation_id'] ?? 0); // كله small
    $nonce       = $_POST['_wpnonce'] ?? '';

    if (!$donation_id || !wp_verify_nonce($nonce, 'delete_donation_' . $donation_id)) {
        wp_die('Security check failed');
    }

    $post = get_post($donation_id);

    if ($post && (int) $post->post_author === (int) get_current_user_id()) {
        $deleted = wp_delete_post($donation_id, true);
        $status  = $deleted ? '1' : '0';
    } else {
        $status = '0';
    }

    // رجّع لنفس الصفحة مع باراميتر نتيجة الحذف
    $back = wp_get_referer();
    wp_redirect( add_query_arg('deleted', $status, $back ? $back : home_url()) );
    exit;
}

// ********* The Most Views Posts Action *************** //
function track_post_views($post_id) {
    if (!is_single()) return;

    $views = (int) get_post_meta($post_id, 'post_views_count', true);
    $views++;
    update_post_meta($post_id, 'post_views_count', $views);
}
function set_post_view_counter() {
    if (is_single()) {
        global $post;
        track_post_views($post->ID);
    }
}
add_action('wp_head', 'set_post_view_counter');
add_action('pre_get_posts', function($query) {
  if (is_admin() || !$query->is_main_query() || !is_archive()) return;

  if (!empty($_GET['orderby'])) {
    $orderby = sanitize_text_field($_GET['orderby']);

    if ($orderby == 'views_desc') {
      $query->set('meta_key', 'post_views_count');
      $query->set('orderby', ['meta_value_num' => 'DESC', 'date' => 'DESC']);
    } elseif ($orderby == 'title_asc') {
      $query->set('orderby', 'title');
      $query->set('order', 'ASC');
    } elseif ($orderby == 'date_asc') {
      $query->set('orderby', 'date');
      $query->set('order', 'ASC');
    } else {
      $query->set('orderby', 'date');
      $query->set('order', 'DESC');
    }
  }

  if (!empty($_GET['masjid'])) {
    $masjid_id = intval($_GET['masjid']);
    $query->set('author', $masjid_id);
  }
});


// ****************** Contact Us Page ****************** //
// 1. صفحة الشكاوى في لوحة التحكم
add_action('admin_menu', function () {
    add_menu_page(
        'User Complaints',
        'Complaints',
        'manage_options',
        'user-complaints',
        'render_complaints_page',
        'dashicons-feedback',
        26
    );
});

// 2. عرض صفحة الشكاوى
function render_complaints_page() {
    $complaints = get_option('stored_complaints', []);
    echo '<div class="wrap"><h1>User Complaints</h1>';

    if (empty($complaints)) {
        echo '<p>No complaints submitted yet.</p></div>';
        return;
    }

    echo '<table class="widefat striped" id="complaints-table"><thead><tr>
        <th>#</th>
        <th>Date</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Action</th>
    </tr></thead><tbody>';

    $reversed = array_reverse($complaints);
    foreach ($reversed as $index => $complaint) {
        $id = count($complaints) - $index - 1; // لحساب الفهرس الأصلي للحذف

        echo '<tr id="row-' . $id . '">';
        echo '<td>' . ($index + 1) . '</td>';
        echo '<td>' . esc_html($complaint['date']) . '</td>';
        echo '<td>' . esc_html($complaint['name']) . '</td>';
        echo '<td>' . esc_html($complaint['email']) . '</td>';
        echo '<td>' . esc_html($complaint['phone']) . '</td>';
        echo '<td>' . esc_html($complaint['subject']) . '</td>';
        echo '<td>' . nl2br(esc_html($complaint['message'])) . '</td>';
        echo '<td><button class="button delete-complaint" data-id="' . $id . '">Delete</button></td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';

    // JavaScript لأجاكس الحذف
    ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.delete-complaint');
        buttons.forEach(btn => {
          btn.addEventListener('click', function () {
            const id = this.dataset.id;
            if (!confirm('Are you sure you want to delete this complaint?')) return;

            fetch(ajaxurl, {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: `action=delete_complaint_ajax&id=${id}`
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                document.getElementById(`row-${id}`).remove();
              } else {
                alert('Failed to delete complaint.');
              }
            });
          });
        });
      });
    </script>
    <?php
}

// 3. AJAX حذف شكوى
add_action('wp_ajax_delete_complaint_ajax', function () {
    if (!current_user_can('manage_options')) wp_send_json_error();

    $id = isset($_POST['id']) ? intval($_POST['id']) : -1;
    $complaints = get_option('stored_complaints', []);

    if (!isset($complaints[$id])) wp_send_json_error();

    unset($complaints[$id]);
    $complaints = array_values($complaints); // إعادة ترتيب المفاتيح

    update_option('stored_complaints', $complaints);
    wp_send_json_success();
});

function handle_ajax_complaint() {
  $name    = sanitize_text_field($_POST['name'] ?? '');
  $email   = sanitize_email($_POST['email'] ?? '');
  $phone   = sanitize_text_field($_POST['phone'] ?? '');
  $subject = sanitize_text_field($_POST['subject'] ?? '');
  $message = wp_kses_post($_POST['message'] ?? '');

  if (!$name || !$email || !$subject || !$message) {
    wp_send_json(['success' => false, 'message' => 'Please fill in all required fields.']);
  }

  $complaints = get_option('stored_complaints', []);
  $complaints[] = [
    'date'    => current_time('mysql'),
    'name'    => $name,
    'email'   => $email,
    'phone'   => $phone,
    'subject' => $subject,
    'message' => $message,
  ];
  update_option('stored_complaints', $complaints);

  wp_send_json(['success' => true, 'message' => 'Your complaint has been submitted successfully.']);
}

// 1. إنشاء صفحة Contact Settings في ACF Options Page
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'Contact Settings',
        'menu_title' => 'Contact Settings',
        'menu_slug'  => 'contact-settings',
        'capability' => 'edit_posts',
        'redirect'   => false,
    ]);
}

// 2. تسجيل مجموعة الحقول الخاصة بصفحة Contact Settings
add_action('acf/init', function () {
    if (function_exists('acf_add_local_field_group')) {

        acf_add_local_field_group([
            'key' => 'group_contact_settings',
            'title' => 'Contact Page Settings',
            'fields' => [
                [
                    'key' => 'field_contact_title',
                    'label' => 'Contact Title',
                    'name' => 'contact_title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_contact_email',
                    'label' => 'Contact Email',
                    'name' => 'contact_email',
                    'type' => 'email',
                ],
                [
                    'key' => 'field_contact_phone',
                    'label' => 'Contact Phone',
                    'name' => 'contact_phone',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_contact_address',
                    'label' => 'Contact Address',
                    'name' => 'contact_address',
                    'type' => 'textarea',
                ],
                [
                    'key' => 'field_contact_facebook',
                    'label' => 'Facebook URL',
                    'name' => 'contact_facebook',
                    'type' => 'url',
                ],
                [
                    'key' => 'field_contact_twitter',
                    'label' => 'Twitter URL',
                    'name' => 'contact_twitter',
                    'type' => 'url',
                ],
                [
                    'key' => 'field_contact_instagram',
                    'label' => 'Instagram URL',
                    'name' => 'contact_instagram',
                    'type' => 'url',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'contact-settings',
                    ],
                ],
            ],
        ]);
    }
});

// ********* User Profile /******* //
add_action('wp_ajax_update_user_password', 'handle_ajax_password_update');
function handle_ajax_password_update() {
    if (!is_user_logged_in()) {
        wp_send_json(['success' => false, 'message' => 'Unauthorized.']);
    }

    if (!wp_verify_nonce($_POST['_wpnonce'], 'ajax_pass_update')) {
        wp_send_json(['success' => false, 'message' => 'Security check failed.']);
    }

    $user = wp_get_current_user();
    $user_id = $user->ID;

    $current_pass = sanitize_text_field($_POST['current_password']);
    $new_pass = sanitize_text_field($_POST['new_password']);
    $confirm_pass = sanitize_text_field($_POST['confirm_password']);

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        wp_send_json(['success' => false, 'message' => 'Please fill in all password fields.']);
    }

    if ($new_pass !== $confirm_pass) {
        wp_send_json(['success' => false, 'message' => 'Password confirmation does not match.']);
    }

    if (!empty($current_pass) && !empty($new_pass) && $new_pass === $confirm_pass) {
        if (wp_check_password($current_pass, $current_user->user_pass, $user_id)) {
            wp_set_password($new_pass, $user_id);
            wp_logout(); // ❗️ تسجيل خروج المستخدم مباشرة
            wp_redirect(site_url('/login'));
            exit;
        } else {
            echo '<p class="text-red-600 font-bold mt-4">Incorrect current password.</p>';
        }
    }

    wp_set_password($new_pass, $user_id);
    wp_send_json(['success' => true, 'message' => 'Password updated successfully.']);
}


// *********** USer Follow ***************** //
add_action('wp_ajax_toggle_follow_masjid', 'handle_toggle_follow_masjid');

function handle_toggle_follow_masjid() {
    // تنظيف أي طباعة سابقة
    if (ob_get_length()) ob_clean();

    // التحقق من nonce
    check_ajax_referer('toggle_follow_masjid', 'security');

    $user_id = get_current_user_id();
    $masjid_id = isset($_POST['masjid_id']) ? absint($_POST['masjid_id']) : 0;

    if (!$user_id || !$masjid_id) {
        wp_send_json_error(['message' => 'بيانات غير صحيحة']);
    }

    $meta_key = 'user_meta_masjids_follow';
    $follows = get_user_meta($user_id, $meta_key, true);
    $follows = is_array($follows) ? $follows : [];

    if (in_array($masjid_id, $follows)) {
        $follows = array_diff($follows, [$masjid_id]);
        update_user_meta($user_id, $meta_key, array_values($follows));
        $is_following = false;
    } else {
        $follows[] = $masjid_id;
        update_user_meta($user_id, $meta_key, array_unique($follows));
        $is_following = true;
    }

    // دالة لحساب عدد المتابعين - تأكد أنها موجودة
    $followers_count = get_masjid_followers_count($masjid_id);

    wp_send_json_success([
        'is_following' => $is_following,
        'total' => $followers_count
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_follow_script');

function enqueue_follow_script() {
    wp_enqueue_script('masjid-follow', get_template_directory_uri() . '/assets/js/follow-masjid.js', ['jquery'], null, true);
    wp_localize_script('masjid-follow', 'masjidFollowData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('toggle_follow_masjid'),
    ]);
}
if (!function_exists('get_masjid_followers_count')) {
    function get_masjid_followers_count($masjid_id) {
        $args = [
            'meta_key'   => 'user_meta_masjids_follow',
            'meta_value' => $masjid_id,
            'meta_compare' => 'LIKE',
            'number' => -1,
            'fields' => 'ID'
        ];

        $user_query = new WP_User_Query($args);
        return $user_query->get_total();
    }
}

// ************************* USer Notifications ******************* //
// إنشاء جدول الإشعارات
function create_notifications_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'notifications';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        masjid_id BIGINT(20) UNSIGNED NOT NULL,
        post_id BIGINT(20) UNSIGNED NOT NULL,
        post_type VARCHAR(50) NOT NULL,
        title TEXT NOT NULL,
        seen TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_setup_theme', 'create_notifications_table');


// إشعار المتابعين عند نشر أو تحديث بوست
function notify_followers_on_publish_or_update($post_id, $post, $update) {
    if (wp_is_post_revision($post_id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
        return;
    }

    $allowed_post_types = ['donations', 'masjid-to-masjid', 'funerals', 'lessons', 'live-feed'];
    if (!in_array($post->post_type, $allowed_post_types)) {
        return;
    }

    if ($post->post_status !== 'publish') {
        return;
    }

    $author_id = $post->post_author;
    $author = get_userdata($author_id);

    if (!in_array('masjid', (array) $author->roles)) {
        return;
    }

    global $wpdb;
    $meta_key = 'user_meta_masjids_follow';

    $followers = get_users([
        'role' => 'subscriber',
        'meta_query' => [
            [
                'key'     => $meta_key,
                'value'   => sprintf('i:%d;', $author_id),
                'compare' => 'LIKE'
            ]
        ]
    ]);

    $table_name = $wpdb->prefix . 'notifications';
    $post_type_label = get_post_type_object($post->post_type)->labels->singular_name;
    $action_text = $update ? 'The masjid has updated' : 'The masjid has published';
    $notification_title = $post_type_label . " Notification";
    $notification_content = $action_text . ' ' . $post_type_label;
    $notification_url = get_permalink($post_id);

    $user_ids = [];

    foreach ($followers as $user) {
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND post_id = %d",
            $user->ID, $post_id
        ));

        if (!$exists) {
            $wpdb->insert($table_name, [
                'user_id'    => $user->ID,
                'masjid_id'  => $author_id,
                'post_id'    => $post_id,
                'post_type'  => $post->post_type,
                'title'      => $notification_content,
                'seen'       => 0,
                'created_at' => current_time('mysql'),
            ]);
        }

        $user_ids[] = strval($user->ID);
    }

    // إرسال الإشعارات عبر OneSignal + Firebase
    if (!empty($user_ids)) {
        send_onesignal_notification($notification_title, $notification_content, $notification_url, $user_ids);
        send_firebase_notification($notification_title, $notification_content, $notification_url, $user_ids);
    }
}
add_action('save_post', 'notify_followers_on_publish_or_update', 10, 3);


// دالة OneSignal
function send_onesignal_notification($title, $message, $url, $user_ids) {
    $fields = [
        'app_id' => '52d93725-02ac-42bb-9b78-2e11761cd7e4',
        'include_external_user_ids' => $user_ids,
        'headings' => ['en' => $title],
        'contents' => ['en' => $message],
        'url' => $url
    ];

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ZTBmNjNmMjctMDUzYS00ZTI2LWI3MDEtNDM1NGMyODdjMDY4'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_exec($ch);
    curl_close($ch);
}


// دالة Firebase
function send_firebase_notification($title, $message, $url, $user_ids) {
    $serverKey = "YOUR_FIREBASE_SERVER_KEY"; // 🔑 غيرها بسيرفر كي بتاع Firebase
    $firebaseUrl = "https://fcm.googleapis.com/fcm/send";

    foreach ($user_ids as $user_id) {
        $token = get_user_meta($user_id, 'fcm_token', true);
        if (!$token) continue;

        $data = [
            "to" => $token,
            "notification" => [
                "title" => $title,
                "body"  => $message,
                "click_action" => $url,
                "sound" => "default"
            ],
            "data" => [
                "url" => $url,
                "post_type" => "notification",
                "user_id" => $user_id,
            ]
        ];

        $headers = [
            "Authorization: key=$serverKey",
            "Content-Type: application/json"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);

        // ممكن نعمل log عشان نتابع
        // error_log("FCM Response: " . $response);
    }
}

// ************* Admin Bar **************//
add_filter('show_admin_bar', function () {
    return current_user_can('administrator');
});

add_action('admin_init', function () {
    if ( current_user_can('administrator') ) return;
    if ( wp_doing_ajax() ) return;

    // اسم الملف الجاري تنفيذه داخل /wp-admin/
    $admin_script = isset($_SERVER['PHP_SELF']) ? basename($_SERVER['PHP_SELF']) : '';

    // اسمح بملفات المعالجة
    $allow = ['admin-post.php', 'admin-ajax.php'];
    if ( in_array($admin_script, $allow, true) ) return;

    // أي صفحة أدمن أخرى: رجّع لواجهة الموقع
    wp_safe_redirect(home_url());
    exit;
});
