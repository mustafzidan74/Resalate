<?php get_header(); ?>

<?php
$user = get_userdata(get_query_var('author'));
$user_id = $user->ID;

if (!in_array('masjid', (array) $user->roles)) {
    // المستخدم ليس مسجدًا، نعرض اسمه وإيميله وصورته فقط
    $avatar = get_avatar_url($user_id, ['size' => 150]);
    $email = esc_html($user->user_email);
    $display_name = esc_html($user->display_name);

    echo '<div class="container mx-auto py-20 px-4">';
    echo '<div class="bg-white shadow-md rounded-lg p-8 max-w-md mx-auto text-center">';
    echo '<img src="' . esc_url($avatar) . '" alt="' . $display_name . '" class="w-24 h-24 rounded-full mx-auto mb-4">';
    echo '<h2 class="text-2xl font-bold mb-2">' . $display_name . '</h2>';
    echo '<p class="text-gray-600 mb-2">' . $email . '</p>';
    echo '</div></div>';

    get_footer();
    return;
}

// بيانات المسجد
$photo = get_field('masjid_photo', 'user_' . $user_id);
$cover = get_field('masjid_cover', 'user_' . $user_id);
$description = get_field('masjid_description', 'user_' . $user_id);
$phone = get_field('phone', 'user_' . $user_id);
$map_embed = get_field('google_map', 'user_' . $user_id); // iframe
$languages = get_field('languages', 'user_' . $user_id) ?: [];
$services = get_field('services', 'user_' . $user_id) ?: [];
$payments = get_field('payment_methods', 'user_' . $user_id) ?: [];
$masjid_services = get_field('masjid_servieses', 'user_' . $user_id) ?: [];
$masjid_id  = $user_id;

$masjid_follower_count = get_masjid_followers_count($user_id);
$social = get_field('social_media', 'user_' . $user_id) ?: [];


?>

