<?php
$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$is_logged_in = is_user_logged_in();
$user_roles = is_array($current_user->roles) ? $current_user->roles : [];
$is_masjid = in_array('masjid', $user_roles);
$is_subscriber = in_array('subscriber', $user_roles);

// صورة المستخدم
if ($is_masjid && $user_id) {
    $avatar = get_field('masjid_photo', 'user_' . $user_id);
    $avatar_url = $avatar['url'] ?? get_avatar_url($user_id);
} elseif ($is_subscriber && $user_id) {
    $avatar = get_field('image', 'user_' . $user_id);
    $avatar_url = is_array($avatar) && isset($avatar['url']) ? $avatar['url'] : get_avatar_url($user_id);
} else {
    $avatar_url = get_avatar_url($user_id);
}

if (isset($_POST['mark_seen'])) {
    $notif_id = absint($_POST['mark_seen']);
    global $wpdb;
    $wpdb->update(
        $wpdb->prefix . 'notifications',
        ['seen' => 1],
        ['id' => $notif_id, 'user_id' => get_current_user_id()]
    );
    wp_redirect($_SERVER['REQUEST_URI']); // تحديث الصفحة بعد الضغط
    exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="shadow-md sticky top-0 py-3 z-30 bg-white">
  <div class="container flex items-center justify-between mx-auto px-4">
    <div class="wrapper flex items-center gap-4">
      <div class="logo">
        <a href="<?php echo esc_url(home_url('/')); ?>">
          <img
            src="<?= get_field("logo", "option"); ?>"
            class="md:w-[150px] w-[120px]"
            alt="<?php bloginfo('name'); ?>"
          />
        </a>
      </div>
    </div>



    <!-- Navbar List -->
    <div class="hidden md:block">
      <?php
      wp_nav_menu(array(
        'theme_location' => 'primary-menu',
        'menu_class'     => 'navbar-list flex gap-6 font-bold',
        'container'      => false
      ));
      ?>
    </div>

    <!-- User/Auth Section -->
    <div class="wrapper flex items-center gap-6">
      <div class="flex items-center gap-6 relative">
        <!-- Notification Bell (Subscribers only) -->
        <?php if ($is_subscriber):
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'notifications';
        $notifications = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC LIMIT 20",
            $user_id
        ));
        
        $unread_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND seen = 0",
            $user_id
        ));

        ?>
            <!-- زر الجرس -->
            <div class="relative">
              <button id="notifBtn" class="relative">
                <svg class="w-6 h-6 text-gray-600 hover:text-blue-600" fill="none" stroke="currentColor"
                  stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M15 17h5l-1.4-1.4C18.4 15.4 18 14.7 18 14V11c0-3-1.6-5.6-4.5-6.3V4a1.5 1.5 0 10-3 0v.7C7.6 5.4 6 7.9 6 11v3c0 .7-.4 1.4-1.6 1.6L3 17h5m4 0v1a2 2 0 104 0v-1h-4" />
                </svg>
                <?php if ($unread_count > 0): ?>
                  <span class="absolute -bottom-1 -right-1 bg-red-500 text-white w-[15px] h-[15px] flex justify-center items-center rounded-full text-[.6rem]"><?= $unread_count; ?></span>
                <?php endif; ?>
              </button>
            
              <!-- Notification Dropdown -->
              <div id="notifMenu" class="hidden absolute right-0 mt-2 w-72 bg-white border rounded shadow-md z-20 max-h-96 overflow-y-auto">
                <ul class="divide-y divide-gray-100">
                  <?php if ($notifications): ?>
                    <?php foreach ($notifications as $notif): ?>
                      <li class="group hover:bg-gray-50 transition-all">
                        <a href="<?= esc_url(get_permalink($notif->post_id)); ?>" class="p-3 block relative">
                          <h4 class="font-semibold text-sm"><?= esc_html($notif->title); ?></h4>
                          <p class="text-xs text-gray-500"><?= date_i18n('Y-m-d H:i', strtotime($notif->created_at)); ?></p>
            
                          <?php if (!$notif->seen): ?>
                            <form method="post" class="absolute top-3 right-3">
                              <input type="hidden" name="mark_seen" value="<?= $notif->id; ?>">
                              <button type="submit" title="Mark as read" class="text-blue-600 hover:underline text-xs">✓</button>
                            </form>
                          <?php endif; ?>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <li class="p-3 text-sm text-center text-gray-500">No notifications available</li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
        <?php endif; ?>

        <!-- User Menu if logged in -->
        <?php if ($is_logged_in): ?>
          <div class="relative">
            <button id="userBtn" class="flex items-center gap-2 focus:outline-none">
              <img src="<?= esc_url($avatar_url); ?>" alt="User Avatar" class="w-10 h-10 rounded-full border-2 border-gray-300" />
            </button>
            <div id="userMenu" class="hidden absolute sm:right-0  sm:translate-x-0 -translate-x-1/2 w-56 bg-white border rounded shadow-md z-20 p-4">
              <?php
              wp_nav_menu(array(
                'theme_location' => $is_masjid ? 'masjid_dashboard_menu' : 'user_dashboard_menu',
                'container'      => false,
                'menu_class'     => 'flex flex-col gap-2 font-medium',
                'fallback_cb'    => false,
              ));
              ?>
                <a 
                  href="<?php echo esc_url(wp_logout_url(home_url())); ?>" 
                  class="block mt-3  text-red-600 font-bold hover:underline"
                >
                  Logout
                </a>
            </div>
          </div>
        <?php else: ?>
          <!-- Login / Register Buttons -->
          <div class="flex items-center gap-2">

            <?php
            $login_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'template-login.php']);
            $register_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'template-register.php']);
            ?>
            
            <a href="<?= esc_url(get_permalink($login_page[0]->ID ?? 0)); ?>" class="bg-green-100 text-green-800 px-4 py-2 rounded hover:bg-green-200">Login</a>
            <a href="<?= esc_url(get_permalink($register_page[0]->ID ?? 0)); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Register</a>
            
          </div>
        <?php endif; ?>
      </div>

      <!-- Mobile Toggle -->
      <button id="navbarToggle" class="md:hidden block focus:outline-none z-50">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>
    <!-- Mobile Sidebar Navbar -->
