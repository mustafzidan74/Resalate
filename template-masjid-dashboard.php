<?php
/**
 * Template Name: Masjid Dashboard
 */

// Check if user is not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

// Check if user has 'masjid' role
$user = wp_get_current_user();
if (!in_array('masjid', (array) $user->roles) && !in_array('administrator', (array) $user->roles)) {
    wp_die(__('You do not have permission to access this page.', 'text-domain'));
}

// Get current user ID
$user_id = get_current_user_id();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify nonce for security
    if (!isset($_POST['masjid_info_nonce']) || !wp_verify_nonce($_POST['masjid_info_nonce'], 'update_masjid_info')) {
        wp_die(__('Security check failed', 'text-domain'));
    }
    
    // Update name (display_name)
    if (!empty($_POST['display_name'])) {
        wp_update_user([
            'ID' => $user_id,
            'display_name' => sanitize_text_field($_POST['display_name']),
            'first_name' => sanitize_text_field($_POST['display_name']), // optional
        ]);
    }

    
    // Sanitize and update fields
    update_field('phone', sanitize_text_field($_POST['phone']), 'user_'.$user_id);
    
    // Handle image uploads
    // Load WordPress media functions if not already loaded
    if (!function_exists('media_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
    }
    
    // Handle masjid photo
    if (!empty($_FILES['masjid_photo']['name'])) {
        $attachment_id = media_handle_upload('masjid_photo', 0);
        if (!is_wp_error($attachment_id)) {
            update_field('masjid_photo', $attachment_id, 'user_'.$user_id);
        }
    }
    
    // Handle masjid cover
    if (!empty($_FILES['masjid_cover']['name'])) {
        $attachment_id = media_handle_upload('masjid_cover', 0);
        if (!is_wp_error($attachment_id)) {
            update_field('masjid_cover', $attachment_id, 'user_'.$user_id);
        }
    }
    
    // Update other fields
    update_field('masjid_description', wp_kses_post($_POST['masjid_description']), 'user_'.$user_id);
    
    // Handle checkbox fields
    $services = isset($_POST['masjid_services']) ? $_POST['masjid_services'] : array();
    
    // تنظيف البيانات وتحويلها للتنسيق المتوقع
    $cleaned_services = array();
    foreach ($services as $service) {
        if (!empty($service)) {
            $cleaned_services[] = array(
                'value' => sanitize_text_field($service[0]),
                'label' => sanitize_text_field($service[0])
            );
        }
    }
    
        // حفظ البيانات
     update_field('masjid_servieses', $services, 'user_'.$user_id);
    
    // Handle languages
    $languages_raw = isset($_POST['languages']) ? (array) $_POST['languages'] : [];
    
    $languages_formatted = array_map(function($lang) {
        return ['title' => sanitize_text_field($lang)];
    }, $languages_raw);
    
    update_field('languages', $languages_formatted, 'user_' . $user_id);
    
    // Handle memorization dates
    if (isset($_POST['memorization_dates'])) {
        $dates = array();
        foreach ($_POST['memorization_dates'] as $date) {
            if (!empty($date['date']) && !empty($date['description'])) {
                $dates[] = array(
                    'date' => sanitize_text_field($date['date']),
                    'description' => wp_kses_post($date['description'])
                );
            }
        }
        update_field('memorization_and_lesson_dates', $dates, 'user_'.$user_id);
    }
    
    // Handle google map
    if (!empty($_POST['google_map'])) {
        update_field('google_map', esc_url_raw($_POST['google_map']), 'user_'.$user_id);
    }
    // Handle payment methods
    $payment_methods = array(
        'paypal_user' => sanitize_text_field($_POST['paypal_user']),
        'switch' => array(
            'number' => sanitize_text_field($_POST['switch_number']),
            'url' => esc_url_raw($_POST['switch_url']),
            'qr_code' => isset($_FILES['switch_qr']['name']) && !empty($_FILES['switch_qr']['name']) ? media_handle_upload('switch_qr', 0) : get_field('payment_methods', 'user_'.$user_id)['switch']['qr_code']
        ),
        'bank_account' => array(
            'name' => sanitize_text_field($_POST['bank_name']),
            'account_number' => sanitize_text_field($_POST['bank_account']),
            'iban' => sanitize_text_field($_POST['bank_iban']),
            'swift_code' => sanitize_text_field($_POST['bank_swift'])
        )
    );
    update_field('payment_methods', $payment_methods, 'user_'.$user_id);
    
// ---------- Social Media ----------
if ( ! function_exists('masjid_normalize_url') ) {
    function masjid_normalize_url( $url ) {
        $url = trim((string) $url);
        if ($url === '') return '';
        // رقم واتساب فقط
        if (preg_match('/^(?:\+?\d{7,15})$/', $url)) {
            $digits = preg_replace('/\D+/', '', $url);
            return esc_url_raw('https://wa.me/'.$digits);
        }
        // لو بدون بروتوكول
        if ( ! preg_match('#^https?://#i', $url) ) {
            $url = 'https://' . $url;
        }
        return esc_url_raw($url);
    }
}

$social_media_clean = array(
    'facebook_url'  => masjid_normalize_url( $_POST['facebook_url']  ?? '' ),
    'x_url'         => masjid_normalize_url( $_POST['x_url']         ?? '' ),
    'instagram_url' => masjid_normalize_url( $_POST['instagram_url'] ?? '' ),
    'youtube_url'   => masjid_normalize_url( $_POST['youtube_url']   ?? '' ),
    'tiktok_url'    => masjid_normalize_url( $_POST['tiktok_url']    ?? '' ),
    'linkedin_url'  => masjid_normalize_url( $_POST['linkedin_url']  ?? '' ),
    'telegram_url'  => masjid_normalize_url( $_POST['telegram_url']  ?? '' ),
    'whatsapp_url'  => masjid_normalize_url( $_POST['whatsapp_url']  ?? '' ),
    'snapchat_url'  => masjid_normalize_url( $_POST['snapchat_url']  ?? '' ),
);

// مفتاح الـ Group من ACF export
$SOCIAL_GROUP_KEY = 'field_68a35bcf9062e'; // Social Media group

// احفظ المجموعة مرّة واحدة على user_{$user_id}
$has_any = array_filter($social_media_clean, fn($v) => !empty($v));
update_field($SOCIAL_GROUP_KEY, $has_any ? $social_media_clean : null, 'user_'.$user_id);
    
    // Redirect to avoid resubmission
    wp_redirect(add_query_arg('updated', 'true', get_permalink()));
    exit;
}