<div class="masjid-details">
  <main class="mb-20">
    <div class="hero-banner cover flex items-center py-20" style="background-image: url('<?= esc_url($cover['url'] ?? 'https://placehold.co/1200x400') ?>'); background-size: cover;">
      <div class="container mx-auto px-4">
        <div class="text-box relative z-20 lg:w-[50%] w-full">
            
          <div class="img-box">
            <img src="<?= esc_url($photo['url'] ?? 'https://placehold.co/400x400') ?>" alt="<?= esc_attr($user->display_name) ?>" class="w-[70px] h-[70px] object-cover rounded-full" />
          </div>
            
          <h1 class="name md:text-[5rem] sm:text-[4rem] text-[3rem] font-bold">
            <?= esc_html($user->display_name) ?>
          </h1>
            <?php
            // خريطة الأيقونات والعناوين
            $icons = [
              'facebook_url'  => ['label' => 'Facebook',  'icon' => 'fa-brands fa-facebook-f'],
              'x_url'         => ['label' => 'X',         'icon' => 'fa-brands fa-x-twitter'], // أو fa-twitter لو نسختك قديمة
              'instagram_url' => ['label' => 'Instagram', 'icon' => 'fa-brands fa-instagram'],
              'youtube_url'   => ['label' => 'YouTube',   'icon' => 'fa-brands fa-youtube'],
              'tiktok_url'    => ['label' => 'TikTok',    'icon' => 'fa-brands fa-tiktok'],
              'linkedin_url'  => ['label' => 'LinkedIn',  'icon' => 'fa-brands fa-linkedin-in'],
              'telegram_url'  => ['label' => 'Telegram',  'icon' => 'fa-brands fa-telegram'],
              'whatsapp_url'  => ['label' => 'WhatsApp',  'icon' => 'fa-brands fa-whatsapp'],
              'snapchat_url'  => ['label' => 'Snapchat',  'icon' => 'fa-brands fa-snapchat'],
            ];
            
            // في حال المجموعة فاضية
            $has_any_social = false;
            foreach ($icons as $key => $_) {
              if (!empty($social[$key])) { $has_any_social = true; break; }
            }
            ?>
            
            <?php if ($has_any_social): ?>
              <div>
                <h4 class="font-semibold mb-2">Social Media</h4>
                <ul class="flex flex-wrap items-center gap-3">
                  <?php foreach ($icons as $key => $meta): ?>
                    <?php if (!empty($social[$key])): 
                      $url = esc_url($social[$key]); ?>
                      <li>
                        <a href="<?php echo $url; ?>"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-full border hover:shadow transition"
                           target="_blank" rel="noopener noreferrer nofollow"
                           title="<?php echo esc_attr($meta['label']); ?>">
                          <i class="<?php echo esc_attr($meta['icon']); ?> text-lg"></i>
                        </a>
                      </li>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
          
          <div class="mt-6">
              <?php if ($description): ?>
                <p class="masjid-desc leading-[1.7] sm:text-[1.1rem] text-[1rem] my-3">
                  <?= wp_trim_words(wp_kses_post($description), 40, '...') ?>
                </p>
              <?php endif; ?>
          </div>
            <?php
            $current_user_id = get_current_user_id();
            $is_subscriber = is_user_logged_in() && in_array('subscriber', wp_get_current_user()->roles);
            $followed_masjids = get_user_meta($current_user_id, 'user_meta_masjids_follow', true);
            $followed_masjids = is_array($followed_masjids) ? $followed_masjids : [];
            
            $is_following = in_array($user_id, $followed_masjids);
            ?>
            
            <?php if ($is_subscriber): ?>
              <div class="follow-btn mt-7">
                <button
                  id="followButton"
                  class="py-3 px-6 inline-block rounded-lg font-bold transition <?= $is_following ? 'bg-red-600 text-white' : 'primary-btn' ?>"
                  data-following="<?= $is_following ? '1' : '0' ?>"
                  data-masjid="<?= esc_attr($user_id) ?>"
                >
                  <div class="flex items-center gap-2">
                    <span class="icon-symbol"><?= $is_following ? '−' : '+' ?></span>
                    <span class="status"><?= $is_following ? 'Unfollow' : 'Follow' ?></span>
                    <p class="number-of-follower">(<?= esc_html($masjid_follower_count) ?>)</p>
                  </div>
                </button>
              </div>
            <?php endif; ?>

          
        </div>
      </div>
    </div>

    <div class="service-box mt-20">
        <div class="container mx-auto px-4 mt-16">
          <div class="grid md:grid-cols-2 gap-8">
        
            <!-- Masjid Information -->
            <div class="bg-white p-6 rounded-lg shadow-md">
              <h3 class="text-2xl font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-mosque text-blue-600"></i> Masjid Information
              </h3>
        
              <?php if ($description): ?>
                <div class="mb-4 flex items-start gap-2">
                  <i class="fa-solid fa-info-circle mt-1 text-gray-500"></i>
                  <div>
                    <h4 class="font-semibold">Masjid Description</h4>
                    <p class="text-gray-700"><?= wp_trim_words(strip_tags($description), 30) ?></p>
                  </div>
                </div>
              <?php endif; ?>
        
              <?php if ($phone): ?>
                <div class="mb-4 flex items-center gap-2">
                  <i class="fa-solid fa-phone text-green-600"></i>
                  <div>
                    <h4 class="font-semibold">Phone Number</h4>
                    <p class="text-gray-700"><?= esc_html($phone) ?></p>
                  </div>
                </div>
              <?php endif; ?>
        
              <?php if ($email = get_userdata(get_the_author_meta('ID'))->user_email): ?>
                <div class="mb-4 flex items-center gap-2">
                  <i class="fa-solid fa-envelope text-yellow-600"></i>
                  <div>
                    <h4 class="font-semibold">Email Address</h4>
                    <p class="text-gray-700"><?= esc_html($email) ?></p>
                  </div>
                </div>
              <?php endif; ?>
        
              <?php if (!empty($languages)): ?>
                <div class="mb-4 flex items-start gap-2">
                  <i class="fa-solid fa-language text-purple-600"></i>
                  <div>
                    <h4 class="font-semibold">Languages</h4>
                    <p class="text-gray-700"><?= esc_html(implode(', ', array_column($languages, 'title'))) ?></p>
                  </div>
                </div>
              <?php endif; ?>
        
              <?php if (!empty($masjid_services)): ?>
                <div class="mb-2 flex items-start gap-2">
                  <i class="fa-solid fa-check-circle text-teal-600 mt-1"></i>
                  <div>
                    <h4 class="font-semibold">Masjid Services</h4>
                    <ul class="list-disc list-inside text-gray-700">
                      <?php foreach ($masjid_services as $service): ?>
                        <li><?= esc_html($service["label"]) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
              <?php endif; ?>
            </div>
        
            <!-- Payment Information -->
            <div class="bg-white p-6 rounded-lg shadow-md">
              <h3 class="text-2xl font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-money-check-dollar text-green-700"></i> Payment Information
              </h3>
            
              <?php if (!empty($payments['paypal_user'])): ?>
                <div class="mb-4 flex items-center gap-2">
                  <i class="fa-brands fa-paypal text-blue-700"></i>
                  <div>
                    <h4 class="font-semibold">PayPal</h4>
                    <p class="text-gray-700"><?= esc_html($payments['paypal_user']) ?></p>
                  </div>
                </div>
              <?php endif; ?>
            
              <?php if (!empty($payments['switch']['number'])): ?>
                <div class="mb-4 flex items-start gap-2">
                  <i class="fa-solid fa-credit-card text-indigo-700"></i>
                  <div>
                    <h4 class="font-semibold">Swish</h4>
                    <p class="text-gray-700">Number: <?= esc_html($payments['switch']['number']) ?></p>
                    <?php if (!empty($payments['switch']['url'])): ?>
                      <p class="text-gray-700">URL: <a href="<?= esc_url($payments['switch']['url']) ?>" class="text-blue-500 underline">Open Link</a></p>
                    <?php endif; ?>
                    <?php if (!empty($payments['switch']['qr_code']['url'])): ?>
                      <img src="<?= esc_url($payments['switch']['qr_code']['url']) ?>" alt="QR Code" class="w-20 mt-2">
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
            
              <?php if (!empty($payments['bank_account'])): ?>
                <div class="mb-4 flex items-start gap-2">
                  <i class="fa-solid fa-building-columns text-gray-700"></i>
                  <div>
                    <h4 class="font-semibold">Bank Account</h4>
                    <p class="text-gray-700">Account Name: <?= esc_html($payments['bank_account']['name']) ?></p>
                    <p class="text-gray-700">Account Number: <?= esc_html($payments['bank_account']['account_number']) ?></p>
                    <p class="text-gray-700">IBAN: <?= esc_html($payments['bank_account']['iban']) ?></p>
                    <p class="text-gray-700">SWIFT Code: <?= esc_html($payments['bank_account']['swift_code']) ?></p>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
    </div>

    <!-- Masjid Events (Grouped by Month) -->
