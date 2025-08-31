<?php
/**
 * Template Name: All Posts
 */

get_header();

// أنواع البوستات المتاحة
$post_types = [
  'donations' => 'Donations',
  'masjid-to-masjid' => 'From Masjid to Masjid',
  'funerals' => 'Funerals',
  'lessons' => 'Lessons',
  'live-feed' => 'Live Feed'
];

// الفلاتر
$selected_masjid = isset($_GET['masjid']) ? intval($_GET['masjid']) : 0;
$selected_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';

$paged = get_query_var('paged') ?: 1;

$args = [
  'post_type' => array_keys($post_types),
  'posts_per_page' => 12,
  'paged' => $paged,
];

if ($selected_type && in_array($selected_type, array_keys($post_types))) {
  $args['post_type'] = $selected_type;
}

if ($selected_masjid) {
  $args['author'] = $selected_masjid;
}

$query = new WP_Query($args);
?>

<main class="my-20">
  <div class="container mx-auto px-4 flex items-start gap-6">
    <div class="wrapper lessons flex-1">
      <!-- الترويسة -->
      <div class="head-content flex justify-between items-center flex-wrap gap-4 mb-4">
        <h2 class="sm:text-xl text-lg font-[900]">All Posts</h2>

        <!-- نموذج الفلترة -->
        <form method="get" class="flex gap-3 w-full sm:w-auto flex-col sm:flex-row items-center">

          <!-- فلتر المساجد -->
          <select name="masjid" class="select2 py-3 px-4 bg-white rounded-lg font-bold w-full sm:w-auto min-w-[180px]">
            <option value="">All Masjids</option>
            <?php
            $masjid_users = get_users(['role' => 'masjid']);
            foreach ($masjid_users as $user) {
              $selected = ($selected_masjid == $user->ID) ? 'selected' : '';
              echo "<option value='{$user->ID}' $selected>{$user->display_name}</option>";
            }
            ?>
          </select>

          <!-- فلتر نوع البوست -->
          <select name="post_type" class="select2 py-3 px-4 bg-white rounded-lg font-bold w-full sm:w-auto min-w-[180px]">
            <option value="">All Post Types</option>
            <?php foreach ($post_types as $key => $label): ?>
              <option value="<?= esc_attr($key) ?>" <?= $selected_type === $key ? 'selected' : '' ?>>
                <?= esc_html($label) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <!-- زر الفلترة -->
          <button type="submit" class="primary-btn bg-blue-600 text-white font-bold px-5 py-3 rounded-lg hover:bg-blue-700 transition">
            Filter
          </button>
        </form>
      </div>

      <!-- الشبكة -->
      <div class="content-wrapper grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
        <?php if ($query->have_posts()): while ($query->have_posts()): $query->the_post();

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
                  <span class="progress-bar" data-progress="<?= esc_attr($percentage) ?>%" style="--progress: <?= esc_attr($percentage) ?>%"></span>
                </div>
              </div>
              <div class="donation-btn mt-6">
                <a href="<?= get_permalink() ?>" class="primary-btn py-3 w-full rounded-lg font-[600] text-center block bg-green-600 text-white hover:bg-green-700 transition">Donate Now</a>
              </div>
            </div>
          </div>

        <?php else: ?>
          <!-- كارد عام -->
          <div class="card-box rounded-lg overflow-hidden shadow-md">
            <div class="img-box">
              <img src="<?= esc_url($thumb) ?>" alt="<?= esc_attr(get_the_title()) ?>" class="w-full h-[180px] object-cover">
            </div>
            <div class="text-box px-4 py-5 bg-white flex flex-col">
              <h3 class="title text-lg mb-2 font-[600]"><?= esc_html(get_the_title()) ?></h3>
              <p class="desc"><?= esc_html($desc) ?></p>
              <div class="account-post-info mt-6 flex items-center gap-3">
                <div class="img">
                  <img src="<?= esc_url($photo['url'] ?? 'https://placehold.co/400x400') ?>" class="w-[50px] h-[50px] object-cover rounded-full" />
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
        <?php endif; endwhile; wp_reset_postdata(); else: ?>
          <p>No posts found.</p>
        <?php endif; ?>
      </div>

      <!-- الترقيم المعدل -->
      <?php
        $pagination = paginate_links([
          'total' => $query->max_num_pages,
          'current' => $paged,
          'mid_size' => 2,
          'prev_text' => __('« Prev'),
          'next_text' => __('Next »'),
          'type' => 'plain',
        ]);

        if ($pagination): ?>
          <div class="nav-links mt-8 text-center">
            <?= $pagination ?>
          </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<!-- تحسين شكل السيلكت -->
<style>
  .select2-container--default .select2-selection--single {
    height: 50px !important;
    padding: 10px 15px !important;
    font-size: 1rem !important;
  }
  .select2-selection__rendered {
    line-height: 1.5 !important;
    font-weight: 600;
  }
</style>

<script>
  jQuery(document).ready(function($) {
    $('.select2').select2({
      width: 'resolve',
      placeholder: "Select",
      allowClear: true
    });
  });
</script>

<?php get_footer(); ?>