// Get current user data
$phone = get_field('phone', 'user_'.$user_id);
$user_data = get_userdata($user_id);
$name = $user_data->display_name;
$email = $user_data->user_email;
$masjid_photo = get_field('masjid_photo', 'user_'.$user_id);
$masjid_cover = get_field('masjid_cover', 'user_'.$user_id);
$masjid_description = get_field('masjid_description', 'user_'.$user_id);
$masjid_services = get_field('masjid_servieses', 'user_'.$user_id) ?: array();
$languages = get_field('languages', 'user_'.$user_id) ?: array();
$memorization_dates = get_field('memorization_and_lesson_dates', 'user_'.$user_id) ?: array();
$google_map_url = get_field('google_map', 'user_'.$user_id);
$payment_methods = get_field('payment_methods', 'user_'.$user_id);
$social_media = get_field('social_media', 'user_'.$user_id) ?: array();

get_header();
?>

<main class="my-20">
<div class="container mx-auto px-4 flex items-start gap-6">
  <!-- Sidebar -->
    <?php get_template_part('template-parts/content-dashboard-sidebar'); ?>
  <div class="wrapper flex-1">
    <div class="head-content">
      <h2 class="sm:text-xl text-lg font-[900] mb-4">
        Update your account Information
      </h2>
    </div>

    <?php if (isset($_GET['updated'])): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">Your information has been updated.</span>
      </div>
    <?php endif; ?>

    <form action="" id="account-info" class="flex flex-col gap-6" method="post" enctype="multipart/form-data">
      <?php wp_nonce_field('update_masjid_info', 'masjid_info_nonce'); ?>
      
        <div class="input-wrapper py-8 px-4 rounded-lg">
          <label for="full-name" class="inline-block mb-2">Full Name</label>
          <input
            type="text"
            id="full-name"
            name="display_name"
            placeholder="e.g. Mohammed Ali"
            class="w-full px-4 py-2 text-sm rounded"
            value="<?php echo esc_attr($name); ?>"
          />
        </div>
        
        <div class="input-wrapper py-8 px-4 rounded-lg">
          <label for="email-address" class="inline-block mb-2">Email Address</label>
          <input
            type="email"
            id="email-address"
            name="email"
            class="w-full px-4 py-2 text-sm rounded bg-gray-100 cursor-not-allowed"
            value="<?php echo esc_attr($email); ?>"
            readonly
          />
        </div>
      
      
      <div class="input-wrapper py-8 px-4 rounded-lg">
        <label for="phone-number" class="inline-block mb-2">Phone Number</label>
        <input
          type="tel"
          id="phone-number"
          name="phone"
          placeholder="e.g. +1 234 567 8900"
          class="w-full px-4 py-2 text-sm rounded"
          value="<?php echo esc_attr($phone); ?>"
        />
        <p id="phoneError" class="text-red-500 text-sm mt-1 hidden">
          Please enter a valid phone number.
        </p>
      </div>

      <div class="input-wrapper py-8 px-4 rounded-lg">
        <div class="head-content">
          <h2 class="sm:text-xl text-lg font-[600] mb-4">Masjid Photo</h2>
        </div>

        <label
          for="profile-image"
          class="success-btn inline-block font-bold mb-2 cursor-pointer py-4 px-8 rounded-full"
        >
          Upload Profile Image
        </label>

        <input
          type="file"
          id="profile-image"
          accept="image/*"
          name="masjid_photo"
          class="hidden"
        />

        <p id="imageError" class="text-red-500 text-sm mt-1 hidden">
          Please select a valid image file (jpg, png, etc.).
        </p>

        <div id="imagePreview" class="relative mt-4 <?php echo empty($masjid_photo) ? 'hidden' : ''; ?> w-fit">
          <?php if (!empty($masjid_photo)): ?>
            <button
              type="button"
              class="absolute -top-2 -right-2 text-white z-10"
              id="removeProfileImage"
              title="Remove image"
            >
              <i class="fa-solid fa-circle-xmark text-red-500 bg-white text-xl"></i>
            </button>
            <img
              src="<?php echo esc_url($masjid_photo['url']); ?>"
              alt="Preview"
              class="w-16 h-16 object-cover rounded border"
            />
          <?php endif; ?>
        </div>
      </div>

      <div class="input-wrapper py-8 px-4 rounded-lg">
        <div class="head-content">
          <h2 class="sm:text-xl text-lg font-[600] mb-4">Masjid Cover</h2>
        </div>

        <label
          for="cover-image"
          class="success-btn inline-block font-bold mb-2 cursor-pointer py-4 px-8 rounded-full"
        >
          Upload Cover Image
        </label>

        <input
          type="file"
          id="cover-image"
          accept="image/*"
          name="masjid_cover"
          class="hidden"
        />

        <p id="coverError" class="text-red-500 text-sm mt-1 hidden">
          Please select a valid image file (jpg, png, etc.).
        </p>

        <div id="coverPreview" class="relative mt-4 <?php echo empty($masjid_cover) ? 'hidden' : ''; ?> w-fit">
          <?php if (!empty($masjid_cover)): ?>
            <button
              type="button"
              class="absolute -top-2 -right-2 text-sm z-10"
              id="removeCoverImage"
              title="Remove image"
            >
              <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
            </button>
            <img
              src="<?php echo esc_url($masjid_cover['url']); ?>"
              alt="Cover Preview"
              class="w-16 h-16 object-cover rounded border"
            />
          <?php endif; ?>
        </div>
      </div>

