<?php
/**
 * Template Name: User Followers
 */

if (!is_user_logged_in()) {
    wp_redirect(site_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
if (!in_array('subscriber', $current_user->roles)) {
    wp_die('Access Denied');
}
?>

<?php get_header(); ?>


      <main class="my-20">
        <div class="container mx-auto px-4 flex items-start gap-6">
          <div class="wrapper lessons flex-1">
            <div
              class="head-content flex justify-between items-center flex-wrap gap-4 mb-4"
            >
              <h2 class="sm:text-xl text-lg font-[900]">Masjids Followers</h2>

              <div class="search-box sm:w-[40%] w-full">
                <form action="">
                  <input
                    name="search-lessons"
                    type="text"
                    class="border w-full py-2 px-5 rounded-lg"
                    placeholder="Search Username"
                  />
                </form>
              </div>
            </div>

            <div class="content-wrapper">
                <div class="p-4 grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-8">
                  <?php
                  $followed_ids = get_user_meta($current_user->ID, 'user_meta_masjids_follow', true);
                  if (!empty($followed_ids) && is_array($followed_ids)) :
                    foreach ($followed_ids as $masjid_id) :
                      $masjid = get_userdata($masjid_id);
                      if (!$masjid || !in_array('masjid', $masjid->roles)) continue;
                
                      $masjid_name = esc_html($masjid->display_name);
                      $masjid_email = esc_html($masjid->user_email);
                        $avatar = get_field('masjid_photo', 'user_' . $masjid_id);
                        $avatar_url = $avatar['url'] ?? get_avatar_url($masjid_id);
                      ?>
                      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white py-6 px-4 rounded-lg shadow-md">
                        <div class="flex items-start gap-4">
                          <img src="<?= esc_url($avatar_url); ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover" />
                          <div>
                            <p class="font-semibold text-lg username"><?= $masjid_name; ?></p>
                            <p class="text-sm user-email text-gray-600"><?= $masjid_email; ?></p>
                          </div>
                        </div>
                
                        <div class="follow-button flex justify-end">
                            <a target="_blank" href="<?php echo esc_url(get_author_posts_url($masjid_id)); ?>" class="primary-btn py-3 px-6 inline-block rounded-lg font-bold transition text-white bg-blue-600 hover:bg-blue-700">
                              View masjid 
                            </a>
                        </div>
                      </div>
                    <?php
                    endforeach;
                  else :
                    echo '<p class="col-span-full text-center text-gray-500">You are not following any masjids yet.</p>';
                  endif;
                  ?>
                </div>
            </div>

          </div>
        </div>
      </main>


<?php get_footer(); ?>
