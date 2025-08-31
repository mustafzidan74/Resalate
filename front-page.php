<?php get_header(); ?>

<div class="page-wrapper home-page">
  <main class="mb-20">

<section class="hero-banner">
  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <?php if (have_rows('slider')): ?>
        <?php while (have_rows('slider')): the_row();
          $background = get_sub_field('background');
          $title = get_sub_field('title');
          $description = get_sub_field('description');
          $primary_btn_text = get_sub_field('button_text_1');
          $primary_btn_link = get_sub_field('button_url_1');
          $secondary_btn_text = get_sub_field('button_text_2');
          $secondary_btn_link = get_sub_field('button_url_2');
        ?>
          <div class="swiper-slide py-12 flex items-center" style="background-image: url(<?php echo esc_url($background['url']); ?>);">
            <div class="container mx-auto px-4">
              <h2 class="font-[700] lg:text-[4rem] md:text-[3rem] text-[2rem] mb-3">
                <?php echo esc_html($title); ?>
              </h2>
              <p class="leading-[1.7] sm:text-[1.4rem] text-[1rem] font-[500] lg:w-[50%] md:w-[70%] w-full">
                <?php echo esc_html($description); ?>
              </p>
              <div class="btn-wrapper flex justify-center gap-5 flex-wrap mt-6">
                <?php if ($primary_btn_link && $primary_btn_text): ?>
                  <a class="primary-btn py-3 px-6 rounded-lg inline-block" href="<?php echo esc_url($primary_btn_link); ?>">
                    <?php echo esc_html($primary_btn_text); ?>
                  </a>
                <?php endif; ?>
                <?php if ($secondary_btn_link && $secondary_btn_text): ?>
                  <a class="success-btn py-3 px-6 rounded-lg inline-block" href="<?php echo esc_url($secondary_btn_link); ?>">
                    <?php echo esc_html($secondary_btn_text); ?>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

    <!-- Navigation arrows -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </div>