<div class="input-wrapper py-8 px-4 rounded-lg">
  <div class="head-content">
    <h2 class="sm:text-xl text-lg font-[600] mb-4">Masjid Description</h2>
  </div>

  <div id="description-toolbar">
    <select class="ql-header">
      <option value="">Paragraph</option>
      <option value="1">H1</option>
      <option value="2">H2</option>
      <option value="3">H3</option>
    </select>
    <button class="ql-bold"></button>
    <button class="ql-italic"></button>
    <button class="ql-underline"></button>
    <button class="ql-link"></button>
    <button class="ql-list" value="ordered"></button>
    <button class="ql-list" value="bullet"></button>
    <button class="ql-clean"></button>
  </div>

  <!-- ⛔ مهم: مفيش HTML جوّا الكونتينر -->
  <div id="description-container" style="height:300px" class="border rounded bg-white"></div>

  <!-- المحتوى الأصلي يتخزن هنا فقط -->
  <textarea name="masjid_description" class="hidden" id="description-textarea">
    <?php echo wp_kses_post($masjid_description); ?>
  </textarea>
</div>

    <div class="input-wrapper py-8 px-4 rounded-lg">
        <label class="inline-block mb-2 font-bold text-lg">Masjid Services</label>
        <div class="space-y-2">
            <?php
            $services_options = array(
                "Friday Sermon",
                "Women's Prayer Area",
                "Funeral Washing Facility",
                "Qur'an Memorization Circles",
                "Women's Lessons",
                "Scientific Lessons",
                "Guidance Talks",
                "Cultural And Seasonal Competitions For Residents"
            );
            
            // استرجاع البيانات المحفوظة
            $saved_services = get_field('masjid_servieses', 'user_'.$user_id) ?: array();
            
            $selected_services = array();
            foreach ($saved_services as $service) {
                if (is_array($service)) {
                    // حل إضافي للتعامل مع البيانات غير الصحيحة
                    $value = is_array($service['value']) ? '' : ($service['value'] ?? '');
                    $label = is_array($service['label']) ? '' : ($service['label'] ?? '');
                    $selected_services[] = !empty($value) ? $value : $label;
                } else {
                    $selected_services[] = $service;
                }
            }
            $selected_services = array_filter($selected_services);
            
            foreach ($services_options as $service): ?>
                <label class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        name="masjid_services[]"
                        value="<?php echo esc_attr($service); ?>"
                        class="accent-primary"
                        <?php echo in_array($service, $selected_services) ? 'checked' : ''; ?>
                    />
                    <?php echo esc_html($service); ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
 
<?php
$language_repeater = get_field('languages', 'user_' . $user_id);
$selected_languages = [];

if ($language_repeater && is_array($language_repeater)) {
    foreach ($language_repeater as $row) {
        if (!empty($row['title'])) {
            $selected_languages[] = $row['title'];
        }
    }
}

