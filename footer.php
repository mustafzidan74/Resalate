<?php
/**
 * The template for displaying the footer
 *
 * @package Resalate
 */
?>

</div> <!-- Close page wrapper -->

<?php
  // احضار الحقول بأمان من الـ Options
  $logo        = get_field('logo', 'option');
  $apps_links  = get_field('apps_links', 'option');
  $android_url = is_array($apps_links) && !empty($apps_links['android']) ? (string) $apps_links['android'] : '';
  $apple_url   = is_array($apps_links) && !empty($apps_links['apple'])   ? (string) $apps_links['apple']   : '';
?>

<footer class="bg-[#fffef4] pt-12">
  <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
    <!-- Logo & About -->
    <div>
      <div class="mb-4">
        <a href="<?php echo esc_url( home_url('/') ); ?>">
          <?php if (!empty($logo)) : ?>
            <img
              src="<?php echo esc_url($logo); ?>"
              alt="<?php echo esc_attr( get_bloginfo('name') ); ?>"
              class="w-[120px]"
            />
          <?php else: ?>
            <span class="font-bold text-lg"><?php bloginfo('name'); ?></span>
          <?php endif; ?>
        </a>
      </div>
      <p class="text-sm mb-4">
        <?php bloginfo('description'); ?>
      </p>

      <?php if ( have_rows('social_media', 'option') ) : ?>
        <div class="flex gap-3">
          <?php while ( have_rows('social_media', 'option') ) : the_row();
            $icon = get_sub_field('icon');         // بيكون SVG/HTML
            $url  = (string) get_sub_field('url'); // أجبرها سترينج
            $url  = $url ? esc_url($url) : '#';
          ?>
            <a href="<?php echo $url; ?>" class="bg-[#e5f4e2] p-2 rounded-full" target="_blank" rel="noopener">
              <?php echo $icon; ?>
            </a>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- App Download -->
    <div>
      <h4 class="font-bold text-lg mb-4"><?php _e('Download App', 'Resalate'); ?></h4>
      <div class="flex flex-col gap-3">
        <?php if ($android_url): ?>
          <a href="<?php echo esc_url($android_url); ?>">
            <img
              src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/play-store.svg' ); ?>"
              alt="Google Play"
              class="h-12"
            />
          </a>
        <?php endif; ?>
        <?php if ($apple_url): ?>
          <a href="<?php echo esc_url($apple_url); ?>">
            <img
              src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/app-store.svg' ); ?>"
              alt="App Store"
              class="h-12"
            />
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Page Links -->
    <div>
      <h4 class="font-bold text-lg mb-4"><?php _e('Pages', 'Resalate'); ?></h4>
      <ul class="space-y-2 text-sm">
        <?php
        if ( has_nav_menu('footer_menu') ) {
          wp_nav_menu(array(
            'theme_location' => 'footer_menu',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'fallback_cb'    => false,
          ));
        }
        ?>
      </ul>
    </div>

    <!-- Contact Info -->
    <div>
      <h4 class="font-bold text-lg mb-4"><?php _e('Contact Us', 'Resalate'); ?></h4>
      <?php if ( have_rows('contact_information', 'option') ) : ?>
        <ul class="space-y-3 text-sm">
          <?php while ( have_rows('contact_information', 'option') ) : the_row();
            $icon  = get_sub_field('icon');            // HTML/SVG
            $title = (string) get_sub_field('title');  // أجبر سترينج
            $url   = (string) get_sub_field('url');
            $href  = $url ? esc_url($url) : '#';
          ?>
            <li class="flex items-center gap-2">
              <?php echo $icon; ?>
              <a href="<?php echo $href; ?>"><?php echo esc_html($title); ?></a>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="border-t mt-10 py-4 text-center text-sm text-gray-600">
    &copy; Copyright <?php echo esc_html( date('Y') ); ?>, <?php bloginfo('name'); ?>
  </div>
</footer>

<?php wp_footer(); ?>

<script>
window.OneSignal = window.OneSignal || [];
OneSignal.push(function() {
  OneSignal.init({
    appId: "52d93725-02ac-42bb-9b78-2e11761cd7e4",
    notifyButton: { enable: true }
  });

  <?php if ( is_user_logged_in() ) : ?>
  OneSignal.setExternalUserId("<?php echo esc_js( (string) get_current_user_id() ); ?>");
  <?php endif; ?>
});
</script>

</body>
</html>
