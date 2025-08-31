<?php
/**
 * Template Name: User Info Editor
 */

if (!is_user_logged_in()) {
    wp_redirect(site_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
if (!in_array('subscriber', $current_user->roles)) {
    wp_die('Access Denied');
}

$user_id = get_current_user_id();
$user_login = $current_user->user_login;
$user_email = $current_user->user_email;

$phone   = get_field('phone_number', 'user_' . $user_id);
$profile_img = get_field('image', 'user_' . $user_id);
?>

<?php get_header(); ?>
<script>
  var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>

<main class="my-20">
  <div class="container mx-auto px-4">
    <div class="user-profile">
      <form method="post" enctype="multipart/form-data" class="space-y-10">
        <!-- PROFILE SECTION -->
        <div class="bg-white rounded-2xl p-6 shadow-md">
          <h2 class="text-2xl font-bold mb-6">User Information</h2>
          <div class="grid md:grid-cols-2 grid-cols-1 gap-6">
            <div>
              <label class="block mb-1 font-semibold">Username</label>
              <input type="text" name="user_login" value="<?= esc_attr($user_login) ?>" class="w-full border rounded-xl p-3" required />
            </div>

            <div>
              <label class="block mb-1 font-semibold">Email (read-only)</label>
              <input type="email" value="<?= esc_attr($user_email) ?>" class="w-full border rounded-xl p-3 bg-gray-100" readonly />
            </div>

            <div>
              <label class="block mb-1 font-semibold">Phone Number</label>
              <input type="text" name="phone_number" value="<?= esc_attr($phone) ?>" class="w-full border rounded-xl p-3" />
            </div>

            <div class="col-span-2">
              <label class="block mb-1 font-semibold">Profile Image</label>
              <?php if ($profile_img): ?>
                <img src="<?= esc_url(is_array($profile_img) ? $profile_img['url'] : wp_get_attachment_url($profile_img)) ?>" alt="Profile" class="w-[100px] h-[100px] rounded-full object-cover mb-3" />
              <?php endif; ?>
              <input type="file" name="profile_image" accept="image/*" />
            </div>
          </div>
        </div>

        <!-- PASSWORD SECTION -->
        <div class="bg-white rounded-2xl p-6 shadow-md">
          <h2 class="text-2xl font-bold mb-6">Change Password</h2>
          <div class="grid md:grid-cols-2 grid-cols-1 gap-6">
            <div>
              <label class="block mb-1 font-semibold">Current Password</label>
              <input type="password" name="current_password" class="w-full border rounded-xl p-3" />
            </div>
            <div>
              <label class="block mb-1 font-semibold">New Password</label>
              <input type="password" name="new_password" class="w-full border rounded-xl p-3" />
            </div>
            <div>
              <label class="block mb-1 font-semibold">Confirm New Password</label>
              <input type="password" name="confirm_password" class="w-full border rounded-xl p-3" />
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button type="submit" name="update_profile" class="bg-green-600 text-white font-semibold py-3 px-6 rounded-xl hover:bg-green-700 transition">
            Save Changes
          </button>
        </div>
      </form>

      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
          wp_update_user([
              'ID' => $user_id,
              'user_login' => sanitize_text_field($_POST['user_login']),
          ]);

          update_field('phone_number', sanitize_text_field($_POST['phone_number']), 'user_' . $user_id);

          if (!empty($_FILES['profile_image']['name'])) {
              require_once ABSPATH . 'wp-admin/includes/file.php';
              require_once ABSPATH . 'wp-admin/includes/media.php';
              require_once ABSPATH . 'wp-admin/includes/image.php';
              $uploaded = media_handle_upload('profile_image', 0);
              if (!is_wp_error($uploaded)) {
                  update_field('image', $uploaded, 'user_' . $user_id);
              }
          }

          $current_pass = $_POST['current_password'];
          $new_pass     = $_POST['new_password'];
          $confirm_pass = $_POST['confirm_password'];

          if (!empty($current_pass) && !empty($new_pass) && $new_pass === $confirm_pass) {
              if (wp_check_password($current_pass, $current_user->user_pass, $user_id)) {
                  wp_set_password($new_pass, $user_id);
                  echo '<p class="text-green-600 font-bold mt-4">Password updated successfully.</p>';
              } else {
                  echo '<p class="text-red-600 font-bold mt-4">Incorrect current password.</p>';
              }
          } elseif (!empty($new_pass)) {
              echo '<p class="text-red-600 font-bold mt-4">Password confirmation does not match.</p>';
          }

          echo "<script>window.location.href=window.location.href;</script>";
          exit;
      }
      ?>
    </div>
  </div>
</main>

<script>
jQuery(document).ready(function($) {
  $('.select2').select2({ width: '100%' });
});
</script>
<script>
jQuery(document).ready(function ($) {
  // Ajax password update
  $('form').on('submit', function (e) {
    e.preventDefault();

    const currentPass = $('input[name="current_password"]').val();
    const newPass = $('input[name="new_password"]').val();
    const confirmPass = $('input[name="confirm_password"]').val();
    const feedback = $('<div class="ajax-feedback mt-4 font-bold"></div>');
    $('.ajax-feedback').remove(); // Clear old messages
    $(this).append(feedback);

    // Proceed with AJAX only for password
    if (currentPass || newPass || confirmPass) {
      $.post(ajaxurl, {
        action: 'update_user_password',
        current_password: currentPass,
        new_password: newPass,
        confirm_password: confirmPass,
        _wpnonce: '<?php echo wp_create_nonce("ajax_pass_update"); ?>'
      }, function (response) {
        feedback
          .text(response.message)
          .removeClass()
          .addClass('ajax-feedback mt-4 font-bold ' + (response.success ? 'text-green-600' : 'text-red-600'));

        if (response.success) {
          $('input[name="current_password"], input[name="new_password"], input[name="confirm_password"]').val('');
        }
      });
    } else {
      // إذا لا يوجد باسورد، اسمح للنموذج بالمتابعة لتحديث الاسم أو الصورة أو الهاتف
      this.submit();
    }
  });
});
</script>

<?php get_footer(); ?>