?>
    
      <div class="input-wrapper py-8 px-4 rounded-lg">
        <label for="languages" class="inline-block mb-2 font-bold text-lg">Languages</label>
        <select id="languages" name="languages[]" multiple placeholder="Type and select any language...">
          <?php
          $all_languages = array(
            "Arabic", "English", "French", "Urdu", "Turkish", "Spanish", 
            "Bengali", "Hindi", "Malay", "Russian", "Persian", "Chinese"
          );
          
          foreach ($all_languages as $lang): ?>
            <option value="<?php echo esc_attr($lang); ?>" <?php echo in_array($lang, $selected_languages) ? 'selected' : ''; ?>>
              <?php echo esc_html($lang); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

    <div class="input-wrapper py-8 px-4 rounded-lg">
        <div class="head-content">
            <h2 class="sm:text-xl text-lg font-[600] mb-4">
                Memorization and lesson dates throughout the month
            </h2>
        </div>
    
        <div class="content-container flex flex-col gap-12" id="memorization-dates-container">
            <?php if (empty($memorization_dates)): ?>
                <div class="wrapper" data-index="0">
                    <div class="date-picker mb-5 relative">
                        <label
                            for="memorization-date-0"
                            class="date-label success-btn inline-block font-bold mb-2 cursor-pointer py-4 px-8 rounded-full transition"
                        >
                            Select a Date
                        </label>
                        <input
                            type="date"
                            id="memorization-date-0"
                            name="memorization_dates[0][date]"
                            class="date-input absolute top-0 left-0 opacity-0 w-0 h-0"
                        />
                        <p id="selected-date-0" class="selected-date mt-1 font-medium hidden"></p>
                    </div>
    
                    <textarea 
                        name="memorization_dates[0][description]" 
                        class="w-full px-4 py-2 border rounded" 
                        rows="5"
                        placeholder="Enter description"
                    ></textarea>
                    
                    <button type="button" class="delete-row-btn text-red-600 mt-3 font-semibold">
                        Delete
                    </button>

                </div>
            <?php else: ?>
                <?php foreach ($memorization_dates as $index => $date): ?>
                    <div class="wrapper" data-index="<?php echo $index; ?>">
                        <div class="date-picker mb-5 relative">
                            <label
                                for="memorization-date-<?php echo $index; ?>"
                                class="date-label success-btn inline-block font-bold mb-2 cursor-pointer py-4 px-8 rounded-full transition"
                            >
                                Select a Date
                            </label>
                            <input
                                type="date"
                                id="memorization-date-<?php echo $index; ?>"
                                name="memorization_dates[<?php echo $index; ?>][date]"
                                class="date-input absolute top-0 left-0 opacity-0 w-0 h-0"
                                value="<?php echo esc_attr(date('Y-m-d', strtotime($date['date']))); ?>"
                            />
                            <p id="selected-date-<?php echo $index; ?>" class="selected-date mt-1 font-medium">
                                Selected Date: <?php echo esc_html($date['date']); ?>
                            </p>
                        </div>
    
                        <textarea 
                            name="memorization_dates[<?php echo $index; ?>][description]" 
                            class="w-full px-4 py-2 border rounded" 
                            rows="5"
                            placeholder="Enter description"
                        ><?php echo esc_textarea($date['description']); ?></textarea>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    
        <div class="add-row-btn flex justify-end mt-5">
            <button
                type="button"
                class="primary-btn inline-block font-bold mb-2 cursor-pointer py-4 px-8 rounded-lg transition"
                id="add-memorization-date"
            >
                Add Row
            </button>
        </div>
    </div>

    <div class="input-wrapper py-8 px-4 rounded-lg">
        <div class="head-content">
            <h2 class="sm:text-xl text-lg font-[600] mb-4">Google Map</h2>
        </div>
    
        <input
            type="text"
            id="map-link"
            name="google_map"
            placeholder="رابط التضمين من خرائط جوجل (Embed)"
            class="w-full px-4 py-3 rounded mb-2"
            value="<?php echo esc_url($google_map_url); ?>"
        />
    
        <p id="mapError" class="text-red-500 text-sm mt-2 hidden">
            الرابط يجب أن يكون من نوع <code>https://www.google.com/maps/embed?pb=...</code>
        </p>
    
        <div id="mapPreview" class="mt-4 <?php echo empty($google_map_url) ? 'hidden' : ''; ?> border rounded overflow-hidden">
            <?php if (!empty($google_map_url)): ?>
                <iframe
                    id="mapFrame"
                    width="100%"
                    height="300"
                    style="border: 0"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="<?php echo esc_url($google_map_url); ?>"
                ></iframe>
            <?php endif; ?>
        </div>
    </div>

      <div class="head-content">
        <h2 class="sm:text-xl text-lg font-[600]">Payment methods</h2>
      </div>

      <div class="input-wrapper py-8 px-4 rounded-lg">
        <div class="box">
          <h4 class="sm:text-lg font-[600] mb-4">PayPal</h4>
          <label for="paypal-link" class="inline-block mb-2">PayPal Link</label>
          <input
            type="text"
            id="paypal-link"
            placeholder="Enter Your PayPal"
            name="paypal_user"
            class="w-full border px-4 py-3 rounded mb-2"
            value="<?php echo esc_attr($payment_methods['paypal_user'] ?? ''); ?>"
          />
        </div>
      </div>
      
      <div class="input-wrapper py-8 px-4 rounded-lg">
        <div class="box">
          <h4 class="sm:text-lg font-[600] mb-4">Swish</h4>
          <div class="flex flex-col gap-4">
            <div class="box-wrapper">
              <label for="switch-number" class="inline-block mb-2">Swish Number</label>
              <input
                type="text"
                placeholder="Enter Switch Number"
                name="switch_number"
                id="switch-number"
                class="w-full px-4 py-3 rounded"
                value="<?php echo esc_attr($payment_methods['switch']['number'] ?? ''); ?>"
              />
            </div>

            <div class="box-wrapper">
              <label for="switch-url" class="inline-block mb-2">URL</label>
              <input
                type="text"
                placeholder="Enter Swish URL"
                name="switch_url"
                id="switch-url"
                class="w-full px-4 py-3 rounded"
                value="<?php echo esc_attr($payment_methods['switch']['url'] ?? ''); ?>"
              />
            </div>

            <div class="box-wrapper">
              <label
                for="switch-qr"
                class="block font-bold mb-2 cursor-pointer py-4 px-8 border border-[#929292] rounded"
              >
                Upload Swish QR
              </label>

              <input
                type="file"
                id="switch-qr"
                accept="image/*"
                name="switch_qr"
                class="hidden"
              />

              <p id="switchQrError" class="text-red-500 text-sm mt-1 hidden">
                Please select a valid image file (jpg, png, etc.).
              </p>

                <?php
                $qr_raw  = $payment_methods['switch']['qr_code'] ?? null;
                $qr_url  = '';
                if (is_numeric($qr_raw)) {
                  $qr_url = wp_get_attachment_image_url((int)$qr_raw, 'thumbnail');
                } elseif (is_array($qr_raw) && !empty($qr_raw['url'])) {
                  $qr_url = $qr_raw['url'];
                }
                $has_qr = !empty($qr_url);
                ?>
                
                <div id="switchQrPreview" class="relative mt-4 <?php echo $has_qr ? '' : 'hidden'; ?> w-fit">
                  <?php if ($has_qr): ?>
                    <button
                      type="button"
                      class="absolute -top-2 -right-2 text-sm z-10"
                      id="removeSwitchQrImage"
                      title="Remove image"
                    >
                      <i class="fa-solid fa-circle-xmark text-red-500 bg-white text-xl"></i>
                    </button>
                    <img
                      src="<?php echo esc_url($qr_url); ?>"
                      alt="Switch QR Preview"
                      class="w-16 h-16 object-cover rounded border"
                    />
                  <?php endif; ?>
                </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="input-wrapper py-8 px-4 rounded-lg">
        <div class="box">
          <div class="box-wrapper space-y-4">
            <h4 class="sm:text-lg font-[600] mb-4">Bank Account</h4>
            <div>
              <label for="bank-name" class="inline-block mb-2">Account Holder Name</label>
              <input
                type="text"
                id="bank-name"
                name="bank_name"
                placeholder="Enter Name"
                class="w-full border px-4 py-3 rounded"
                value="<?php echo esc_attr($payment_methods['bank_account']['name'] ?? ''); ?>"
              />
            </div>
            <div>
              <label for="bank-account" class="inline-block mb-2">Account Number</label>
              <input
                type="text"
                id="bank-account"
                name="bank_account"
                placeholder="Enter Account Number"
                class="w-full border px-4 py-3 rounded"
                value="<?php echo esc_attr($payment_methods['bank_account']['account_number'] ?? ''); ?>"
              />
            </div>
            <div>
              <label for="bank-iban" class="inline-block mb-2">IBAN</label>
              <input
                type="text"
                id="bank-iban"
                name="bank_iban"
                placeholder="Enter IBAN"
                class="w-full border px-4 py-3 rounded"
                value="<?php echo esc_attr($payment_methods['bank_account']['iban'] ?? ''); ?>"
              />
            </div>
            <div>
              <label for="bank-swift" class="inline-block mb-2">SWIFT Code</label>
              <input
                type="text"
                id="bank-swift"
                name="bank_swift"
                placeholder="Enter SWIFT Code"
                class="w-full border px-4 py-3 rounded"
                value="<?php echo esc_attr($payment_methods['bank_account']['swift_code'] ?? ''); ?>"
              />
            </div>
          </div>
        </div>
      </div>


      <div class="head-content">
        <h2 class="sm:text-xl text-lg font-[600] mb-2">Social Media</h2>
      </div>

      <div class="input-wrapper py-8 px-4 rounded-lg">
        <div class="grid md:grid-cols-2 grid-cols-1 gap-5">
          <?php
            // Helper inline
            $sm = function($key) use ($social_media) { return esc_attr($social_media[$key] ?? ''); };
            

          ?>

          <div>
            <label for="facebook_url" class="inline-block mb-2">Facebook URL</label>
            <input type="text" id="facebook_url" name="facebook_url" class="w-full px-4 py-3 rounded"
                   placeholder="https://facebook.com/yourpage"
                   value="<?php echo $sm('facebook_url'); ?>">
          </div>

          <div>
            <label for="x_url" class="inline-block mb-2">X (Twitter) URL</label>
            <input type="text" id="x_url" name="x_url" class="w-full px-4 py-3 rounded"
                   placeholder="https://x.com/yourhandle"
                   value="<?php echo $sm('x_url'); ?>">
          </div>

          <div>
            <label for="instagram_url" class="inline-block mb-2">Instagram URL</label>
            <input type="text" id="instagram_url" name="instagram_url" class="w-full px-4 py-3 rounded"
                   placeholder="https://instagram.com/yourhandle"
                   value="<?php echo $sm('instagram_url'); ?>">
          </div>

          <div>
            <label for="youtube_url" class="inline-block mb-2">YouTube URL</label>
            <input type="text" id="youtube_url" name="youtube_url" class="w-full px-4 py-3 rounded"
                   placeholder="https://youtube.com/@yourchannel"
                   value="<?php echo $sm('youtube_url'); ?>">
          </div>

          <div>
            <label for="tiktok_url" class="inline-block mb-2">TikTok URL</label>
            <input type="text" id="tiktok_url" name="tiktok_url" class="w-full px-4 py-3 rounded"
                   placeholder="https://www.tiktok.com/@yourhandle"
                   value="<?php echo $sm('tiktok_url'); ?>">
          </div>

          <div>
            <label for="linkedin_url" class="inline-block mb-2">LinkedIn URL</label>
            <input type="text" id="linkedin_url" name="linkedin_url" class="w-full px-4 py-3 rounded"
                   placeholder="https://www.linkedin.com/company/yourpage"
                   value="<?php echo $sm('linkedin_url'); ?>">
          </div>

          <div>
            <label for="telegram_url" class="inline-block mb-2">Telegram URL</label>
            <input type="text" id="telegram_url" name="telegram_url" class="w-full px-4 py-3 rounded"
                   placeholder="https://t.me/yourchannel"
                   value="<?php echo $sm('telegram_url'); ?>">
          </div>

          <div>
            <label for="whatsapp_url" class="inline-block mb-2">WhatsApp</label>
            <input type="text" id="whatsapp_url" name="whatsapp_url" class="w-full px-4 py-3 rounded"
                   placeholder="رقم فقط مثل: +201234567890 أو رابط wa.me"
                   value="<?php echo $sm('whatsapp_url'); ?>">
          </div>

          <div>
            <label for="snapchat_url" class="inline-block mb-2">Snapchat URL</label>
            <input type="text" id="snapchat_url" name="snapchat_url" class="w-full px-4 py-3 rounded"
                   placeholder="https://www.snapchat.com/add/yourhandle"
                   value="<?php echo $sm('snapchat_url'); ?>">
          </div>
        </div>

        <?php
          // معاينة سريعة للأيقونات لو فيه أي روابط محفوظة:
          $icons = array(
            'facebook_url'  => ['label' => 'Facebook',  'icon' => 'fa-facebook-f'],
            'x_url'         => ['label' => 'X',         'icon' => 'fa-x-twitter'], // fallback fa-twitter
            'instagram_url' => ['label' => 'Instagram', 'icon' => 'fa-instagram'],
            'youtube_url'   => ['label' => 'YouTube',   'icon' => 'fa-youtube'],
            'tiktok_url'    => ['label' => 'TikTok',    'icon' => 'fa-tiktok'],
            'linkedin_url'  => ['label' => 'LinkedIn',  'icon' => 'fa-linkedin-in'],
            'telegram_url'  => ['label' => 'Telegram',  'icon' => 'fa-telegram'],
            'whatsapp_url'  => ['label' => 'WhatsApp',  'icon' => 'fa-whatsapp'],
            'snapchat_url'  => ['label' => 'Snapchat',  'icon' => 'fa-snapchat'],
          );
          $has_icon = false;
          foreach ($icons as $k => $_) {
            if (!empty($social_media[$k])) { $has_icon = true; break; }
          }
        ?>

        <?php if ($has_icon): ?>
          <div class="mt-6">
            <p class="mb-3 font-semibold">Preview:</p>
            <ul class="flex flex-wrap items-center gap-3">
              <?php foreach ($icons as $key => $meta):
                $url = $social_media[$key] ?? '';
                if (!$url) continue;
                $brandClass = 'fa-brands ' . $meta['icon'];
                // fallback لـ X لو ما فيه fa-x-twitter
                if ($meta['icon'] === 'fa-x-twitter') {
                  $brandClass = 'fa-brands fa-x-twitter';
                }
              ?>
                <li>
                  <a href="<?php echo esc_url($url); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full border"
                     target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr($meta['label']); ?>">
                    <i class="<?php echo esc_attr($brandClass); ?> text-lg"></i>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>

      <button
        type="submit"
        class="success-btn px-8 py-3 text-lg font-bold rounded w-fit self-end transition-colors mt-4 inline-block"
      >
        Save
      </button>
    </form>
  </div>