</section>

    <!-- Start Quranic verse -->
    <?php if (have_rows('tolerance')): ?>
      <div class="marquee-container" dir="rtl">
        <div class="marquee-track">
          <?php
          while (have_rows('tolerance')): the_row();
            $title = get_sub_field('title');
            ?>
            <div class="marquee-text sm:text-[28px] text-[20px] font-[600]">
              <?php echo esc_html($title); ?> <span>•</span>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- End Quranic verse -->
    
    <?php 
    $partner_image = get_field('partner_image');
    $partner_title = get_field('partner_title');
    $partner_description = get_field('partner_description');
    $partner_button_title = get_field('partner_button_title');
    $partner_button_url = get_field('partner_button_url');
    ?>
    <?php if ($partner_image || $partner_title || $partner_description || $partner_button_title || $partner_button_url): ?>
    <!-- Start Partner Section -->
    <section class="about-section my-20">
      <div class="container mx-auto px-4">
        <div class="content-wrapper grid md:grid-cols-2 grid-cols-1 items-center gap-8">
          
          <?php if ($partner_image): ?>
            <div class="img-box md:order-1 order-2">
              <img src="<?php echo esc_url($partner_image); ?>" class="w-full rounded-lg" alt="<?php echo esc_attr($partner_title); ?>" />
            </div>
          <?php endif; ?>
    
          <div class="text-box md:order-2 order-1">
            <?php if ($partner_title): ?>
              <h3 class="font-[700] lg:text-[2.5rem] text-[2rem]">
                <?php echo esc_html($partner_title); ?>
              </h3>
            <?php endif; ?>
    
            <div class="content-box flex flex-col gap-3 my-4">
              <?php if ($partner_description): ?>
                <p class="sm:text-[1.2rem] text-[1rem] leading-[1.7]">
                  <?php echo wp_kses_post($partner_description); ?>
                </p>
              <?php endif; ?>
    
              <!-- Optional: Advantage List could be inserted from a repeater if needed -->
            </div>
    
            <?php if ($partner_button_title && $partner_button_url): ?>
              <a class="success-btn py-3 px-6 rounded-lg inline-block mt-4" href="<?php echo esc_url($partner_button_url); ?>" target="_blank">
                <?php echo esc_html($partner_button_title); ?>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
    <!-- End Partner Section -->
    <?php endif; ?>
    
    
    <?php
    // جلب آخر 6 مستخدمين من نوع masjid
    $latest_masjids = get_users([
        'role'    => 'masjid',
        'number'  => 6,
        'orderby' => 'registered',
        'order'   => 'DESC',
    ]);
    ?>
    
    <?php if (!empty($latest_masjids)): ?>
    
    <section class="latest-masjids my-20">
      <div class="container mx-auto px-4">
        <div class="head-content text-center mb-12">
          <h2 class="text-2xl sm:text-3xl font-[800]">Latest Registered Masjids</h2>
        </div>
    
        <div class="swiper latestMasjidsSwiper">
          <div class="swiper-wrapper">
            <?php foreach ($latest_masjids as $masjid): ?>
              <?php
                $user_id = $masjid->ID;
                $name = $masjid->display_name;
                $email = $masjid->user_email;
                $photo = get_field('masjid_photo', 'user_' . $user_id);
                $cover = get_field('masjid_cover', 'user_' . $user_id);
                $description = get_field('masjid_description', 'user_' . $user_id);
                $phone = get_field('phone', 'user_' . $user_id);
                $languages = get_field('languages', 'user_' . $user_id) ?: [];
                $profile_url = get_author_posts_url($user_id);
              ?>
              <div class="swiper-slide">
                <div class="card-box rounded-lg overflow-hidden shadow-md bg-white">
                  <div class="img-box">
                    <img 
                      src="<?= esc_url($cover['url'] ?? 'https://placehold.co/600x400?text=Mosque') ?>" 
                      alt="<?= esc_attr($name) ?>" 
                      class="w-full h-[200px] object-cover"
                    />
                  </div>
                  <div class="text-box px-4 py-5 flex flex-col">
                    <h3 class="title text-lg mb-2 font-[600]"><?= esc_html($name) ?></h3>
    
                    <?php if (!empty($description)): ?>
                      <p class="desc line-clamp-3"><?= wp_trim_words(wp_kses_post($description), 25, '...') ?></p>
                    <?php endif; ?>
    
                    <div class="flex flex-col gap-2 my-4 text-sm">
                      <?php if ($email): ?>
                        <div class="flex items-center gap-2">
                          <i class="fa-solid fa-envelope"></i> <span><?= esc_html($email) ?></span>
                        </div>
                      <?php endif; ?>
    
                      <?php if ($phone): ?>
                        <div class="flex items-center gap-2">
                          <i class="fa-solid fa-phone"></i> <span><?= esc_html($phone) ?></span>
                        </div>
                      <?php endif; ?>
    
                      <?php if (!empty($languages)): ?>
                        <div class="flex items-center gap-2">
                          <i class="fa-solid fa-language"></i>
                          <span><?= implode(', ', array_column($languages, 'title')) ?></span>
                        </div>
                      <?php endif; ?>
                    </div>
    
                    <a href="<?= esc_url($profile_url) ?>" class="self-start mt-2 text-sm font-bold text-blue-600 hover:underline">
                      Show Profile
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
    
          <!-- نقاط التنقل -->
          <div class="swiper-pagination"></div>
    
          <!-- الأسهم (سنخفيها في الموبايل بالـ CSS) -->
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
        </div>
    
        <!-- زر عرض كل المساجد -->
        <div class="text-center mt-10">
          <a href="<?= esc_url(site_url('/masjids')) ?>" class="primary-btn inline-block px-6 py-3 rounded-lg font-semibold">
            View All Masjids
          </a>
        </div>
      </div>
    </section>

    <?php endif; ?>

    
    <?php
    $sponsor_image = get_field("sponsor_image_order");
    ?>
    <!-- Start Sponsor Order -->
    <section class="contact-section">
      <div class="container mx-auto px-4">
        <div class="content-wrapper grid md:grid-cols-2 grid-cols-1 gap-8">
    
          <!-- Sponsor Image -->
          <div class="map md:order-1 order-2">
            <?php if ($sponsor_image): ?>
              <img src="<?= esc_url($sponsor_image); ?>" alt="Sponsor Image" class="rounded-lg w-full" />
            <?php endif; ?>
          </div>
    
          <!-- Sponsor Form -->
          <div class="contact-form md:order-2 order-1">
            <form id="sponsor-request-form" action="#">
              <div class="wrapper flex flex-col gap-6">
    
                <div class="input-container">
                  <label class="font-[500] text-[.9rem] inline-block mb-2" for="name">Your Name</label>
                  <input
                    class="py-3 px-3 w-[99%] rounded-lg text-[.8rem]"
                    name="name"
                    id="name"
                    type="text"
                    placeholder="Name"
                  />
                </div>
    
                <div class="input-container">
                  <label class="font-[500] text-[.9rem] inline-block mb-2" for="email">Email</label>
                  <input
                    class="py-3 px-3 w-[99%] rounded-lg text-[.8rem]"
                    name="email"
                    id="email"
                    type="email"
                    placeholder="Email"
                  />
                </div>
    
                <div class="input-container">
                  <label class="font-[500] text-[.9rem] inline-block mb-2" for="phone">Phone Number</label>
                  <input
                    class="py-3 px-3 w-[99%] rounded-lg text-[.8rem]"
                    name="phone"
                    id="phone"
                    type="text"
                    placeholder="Phone"
                  />
                </div>
    
                <div class="input-container">
                  <label class="font-[500] text-[.9rem] inline-block mb-2" for="message">Message</label>
                  <textarea
                    class="py-3 px-3 w-[99%] rounded-lg text-[.8rem] h-[150px] resize-none"
                    name="message"
                    id="message"
                    placeholder="Write your message here..."
                  ></textarea>
                </div>
    
                <div class="flex justify-end">
                  <button
                    class="success-btn py-3 px-6 rounded-lg"
                    id="submit-sponsor-request"
                    type="submit"
                  >
                    Send Message
                  </button>
                </div>
    
                <div id="form-message"></div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
    <!-- End Sponsor Order -->
    
    
