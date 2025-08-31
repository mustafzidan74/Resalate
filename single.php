<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Resalate
 */

?>

<?php get_header(); ?>

<?php
$post_id = get_the_ID();
$post_type = get_post_type($post_id);
$author_id = get_post_field('post_author', get_the_ID());
$masjid_photo = get_field('masjid_photo', 'user_' . $author_id);
$date = get_the_date('d/m/Y');
$iframe_url = get_field('iframe_url');
$thumbnail = get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/800x600';
$title = get_the_title();
$content = get_the_content();
$share_url = urlencode(get_permalink());
$share_title = urlencode($title);

// التبرعات
$total = get_field('total_amount', $post_id);
$paid = get_field('amount_paid', $post_id);
$currency = get_field('currency', $post_id);
$percentage = ($total > 0 && $paid >= 0) ? min(100, round(($paid / $total) * 100)) : 0;
?>

<main class="my-20">
  <div class="container mx-auto px-4">
    <div class="post-details grid md:grid-cols-2 grid-cols-1 gap-6 mb-20">
        <?php
        $category_name = $post_type === 'post'
          ? (get_the_category() ? get_the_category()[0]->name : 'Uncategorized')
          : $post_type;
        ?>
        <div class="card-box rounded-lg overflow-hidden" data-category-name="<?= esc_attr($category_name) ?>">
        <div class="img-box">
          <?php if ($post_type === 'live-feed' && $iframe_url): ?>
            <iframe src="<?= esc_url($iframe_url) ?>" class="w-full h-[300px] md:h-[400px]" frameborder="0" allowfullscreen></iframe>
          <?php else: ?>
            <img src="<?= esc_url($thumbnail) ?>" class="w-full" alt="<?= esc_attr($title) ?>" />
          <?php endif; ?>
        </div>

        <div class="text-box px-4 py-5 bg-white flex flex-col">
          <div class="account-post-info mt-6 flex justify-between items-center flex-wrap gap-3">
              <?php if ($post_type !== 'post'): ?>
            <div class="flex items-center gap-3">
              <div class="img">
                <img src="<?= esc_url($masjid_photo['url'] ?? 'https://placehold.co/400x400') ?>" class="w-[50px] h-[50px] object-cover rounded-full" alt="" />
              </div>
              <div class="text-box">
                <?php
                $author_id = get_post_field('post_author', get_the_ID());
                $masjid_name = get_the_author_meta('display_name', $author_id);
                ?>
                <p class="masjid-name font-[600] sm:text-[.9rem] text-sm mb-1">
                  <?= esc_html($masjid_name) ?>
                </p>
                <p class="realesed-date sm:text-[.8rem] text-[.7rem]"><?= $date ?></p>
              </div>
            </div>
            <?php endif; ?>

            <!-- Social Share -->
            <div class="social-share flex items-center gap-2">
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>" target="_blank" class="text-blue-600 text-xl">
                <i class="fab fa-facebook"></i>
              </a>
              <a href="https://twitter.com/intent/tweet?url=<?= $share_url ?>&text=<?= $share_title ?>" target="_blank" class="text-blue-400 text-xl">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $share_url ?>&title=<?= $share_title ?>" target="_blank" class="text-blue-700 text-xl">
                <i class="fab fa-linkedin"></i>
              </a>
            </div>
          </div>
          <?php if ($post_type === 'donations'): ?>
            <div class="donations-progress-bar mt-12">
              <h3 class="font-[600] mb-3 sm:text-[1.5rem] text-[1rem]">Donations</h3>
              <div class="parent">
                <span class="progress-bar" data-progress="<?= $percentage ?>%" style="--progress: <?= $percentage ?>%"></span>
              </div>
              <p class="mt-2 text-sm font-semibold">
                <?= esc_html($paid) ?> / <?= esc_html($total) ?> <?= esc_html($currency) ?>
              </p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="content-box">
        <div class="box">
          <h3 class="font-[600] mb-3 sm:text-[2rem] text-[1.5rem]"><?= esc_html($title) ?></h3>
          <div class="flex flex-col gap-4 leading-[1.7] text-gray-800">
            <?= $content ?>
          </div>
        </div>
        
         <?php if ($post_type === 'donations'): ?>
         
         <?php
// اقرأ مجموعة الدفع للمسجد الحالي
$payments = get_field('payment_methods', 'user_' . $author_id) ?: [];

// helper صغير لاستخراج رابط الصورة سواء كان ID أو Array
$qr_url = '';
if (!empty($payments['switch']['qr_code'])) {
    if (is_numeric($payments['switch']['qr_code'])) {
        $qr_url = wp_get_attachment_image_url((int)$payments['switch']['qr_code'], 'medium');
    } elseif (is_array($payments['switch']['qr_code']) && !empty($payments['switch']['qr_code']['url'])) {
        $qr_url = $payments['switch']['qr_code']['url'];
    }
}

$has_payment =
    !empty($payments['paypal_user']) ||
    !empty($payments['switch']['number']) ||
    !empty($payments['switch']['url']) ||
    !empty($qr_url) ||
    !empty($payments['bank_account']['name']) ||
    !empty($payments['bank_account']['account_number']) ||
    !empty($payments['bank_account']['iban']) ||
    !empty($payments['bank_account']['swift_code']);