</div>
</main>

<?php
get_footer();
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  /* ===========================
     1) Quill (Masjid Description) — Lazy init
     =========================== */
  let quillDesc = null;
  const descContainer = document.getElementById("description-container");
  const descToolbar   = document.getElementById("description-toolbar");
  const descTextarea  = document.getElementById("description-textarea");
  const form          = document.getElementById("account-info");

  function initQuillDesc() {
    if (quillDesc || !descContainer) return; // prevent re-init
    quillDesc = new Quill("#description-container", {
      theme: "snow",
      modules: { toolbar: "#description-toolbar" }
    });

    // seed with existing HTML quickly
    const initialHTML = (descTextarea && descTextarea.value) ? descTextarea.value : "";
    if (initialHTML) {
      quillDesc.clipboard.dangerouslyPasteHTML(initialHTML);
    }

    // keep textarea in sync
    quillDesc.on("text-change", function () {
      if (descTextarea) descTextarea.value = quillDesc.root.innerHTML;
    });
  }

  // Lazy: init when the editor first becomes visible
  if (descContainer) {
    if ("IntersectionObserver" in window) {
      const io = new IntersectionObserver((entries, obs) => {
        if (entries[0].isIntersecting) {
          initQuillDesc();
          obs.disconnect();
        }
      }, { threshold: 0.1 });
      io.observe(descContainer);
    } else {
      // Fallback: init on first click
      descContainer.addEventListener("click", initQuillDesc, { once: true });
    }
  }

  // Ensure content saved even if user never opened the editor
  if (form) {
    form.addEventListener("submit", function () {
      if (quillDesc && descTextarea) {
        descTextarea.value = quillDesc.root.innerHTML;
      }
    });
  }

  /* ===========================
     2) TomSelect (Languages)
     =========================== */
  if (document.getElementById("languages")) {
    const languageSelect = document.getElementById("languages");
    languageSelect.innerHTML = "";

    const allLanguages = [
      "Arabic","English","French","Urdu","Turkish","Spanish",
      "Bengali","Hindi","Malay","Russian","Persian","Chinese","Swedish"
    ];

    allLanguages.forEach((lang) => {
      const option = document.createElement("option");
      option.value = lang;
      option.text  = lang;
      languageSelect.appendChild(option);
    });

    const tomSelect = new TomSelect("#languages", {
      maxItems: null,
      create: true,
      persist: false,
      plugins: ["remove_button"],
      placeholder: "Type and select any language..."
    });

    <?php if (!empty($selected_languages)): ?>
      tomSelect.setValue(<?php echo json_encode($selected_languages); ?>);
    <?php endif; ?>
  }

  /* ===========================
     3) Memorization dates
     =========================== */
  const container = document.getElementById("memorization-dates-container");
  const addBtn    = document.getElementById("add-memorization-date");

  if (container && addBtn) {
    function lazyInitRowQuill(editorContainer, toolbar, textarea) {
      let q = null;
      function init() {
        if (q) return;
        q = new Quill(editorContainer, {
          theme: "snow",
          modules: { toolbar }
        });
        if (textarea.value) q.clipboard.dangerouslyPasteHTML(textarea.value);
        q.on("text-change", function () {
          textarea.value = q.root.innerHTML;
        });
      }
      // init on first interaction
      editorContainer.addEventListener("click", init, { once: true });
      // or when visible
      if ("IntersectionObserver" in window) {
        const io = new IntersectionObserver((entries, obs) => {
          if (entries[0].isIntersecting) {
            init();
            obs.disconnect();
          }
        }, { threshold: 0.15 });
        io.observe(editorContainer);
      }
    }

    function initDateRow(wrapper, index) {
      const dateInput  = wrapper.querySelector(".date-input");
      const dateLabel  = wrapper.querySelector(".date-label");
      const dateOutput = wrapper.querySelector(".selected-date");

      // trigger native date picker
      if (dateLabel && dateInput) {
        dateLabel.addEventListener("click", () => {
          dateInput.showPicker?.() || dateInput.click();
        });
      }

      if (dateInput && dateOutput) {
        const setOut = () => {
          if (dateInput.value) {
            const formatted = new Date(dateInput.value).toLocaleDateString();
            dateOutput.textContent = `Selected Date: ${formatted}`;
            dateOutput.classList.remove("hidden");
          } else {
            dateOutput.classList.add("hidden");
          }
        };
        dateInput.addEventListener("change", setOut);
        if (dateInput.value) setOut();
      }

      // delete row
      const deleteBtn = wrapper.querySelector(".delete-row-btn");
      if (deleteBtn) {
        deleteBtn.addEventListener("click", () => wrapper.remove());
      }

      // optional Quill per-row (only for dynamically-added ones)
      const editorContainer = wrapper.querySelector(".date-container");
      const toolbar         = wrapper.querySelector(".date-toolbar");
      const textarea        = wrapper.querySelector("textarea");

      if (editorContainer && toolbar && textarea) {
        lazyInitRowQuill(editorContainer, toolbar, textarea);
      }
    }

    // init existing rows
    container.querySelectorAll(".wrapper").forEach((w, i) => initDateRow(w, i));

    // add new row
    addBtn.addEventListener("click", () => {
      const index = container.querySelectorAll(".wrapper").length;
      const wrapper = document.createElement("div");
      wrapper.className = "wrapper";
      wrapper.dataset.index = index;

      wrapper.innerHTML = `
        <div class="date-picker mb-5 relative">
          <label for="memorization-date-${index}"
                 class="date-label success-btn inline-block font-bold mb-2 cursor-pointer py-4 px-8 rounded-full transition">
            Select a Date
          </label>
          <input type="date" id="memorization-date-${index}"
                 name="memorization_dates[${index}][date]"
                 class="date-input absolute top-0 left-0 opacity-0 w-0 h-0" />
          <p id="selected-date-${index}" class="selected-date mt-1 font-medium hidden"></p>
        </div>

        <div class="toolbar-container">
          <div class="date-toolbar" id="date-toolbar-${index}">
            <select class="ql-header">
              <option value="1">Heading 1</option>
              <option value="2">Heading 2</option>
              <option value="3">Heading 3</option>
              <option value="4">Heading 4</option>
              <option value="5">Heading 5</option>
              <option value="6">Heading 6</option>
              <option value="">Paragraph</option>
            </select>
            <button class="ql-bold"></button>
            <button class="ql-italic"></button>
            <button class="ql-underline"></button>
            <select class="ql-color"></select>
            <select class="ql-background"></select>
            <button class="ql-link"></button>
            <select class="ql-align"></select>
            <button class="ql-list" value="ordered"></button>
            <button class="ql-list" value="bullet"></button>
            <button class="ql-image"></button>
            <button class="ql-clean"></button>
          </div>
        </div>

        <div class="date-container" id="date-container-${index}" style="height:300px"></div>
        <textarea name="memorization_dates[${index}][description]"
                  class="hidden" id="date-textarea-${index}"></textarea>

        <button type="button" class="delete-row-btn text-red-600 mt-3 font-semibold">Delete</button>
      `;

      container.appendChild(wrapper);
      initDateRow(wrapper, index);
    });
  }

  /* ===========================
     4) Phone input sanitization
     =========================== */
  const phoneInput = document.getElementById("phone-number");
  if (phoneInput) {
    phoneInput.addEventListener("input", () => {
      phoneInput.value = phoneInput.value.replace(/[^\d+]/g, "");
    });
  }

  /* ===========================
     5) Image uploads (preview/remove)
     =========================== */
  function setupImageUpload(inputId, previewId, errorId, removeBtnId) {
    const input    = document.getElementById(inputId);
    const preview  = document.getElementById(previewId);
    const error    = document.getElementById(errorId);
    const removeBtn= document.getElementById(removeBtnId);

    if (!input || !preview || !error || !removeBtn) return;

    let img = preview.querySelector("img");

    input.addEventListener("change", function () {
      const file = this.files[0];
      if (!file) return;

      if (!file.type.startsWith("image/")) {
        error.classList.remove("hidden");
        preview.classList.add("hidden");
        if (img) img.src = "";
        return;
      }

      error.classList.add("hidden");

      if (!img) {
        img = document.createElement("img");
        img.className = "w-16 h-16 object-cover rounded border";
        preview.appendChild(img);
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        img.src = e.target.result;
        preview.classList.remove("hidden");
      };
      reader.readAsDataURL(file);
    });

    removeBtn.addEventListener("click", function () {
      if (img) img.src = "";
      preview.classList.add("hidden");
      input.value = "";
    });
  }

  setupImageUpload("profile-image", "imagePreview", "imageError", "removeProfileImage");
  setupImageUpload("cover-image",   "coverPreview",  "coverError", "removeCoverImage");
  setupImageUpload("switch-qr",     "switchQrPreview","switchQrError","removeSwitchQrImage");

  /* ===========================
     6) Google Map (embed preview)
     =========================== */
  const mapInput   = document.getElementById("map-link");
  const mapError   = document.getElementById("mapError");
  const mapPreview = document.getElementById("mapPreview");
  const mapFrame   = document.getElementById("mapFrame");

  if (mapInput && mapError && mapPreview && mapFrame) {
    const embedUrlRegex = /^https:\/\/www\.google\.com\/maps\/embed\?pb=/;
    function updateMapPreview(value) {
      const trimmed = value.trim();
      if (embedUrlRegex.test(trimmed)) {
        mapError.classList.add("hidden");
        mapFrame.src = trimmed;
        mapPreview.classList.remove("hidden");
      } else {
        mapFrame.src = "";
        mapPreview.classList.add("hidden");
        mapError.classList.remove("hidden");
      }
    }
    mapInput.addEventListener("input", function () {
      updateMapPreview(this.value);
    });
    if (mapInput.value) updateMapPreview(mapInput.value);
  }
});
</script>