<?php
// جلب آخر 6 بوستات من نوع donations
$recent_donations = new WP_Query([
  'post_type'      => 'donations',
  'posts_per_page' => 6,
  'post_status'    => 'publish',
  'orderby'        => 'date',
  'order'          => 'DESC',
]);
?>

<?php if ($recent_donations->have_posts()): ?>
<section class="latest-donations my-20">
  <div class="container mx-auto px-4">
    <div class="head-content text-center mb-12">
      <h2 class="text-2xl sm:text-3xl font-[800]">Latest Donations</h2>
    </div>

    <!-- سلايدر Swiper -->
    <div class="swiper latestDonationsSwiper">
      <div class="swiper-wrapper">
        <?php while ($recent_donations->have_posts()): $recent_donations->the_post(); ?>
          <?php
            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://placehold.co/400x300';
            $total = get_field('total_amount');
            $paid = get_field('amount_paid');
            $currency = get_field('currency');
            $percentage = ($total > 0 && $paid >= 0) ? min(100, round(($paid / $total) * 100)) : 0;
          ?>
          <div class="swiper-slide">
            <div class="card-box rounded-lg overflow-hidden shadow-md bg-white">
              <div class="img-box">
                <img src="<?= esc_url($thumb) ?>" alt="<?= esc_attr(get_the_title()) ?>" class="w-full h-[180px] object-cover">
              </div>
              <div class="text-box px-4 py-5 flex flex-col">
                <h3 class="title text-lg mb-2 font-[600]"><?= esc_html(get_the_title()) ?></h3>
                <p class="desc"><?= esc_html(wp_trim_words(strip_tags(get_the_content()), 20)) ?></p>

                <div class="mt-4 text-sm text-gray-700 space-y-1">
                  <?php if ($total): ?>
                    <p><strong>Total:</strong> <?= esc_html($total) ?> <?= esc_html($currency ?: '') ?></p>
                  <?php endif; ?>
                  <?php if ($paid): ?>
                    <p><strong>Paid:</strong> <?= esc_html($paid) ?> <?= esc_html($currency ?: '') ?></p>
                  <?php endif; ?>
                </div>

                <div class="donations-progress-bar mt-6">
                  <h3 class="font-[600] mb-2 text-sm">Donations</h3>
                  <div class="parent">
                    <span
                      class="progress-bar"
                      data-progress="<?= esc_attr($percentage) ?>%"
                      style="--progress: <?= esc_attr($percentage) ?>%"
                    ></span>
                  </div>
                </div>

                <div class="donation-btn mt-6">
                  <a href="<?= esc_url(get_permalink()) ?>" class="primary-btn py-3 w-full rounded-lg font-[600] text-center block bg-green-600 text-white hover:bg-green-700 transition">
                    Donate Now
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>

      <!-- النقاط -->
      <div class="swiper-pagination"></div>

      <!-- الأسهم (سنخفيها بالموبايل) -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>

    <!-- زر عرض كل التبرعات -->
    <div class="text-center mt-10">
      <a href="<?= esc_url(site_url('/donations')) ?>" class="primary-btn inline-block px-6 py-3 rounded-lg font-semibold">
        View All Donations
      </a>
    </div>
  </div>
