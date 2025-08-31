<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Resalate
 */
?>

<?php get_header(); ?>

<main class="my-20">
  <div class="container mx-auto px-4 flex items-start gap-6">
    <div class="wrapper lessons flex-1">

      <!-- الترويسة -->
      <div class="head-content flex justify-between items-center flex-wrap gap-4 mb-4">
        <h2 class="sm:text-xl text-lg font-[900]">All Posts</h2>

        <div class="flex gap-3 w-full sm:w-auto flex-col sm:flex-row">
          <!-- الفلترة حسب المسجد -->
          <form method="get" action="" class="w-full">
            <select name="masjid" onchange="this.form.submit()" class="w-full py-3 px-5 bg-white rounded-lg font-bold select2">
              <option value="">All Masjids</option>
              <?php
              $masjid_users = get_users(['role' => 'masjid']);
              foreach ($masjid_users as $user) {
                $selected = (isset($_GET['masjid']) && $_GET['masjid'] == $user->ID) ? 'selected' : '';
                echo "<option value='{$user->ID}' $selected>{$user->display_name}</option>";
              }
              ?>
            </select>
          </form>

          <!-- الترتيب -->
          <select id="orderby" class="w-full py-3 px-5 bg-white rounded-lg font-bold select2">
            <option value="date_desc" <?= ($_GET['orderby'] ?? '') == 'date_desc' ? 'selected' : '' ?>>Newest First</option>
            <option value="date_asc" <?= ($_GET['orderby'] ?? '') == 'date_asc' ? 'selected' : '' ?>>Oldest First</option>
            <option value="title_asc" <?= ($_GET['orderby'] ?? '') == 'title_asc' ? 'selected' : '' ?>>By Name</option>
            <option value="views_desc" <?= ($_GET['orderby'] ?? '') == 'views_desc' ? 'selected' : '' ?>>Most Viewed</option>
          </select>

          <script>
            document.getElementById('orderby').addEventListener('change', function () {
              const url = new URL(window.location.href);
              url.searchParams.set('orderby', this.value);
              window.location.href = url.toString();
            });
          </script>
        </div>
      </div>

      <!-- الشبكة -->
        <div class="content-wrapper grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
          <?php if (have_posts()): while (have_posts()): the_post();
        
            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://placehold.co/400x300';
            $author_id = get_the_author_meta('ID');
            $photo = get_field('masjid_photo', 'user_' . $author_id);
            $date = get_the_date('d/m/Y');
            $desc = wp_trim_words(strip_tags(get_the_content()), 20);
            $views = get_post_meta(get_the_ID(), 'post_views_count', true);
            $post_type = get_post_type();
        
            if ($post_type === 'donations'):
              $total = get_field('total_amount');
              $paid = get_field('amount_paid');
              $currency = get_field('currency');
              $percentage = ($total > 0 && $paid >= 0) ? min(100, round(($paid / $total) * 100)) : 0;
          ?>
            <!-- بطاقة التبرعات -->
            <div class="card-box rounded-lg overflow-hidden shadow-md">
              <div class="img-box">
                <img src="<?= esc_url($thumb) ?>" alt="<?= esc_attr(get_the_title()) ?>" class="w-full h-[180px] object-cover">
              </div>
              <div class="text-box px-4 py-5 bg-white flex flex-col">
                <h3 class="title text-lg mb-2 font-[600]"><?= esc_html(get_the_title()) ?></h3>
                <p class="desc"><?= esc_html($desc) ?></p>
                <div class="mt-4 text-sm text-gray-700 space-y-1">
                  <?php if ($total): ?>
                    <p><strong>Total:</strong> <?= esc_html($total) ?> <?= esc_html($currency ?: '') ?></p>
                  <?php endif; ?>
                  <?php if ($paid): ?>
                    <p><strong>Paid:</strong> <?= esc_html($paid) ?> <?= esc_html($currency ?: '') ?></p>
                  <?php endif; ?>
                </div>
        
                <div class="donations-progress-bar mt-12">
                  <h3 class="font-[600] mb-3 text-sm">Donations</h3>
                    <div class="parent">
                      <span
                        class="progress-bar"
                        data-progress="<?php echo esc_attr($percentage); ?>%"
                        style="--progress: <?php echo esc_attr($percentage); ?>%"
                      ></span>
                    </div>
                </div>
                <div class="donation-btn mt-6">
                  <a href="<?= esc_url(get_permalink()) ?>" class="primary-btn py-3 w-full rounded-lg font-[600] text-center block bg-green-600 text-white hover:bg-green-700 transition">Donate Now</a>
                </div>
              </div>
            </div>
        
          <?php else: ?>
            <!-- الكارد العادي -->
            <div class="card-box rounded-lg overflow-hidden shadow-md">
              <div class="img-box">
                <img src="<?= esc_url($thumb) ?>" alt="<?= esc_attr(get_the_title()) ?>" class="w-full h-[180px] object-cover">
              </div>
        
              <div class="text-box px-4 py-5 bg-white flex flex-col">
                <h3 class="title text-lg mb-2 font-[600]"><?= esc_html(get_the_title()) ?></h3>
                <p class="desc"><?= esc_html($desc) ?></p>
        
                <div class="account-post-info mt-6 flex items-center gap-3">
                  <div class="img">
                    <img src="<?= esc_url($photo['url'] ?? 'https://placehold.co/400x400') ; ?>" class="w-[50px] h-[50px] object-cover rounded-full" />
                  </div>
                  <div class="text-box">
                    <a href="<?= get_author_posts_url($author_id) ?>" class="masjid-name font-[600] sm:text-[.9rem] text-sm mb-1 hover:underline">
                      <?= get_the_author() ?>
                    </a>
                    <p class="realesed-date sm:text-[.8rem] text-[.7rem]"><?= $date ?></p>
                  </div>
                </div>
        
                <a class="self-end mt-5 text-sm font-bold" href="<?= get_permalink() ?>">Read More</a>
              </div>
            </div>
          <?php endif; ?>
          <?php endwhile; else: ?>
            <p>No posts found.</p>
          <?php endif; ?>
        </div>

      <!-- الصفحة -->
      <div class="flex items-center justify-center mt-8">
        <?php the_posts_pagination([
          'mid_size' => 2,
          'prev_text' => __('« Prev'),
          'next_text' => __('Next »'),
          'class' => 'pagination flex gap-1',
        ]); ?>
      </div>
    </div>
  </div>
</main>

<script>
  jQuery(document).ready(function($) {
    $('.select2').select2({
      width: 'resolve',
      placeholder: "Select an option",
      allowClear: true
    });
  });
</script>

<?php get_footer(); ?>
