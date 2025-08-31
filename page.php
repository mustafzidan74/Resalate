<?php
/**
 * The template for displaying all pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Resalate
 */
?>

<?php get_header(); ?>

<?php
$page_id = get_the_ID();
$title = get_the_title();
$content = get_the_content();
$thumbnail = get_the_post_thumbnail_url($page_id, 'large');
?>

<main class="my-20">
  <div class="container mx-auto px-4">
    <div class="page-details grid md:grid-cols-1 grid-cols-1 gap-6">
        <?php if($thumbnail) : ?>
      <div class="image-box rounded-lg overflow-hidden shadow">
        <img src="<?= esc_url($thumbnail) ?>" class="w-full h-auto object-cover" alt="<?= esc_attr($title) ?>" />
      </div>
      <?php endif; ?>

      <div class="content-box bg-white rounded-lg shadow px-6 py-8">
        <h1 class="text-2xl sm:text-3xl font-bold mb-4"><?= esc_html($title) ?></h1>
        <div class="prose max-w-none text-gray-800 leading-7">
          <?= get_the_content(); ?>
        </div>
      </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>