<ul
  id="navbarList"
  class="navbar-list flex sm:gap-6 gap-4 md:flex-row flex-col md:static fixed top-0 right-[-100%] md:w-fit h-full w-2/3 bg-white z-[100] md:p-0 py-8 px-4 transition-all duration-300"
>
  <!-- Header for mobile menu -->
  <div class="md:hidden flex items-center justify-between pb-4 border-b">
    <div class="img">
      <a href="<?php echo esc_url(home_url('/')); ?>">
        <img
          class="md:w-[120px] w-[100px]"
          src="<?= esc_url(get_field("logo", "option")); ?>"
          alt="Logo"
        />
      </a>
    </div>

    <button
      id="closeSidebar"
      class="hover:text-red-500 transition md:hidden"
    >
      <svg
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>
  </div>

  <!-- WordPress Menu Items -->
  <?php
  wp_nav_menu(array(
    'theme_location' => 'primary-menu',
    'container'      => false,
    'items_wrap'     => '%3$s', // Remove <ul> wrapper
    'walker'         => new Walker_Nav_Menu(), // Default walker
    'menu_class'     => '', // Prevent adding unwanted classes
  ));
  ?>
</ul>

  <div id="overlay-navbar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-30 md:hidden"></div>
</header>

<!-- Overlay -->
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 md:hidden"></div>

<div class="page-wrapper">



<script>
  document.addEventListener("DOMContentLoaded", function () {
    const notifBtn = document.getElementById("notifBtn");
    const notifMenu = document.getElementById("notifMenu");

    if (notifBtn && notifMenu) {
      notifBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        notifMenu.classList.toggle("hidden");
      });

      document.addEventListener("click", function () {
        notifMenu.classList.add("hidden");
      });

      notifMenu.addEventListener("click", function (e) {
        e.stopPropagation(); // لا تغلق عند الضغط داخل القائمة
      });
    }
    // const notifBtn = document.getElementById("notifBtn");
// const notifMenu = document.getElementById("notifMenu");
const userBtn = document.getElementById("userBtn");
const userMenu = document.getElementById("userMenu");

// Toggle menus
notifBtn?.addEventListener("click", (e) => {
  e?.stopPropagation();
  notifMenu?.classList.toggle("hidden");
  userMenu?.classList.add("hidden"); // Close other menu
});


  });
</script>
