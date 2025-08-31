<?php
/**
 * Template Name: Masjid Followers
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/masjid-login'));
    exit;
}

$current_user_id = get_current_user_id();
$current_user = wp_get_current_user();

if (!in_array('masjid', (array) $current_user->roles)) {
    wp_die(__('You do not have permission to access this page.', 'text-domain'));
}

get_header();

// استعلام البحث إن وُجد
$search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// جلب المتابعين الذين تحتوي ميتا user_meta "followed_masjids" على ID المسجد الحالي
$args = [
    'role'    => 'subscriber',
    'orderby' => 'display_name',
    'order'   => 'ASC',
    'meta_query' => [
        [
            'key'     => 'user_meta_masjids_follow',
            'value'   => 'i:' . $current_user_id . ';',
            'compare' => 'LIKE'
        ]
    ]
];

if (!empty($search_query)) {
    $args['search'] = '*' . esc_attr($search_query) . '*';
    $args['search_columns'] = ['user_login', 'user_email', 'display_name'];
}

$user_query = new WP_User_Query($args);
$followers = $user_query->get_results();
?>

<main class="my-20">
  <div class="container mx-auto px-4 flex items-start gap-6">
    <!-- Sidebar -->
    <?php get_template_part('template-parts/content-dashboard-sidebar'); ?>

    <div class="wrapper lessons flex-1">
      <div class="head-content flex justify-between items-center flex-wrap gap-4 mb-4">
        <h2 class="sm:text-xl text-lg font-[900]">My Followers</h2>
      </div>

      <div class="content-wrapper">
        <div class="p-4 grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-8">
          <?php if (!empty($followers)) : ?>
            <?php foreach ($followers as $follower) :
                $avatar = get_field('image', 'user_' . $follower->ID);
                $avatar_url = is_array($avatar) && isset($avatar['url']) ? $avatar['url'] : get_avatar_url($follower->ID);
              $phone = get_user_meta($follower->ID, 'phone_number', true); // تعديل لو اسم المفتاح مختلف
              ?>
              <div class="flex items-start gap-4 bg-white py-6 px-4 rounded-lg shadow-md">
                <img
                  src="<?php echo esc_url($avatar_url); ?>"
                  alt="<?php echo esc_attr($follower->display_name); ?>"
                  class="w-12 h-12 rounded-full object-cover"
                />
                <div>
                  <p class="font-semibold text-lg username"><?php echo esc_html($follower->display_name); ?></p>
                  <p class="text-sm user-email"><?php echo esc_html($follower->user_email); ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <p class="col-span-full text-center text-gray-500">No followers found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>
