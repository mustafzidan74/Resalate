<?php
/*
  Template Name: About Us
*/
get_header(); ?>


<!-- About Section -->
<?php if (get_field("about_us_title") && get_field("about_us_description")): ?>
<section class="bg-[#f8f6f1] py-16">
  <div class="container mx-auto px-4 text-center">
    <h2 class="text-3xl font-bold mb-4"><?= esc_html(get_field("about_us_title")); ?></h2>
    <p class="max-w-3xl mx-auto text-gray-700"><?= wp_kses_post(get_field("about_us_description")); ?></p>
  </div>

  <!-- Images -->
  <div class="container mx-auto px-4 mt-12 grid md:grid-cols-2 gap-6">
    <div><img src="<?= esc_url(get_field("about_us_image_1")); ?>" alt="About Image" class="rounded-xl shadow-md"></div>
    <div><img src="<?= esc_url(get_field("about_us_image_2")); ?>" alt="About Image" class="rounded-xl shadow-md"></div>
  </div>

    <!-- Vision & Mission -->
    <div class="container mx-auto px-4 mt-16 grid md:grid-cols-2 gap-12">
      <div>
        <h3 class="text-xl font-semibold mb-2 flex items-center gap-2">
          <i class="fas fa-eye text-blue-600"></i>
          <?= esc_html(get_field("vision_title")); ?>
        </h3>
        <p class="text-gray-700"><?= wp_kses_post(get_field("vision_description")); ?></p>
      </div>
      <div>
        <h3 class="text-xl font-semibold mb-2 flex items-center gap-2">
          <i class="fas fa-bullseye text-red-600"></i>
          <?= esc_html(get_field("mission_title")); ?>
        </h3>
        <p class="text-gray-700"><?= wp_kses_post(get_field("mission_description")); ?></p>
      </div>
    </div>
</section>
<?php endif; ?>

<!-- Goals Section -->
<?php if (have_rows('goals_features')): ?>
<section class="py-16 bg-white">
  <div class="container mx-auto px-4 grid md:grid-cols-2 gap-12 items-start">
    <div>
      <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        <i class="fas fa-flag-checkered text-green-600"></i>
        <?= esc_html(get_field("goals_title")); ?>
      </h2>
      <p class="text-gray-700 mb-6"><?= wp_kses_post(get_field("goals_description")); ?></p>
    </div>
    <div class="space-y-4">
      <?php while (have_rows('goals_features')): the_row(); ?>
        <div class="flex items-start gap-4">
          <div class="text-green-600 pt-1"><i class="fas fa-check-circle"></i></div>
          <div>
            <h4 class="font-semibold text-gray-800"><?= esc_html(get_sub_field('title')); ?></h4>
            <p class="text-gray-600"><?= esc_html(get_sub_field('description')); ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Testimonials -->
<?php if (have_rows('testimonials')): ?>
<section class="bg-[#f8f6f1] py-16">
  <div class="container mx-auto px-4 text-center">
    <h2 class="text-2xl font-bold mb-8"><?= esc_html(get_field("testimonials_title")); ?></h2>
    <div class="grid md:grid-cols-2 gap-8">
      <?php while (have_rows('testimonials')): the_row(); ?>
        <div class="bg-white border border-gray-200 rounded-xl p-6 text-left shadow-sm">
          <div class="flex items-center mb-4">
            <div class="text-yellow-400 mr-2">
              <?php for ($i = 0; $i < 5; $i++): ?>
                <i class="fas fa-star"></i>
              <?php endfor; ?>
            </div>
          </div>
          <p class="text-gray-700 mb-4"><?= esc_html(get_sub_field('description')); ?></p>
          <div class="flex items-center gap-4">
            <img src="<?= esc_url(get_sub_field('image')); ?>" alt="Author" class="w-10 h-10 rounded-full">
            <div>
              <p class="font-semibold text-gray-800"><?= esc_html(get_sub_field('name')); ?></p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