?>

<?php if ($has_payment): ?>
  <div class="bg-white p-6 rounded-lg shadow-md mt-6">
    <h3 class="text-2xl font-bold mb-4 flex items-center gap-2">
      <i class="fa-solid fa-money-check-dollar text-green-700"></i>
      Payment Information
    </h3>

    <?php if (!empty($payments['paypal_user'])): ?>
      <div class="mb-4 flex items-center gap-2">
        <i class="fa-brands fa-paypal text-blue-700"></i>
        <div>
          <h4 class="font-semibold">PayPal</h4>
          <p class="text-gray-700"><?php echo esc_html($payments['paypal_user']); ?></p>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($payments['switch']['number']) || !empty($payments['switch']['url']) || $qr_url): ?>
      <div class="mb-4 flex items-start gap-2">
        <i class="fa-solid fa-credit-card text-indigo-700 mt-1"></i>
        <div>
          <h4 class="font-semibold">Swish</h4>
          <?php if (!empty($payments['switch']['number'])): ?>
            <p class="text-gray-700">Number: <?php echo esc_html($payments['switch']['number']); ?></p>
          <?php endif; ?>
          <?php if (!empty($payments['switch']['url'])): ?>
            <p class="text-gray-700">
              URL:
              <a href="<?php echo esc_url($payments['switch']['url']); ?>" class="text-blue-500 underline" target="_blank" rel="noopener">
                Open Link
              </a>
            </p>
          <?php endif; ?>
          <?php if ($qr_url): ?>
            <img src="<?php echo esc_url($qr_url); ?>" alt="Swish QR" class="w-20 mt-2 rounded shadow">
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php
      $bank = $payments['bank_account'] ?? [];
      $has_bank = !empty($bank['name']) || !empty($bank['account_number']) || !empty($bank['iban']) || !empty($bank['swift_code']);
    ?>
    <?php if ($has_bank): ?>
      <div class="mb-2 flex items-start gap-2">
        <i class="fa-solid fa-building-columns text-gray-700 mt-1"></i>
        <div>
          <h4 class="font-semibold">Bank Account</h4>
          <?php if (!empty($bank['name'])): ?>
            <p class="text-gray-700">Account Name: <?php echo esc_html($bank['name']); ?></p>
          <?php endif; ?>
          <?php if (!empty($bank['account_number'])): ?>
            <p class="text-gray-700">Account Number: <?php echo esc_html($bank['account_number']); ?></p>
          <?php endif; ?>
          <?php if (!empty($bank['iban'])): ?>
            <p class="text-gray-700">IBAN: <?php echo esc_html($bank['iban']); ?></p>
          <?php endif; ?>
          <?php if (!empty($bank['swift_code'])): ?>
            <p class="text-gray-700">SWIFT Code: <?php echo esc_html($bank['swift_code']); ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

         
        <?php endif; ?>
      </div>
    </div>

    <!-- Related Posts -->
    <?php
    $related_query = new WP_Query([
      'post_type' => $post_type,
      'posts_per_page' => 4,
      'post__not_in' => [$post_id],
      'orderby' => 'date',
      'order' => 'DESC',
    ]);
    ?>

    <?php if ($related_query->have_posts()): ?>
      <div class="realted-posts lessons">
        <div class="head-content flex justify-between items-center flex-wrap gap-4 mb-4">
          <h2 class="sm:text-xl text-lg font-[900]">Related Posts</h2>
        </div>

        <div class="content-wrapper grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
          <?php while ($related_query->have_posts()): $related_query->the_post();
            $r_thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://placehold.co/400x300';
            $r_author_id = get_the_author_meta('ID');
            $r_photo = get_field('masjid_photo', 'user_' . $r_author_id);
            $r_date = get_the_date('d/m/Y');
          ?>
            <div class="card-box rounded-lg overflow-hidden shadow-md">
              <div class="img-box">
                <img src="<?= esc_url($r_thumb) ?>" alt="<?= esc_attr(get_the_title()) ?>" />
              </div>
              <div class="text-box px-4 py-5 bg-white flex flex-col">
                <h3 class="title text-lg mb-2 font-[600]"><?= esc_html(get_the_title()) ?></h3>
                <p class="desc"><?= wp_trim_words(strip_tags(get_the_content()), 15) ?></p>
                <?php if ($post_type !== 'post'): ?>
                <div class="account-post-info mt-6 flex items-center gap-3">
                  <div class="img">
                    <img src="<?= esc_url($r_photo['url'] ?? 'https://placehold.co/400x400') ?>" class="w-[50px] h-[50px] object-cover rounded-full" alt="" />
                  </div>
                  <div class="text-box">
                    <a href="<?= get_author_posts_url($r_author_id) ?>" class="masjid-name font-[600] sm:text-[.9rem] text-sm mb-1 hover:underline">
                      <?= get_the_author() ?>
                    </a>
                    <p class="realesed-date sm:text-[.8rem] text-[.7rem]"><?= $r_date ?></p>
                  </div>
                </div>
                <?php endif; ?>
                <a class="self-end mt-5 text-sm font-bold" href="<?= get_permalink() ?>">Read More</a>
              </div>
            </div>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