</section>
<?php endif; ?>
    
    
    <?php 
    $sponsor_title = get_field('sponsor_title');
    $sponsor_gallery = get_field('sponsor_gallery');
    ?>
    <?php if ($sponsor_title || $sponsor_gallery): ?>
    <!-- Start Sponsor Logos -->
    <section class="partner-section my-20">
      <div class="container mx-auto px-4">
    
        <?php if ($sponsor_title): ?>
          <div class="head-content text-center mb-12">
            <p class="font-[600] sm:text-[1.5rem] text-[1.2rem]">
              <?php echo esc_html($sponsor_title); ?>
            </p>
          </div>
        <?php endif; ?>
    
        <?php if ($sponsor_gallery): ?>
          <div class="content-wrapper overflow-hidden">
            <div class="marquee-track flex items-center gap-12 animate-scroll-x">
              <?php foreach ($sponsor_gallery as $image): ?>
                <img
                  src="<?php echo esc_url($image['url']); ?>"
                  alt="<?php echo esc_attr($image['alt']); ?>"
                  class="w-[100px] object-cover aspect-image"
                />
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
    
      </div>
    </section>
    <!-- End Sponsor Logos -->
    <?php endif; ?>
    
    <?php 
    $counter_title = get_field('counter_title');
    $counter_data = get_field('counter_data');
    ?>
    <?php if ($counter_title || $counter_data): ?>
    <!-- Start Counters Section -->
    <section class="indicators-section my-20">
      <div class="container mx-auto px-4">
    
        <?php if ($counter_title): ?>
          <div class="head-content text-center mb-12">
            <p class="font-[600] sm:text-[1.5rem] text-[1.2rem]">
              <?php echo esc_html($counter_title); ?>
            </p>
          </div>
        <?php endif; ?>
    
        <?php if ($counter_data): ?>
          <div class="content-wrapper grid lg:grid-cols-4 sm:grid-cols-2 grid-cols-1 gap-6">
            <?php foreach ($counter_data as $data): ?>
              <div class="box text-center">
                <h4 class="font-[700] lg:text-[3rem] text-[2rem] mb-2" data-num="<?php echo esc_attr($data['number']); ?>">
                  <span>0</span>+
                </h4>
                <p class="sm:text-[1.2rem] text-[1rem] leading-[1.7]">
                  <?php echo esc_html($data['title']); ?>
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
    
      </div>
    </section>
    <!-- End Counters Section -->
    <?php endif; ?>
  </main>

</div>



<?php get_footer(); ?>

<script>
    let homeHeroSlider = new Swiper(".mySwiper", {
  speed: 1000,
  loop: true,

  autoplay: {
    delay: 3000, // 3 seconds
    disableOnInteraction: false, // keeps autoplay running after user interaction
  },

  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
});

document.addEventListener("DOMContentLoaded", () => {
  const indicatorsSection = document.querySelector(".indicators-section");
  const counters = indicatorsSection.querySelectorAll("h4 span");
  let started = false;

  const animateValue = (el, end, duration = 2000) => {
    let start = 0;
    let startTime = null;

    const step = (timestamp) => {
      if (!startTime) startTime = timestamp;
      const progress = timestamp - startTime;
      const current = Math.min(Math.floor((progress / duration) * end), end);
      el.textContent = current;
      if (current < end) {
        requestAnimationFrame(step);
      }
    };

    requestAnimationFrame(step);
  };

  const observer = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting && !started) {
        counters.forEach((counter) => {
          const endValue = parseInt(
            counter.parentElement.getAttribute("data-num")
          );
          animateValue(counter, endValue);
        });
        started = true;
      }
    },
    {
      threshold: 0.5,
    }
  );

  observer.observe(indicatorsSection);
});

</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("sponsor-request-form");
  const messageBox = document.getElementById("form-message");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    const data = new URLSearchParams();
    for (const pair of formData) {
      data.append(pair[0], pair[1]);
    }

    messageBox.textContent = "Sending...";
    messageBox.classList.remove("text-green-600", "text-red-600");
    messageBox.classList.add("text-gray-600");

    fetch("<?= admin_url('admin-ajax.php'); ?>", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "action=submit_sponsor_request&" + data.toString()
    })
    .then((response) => response.json())
    .then((res) => {
      if (res.success) {
        messageBox.textContent = res.data;
        messageBox.classList.remove("text-gray-600", "text-red-600");
        messageBox.classList.add("text-green-600");
        form.reset();
      } else {
        messageBox.textContent = res.data || "Something went wrong.";
        messageBox.classList.remove("text-gray-600", "text-green-600");
        messageBox.classList.add("text-red-600");
      }
    })
    .catch(() => {
      messageBox.textContent = "Network error.";
      messageBox.classList.remove("text-gray-600", "text-green-600");
      messageBox.classList.add("text-red-600");
    });
  });
});

document.addEventListener('DOMContentLoaded', function () {
  new Swiper('.latestMasjidsSwiper', {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      }
    }
  });
});

document.addEventListener('DOMContentLoaded', function () {
  new Swiper('.latestDonationsSwiper', {
    slidesPerView: 1.2,
    spaceBetween: 20,
    loop: true,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      }
    }
  });
});


</script>