<div class="masjid-events-location py-16">
  <div class="container mx-auto px-4">
    <?php
    $events = get_field('memorization_and_lesson_dates', 'user_' . $user_id);

    if (!empty($events)) {
      // ترتيب الأحداث حسب التاريخ
      usort($events, function ($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
      });

      // تجميع حسب الشهر والسنة
      $grouped = [];
      foreach ($events as $event) {
        $timestamp = strtotime($event['date']);
        $month_key = date('F Y', $timestamp);
        $grouped[$month_key][] = $event;
      }

      // تحديد الشهر الحالي (مثل August 2025)
      $current_month_key = date('F Y');

      // فصل الشهر الحالي عن الباقي
      $first_month_events = $grouped[$current_month_key] ?? [];
      unset($grouped[$current_month_key]);

      // ترتيب باقي الشهور
      uksort($grouped, function ($a, $b) {
        return strtotime("1 $a") - strtotime("1 $b");
      });
    ?>
      <div class="event-table-wrapper">
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
          <i class="fa-solid fa-calendar-days text-green-600"></i> Memorization & Lesson Dates
        </h2>

        <?php if (!empty($first_month_events)): ?>
          <div class="mb-10">
            <h3 class="text-xl font-bold mb-4 border-b border-gray-300 pb-1"><?= esc_html($current_month_key) ?>(Current Month)</h3>
            <div class="overflow-x-auto">
              <table class="w-full text-left bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-800">
                  <tr>
                    <th class="py-3 px-4 w-1/5">Day</th>
                    <th class="py-3 px-4 w-1/5">Date</th>
                    <th class="py-3 px-4">Description</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($first_month_events as $event): 
                    $timestamp = strtotime($event['date']);
                    $day = date('l', $timestamp);
                    $date = date('F j, Y', $timestamp);
                  ?>
                    <tr class="border-t">
                      <td class="py-3 px-4 text-gray-600"><?= esc_html($day) ?></td>
                      <td class="py-3 px-4 text-gray-600"><?= esc_html($date) ?></td>
                      <td class="py-3 px-4 text-gray-700"><?= wp_kses_post($event['description']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif; ?>

        <?php foreach ($grouped as $month => $month_events): ?>
          <div class="mb-10">
            <h3 class="text-xl font-bold mb-4 border-b border-gray-300 pb-1"><?= esc_html($month) ?></h3>
            <div class="overflow-x-auto">
              <table class="w-full text-left bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-800">
                  <tr>
                    <th class="py-3 px-4 w-1/5">Day</th>
                    <th class="py-3 px-4 w-1/5">Date</th>
                    <th class="py-3 px-4">Description</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($month_events as $event): 
                    $timestamp = strtotime($event['date']);
                    $day = date('l', $timestamp);
                    $date = date('F j, Y', $timestamp);
                  ?>
                    <tr class="border-t">
                      <td class="py-3 px-4 text-gray-600"><?= esc_html($day) ?></td>
                      <td class="py-3 px-4 text-gray-600"><?= esc_html($date) ?></td>
                      <td class="py-3 px-4 text-gray-700"><?= wp_kses_post($event['description']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php } ?>
  </div>
</div>


    <!-- Location -->
<?php if (!empty($map_embed)): ?>
  <div class="masjid-location py-12">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold mb-2">Masjid Location</h2>
      <p class="text-gray-600 mb-6">You can view the masjid location on the map below.</p>

      <div class="map rounded-lg overflow-hidden shadow-md">
        <iframe 
          src="<?= esc_url($map_embed) ?>" 
          width="100%" 
          height="450" 
          style="border:0;" 
          allowfullscreen 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade"
          class="w-full h-[450px] rounded-lg">
        </iframe>
      </div>
    </div>
  </div>
<?php endif; ?>
    
    
    
    <?php
    $post_types = [
      'donations' => 'Donations',
      'masjid-to-masjid' => 'From Masjid to Masjid',
      'funerals' => 'Funerals',
      'lessons' => 'Lessons',
      'live-feed' => 'Live Feed'
    ];
    ?>
    
    <!-- Masjid Posts Tabs -->
    <div class="container mx-auto px-4 my-20">
      <div class="tabs-wrapper">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
          <h2 class="text-xl font-bold">All Posts</h2>
          <div class="flex gap-4 flex-wrap">
            <?php $first = true; foreach ($post_types as $key => $label): ?>
              <button 
                class="filter-btn success-btn py-2 px-4 rounded <?= $first ? 'active' : '' ?>" 
                data-tab="<?= esc_attr($key) ?>"
              >
                <?= esc_html($label) ?>
              </button>
              <?php $first = false; ?>
            <?php endforeach; ?>
          </div>
        </div>

        <?php foreach ($post_types as $post_type => $label): ?>
          <?php
          $args = [
            'post_type' => $post_type,
            'author' => $user_id,
            'post_status' => 'publish',
            'posts_per_page' => -1
          ];
          $query = new WP_Query($args);
          ?>
          <div class="tab-content tab-<?= esc_attr($post_type) ?> hidden">
            <h3 class="text-xl font-bold mb-4"><?= esc_html($label) ?> Posts</h3>
            <div class="grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
              <?php if ($query->have_posts()): ?>
                <?php while ($query->have_posts()): $query->the_post(); ?>
                  <?php
                  $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://placehold.co/400x300';
                  $desc = wp_trim_words(strip_tags(get_the_content() ?: get_the_content()), 20);
                  $date = get_the_date('d/m/Y');
                  ?>
                  <?php if ($post_type === 'donations'):
                    $total = get_field('total_amount', get_the_ID());
                    $paid = get_field('amount_paid', get_the_ID());
                    $currency = get_field('currency', get_the_ID());

                    $percentage = 0;
                    if ($total > 0 && $paid >= 0) {
                        $percentage = min(100, round(($paid / $total) * 100));
                    }
                  ?>
                    <div class="card-box rounded-lg overflow-hidden shadow-md">
                      <div class="img-box">
                        <img src="<?= esc_url($thumb) ?>" alt="Image">
                      </div>
                      <div class="text-box px-4 py-5 bg-white flex flex-col">
                        <h3 class="title text-lg mb-2 font-[600]"><?= esc_html(get_the_title()) ?></h3>
                        <p class="desc"><?= esc_html($desc) ?></p>
                        <div class="mt-4 text-sm text-gray-700 space-y-1">
                          <?php if ($total): ?>
                            <p><strong>Total:</strong> <?php echo esc_html($total); ?> <?php echo esc_html($currency ?: ''); ?></p>
                          <?php endif; ?>
                          <?php if ($paid): ?>
                            <p><strong>Paid:</strong> <?php echo esc_html($paid); ?> <?php echo esc_html($currency ?: ''); ?></p>
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
                          <a href="<?= esc_url(get_permalink()) ?>" class="primary-btn py-3 w-full rounded-lg font-[600] text-center block">Donate Now</a>
                        </div>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="card-box rounded-lg overflow-hidden shadow-md" data-tab="<?= esc_attr($post_type) ?>">
                      <div class="img-box">
                        <img src="<?= esc_url($thumb) ?>" alt="Post Image">
                      </div>
                      <div class="text-box px-4 py-5 bg-white flex flex-col">
                        <h3 class="title text-lg mb-2 font-[600]"><?= esc_html(get_the_title()) ?></h3>
                        <p class="desc"><?= esc_html($desc) ?></p>
                        <a href="<?= esc_url(get_permalink()) ?>" class="self-end mt-5 text-sm font-bold">Read More</a>
                      </div>
                    </div>
                  <?php endif; ?>
                <?php endwhile; wp_reset_postdata(); ?>
              <?php else: ?>
                <p class="text-red-500 col-span-full">No posts found.</p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    
    <script>
      const tabs = document.querySelectorAll('[data-tab]');
      const contents = document.querySelectorAll('.tab-content');
    
      tabs.forEach(btn => {
        btn.addEventListener('click', () => {
          // إخفاء كل التابات
          contents.forEach(c => c.classList.add('hidden'));
    
          // إزالة active من كل الأزرار
          tabs.forEach(b => b.classList.remove('active'));
    
          // إظهار التاب المطلوب
          const tab = document.querySelector('.tab-' + btn.dataset.tab);
          if (tab) tab.classList.remove('hidden');
    
          // أضف active لهذا الزر
          btn.classList.add('active');
        });
      });
    
      // إظهار أول تاب تلقائيًا
      const firstTab = document.querySelector('.tab-content');
      if (firstTab) {
        firstTab.classList.remove('hidden');
      }
    </script>
    
    
    
  </main>
</div>

<?php get_footer(); ?>