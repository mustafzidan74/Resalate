<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
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

      <div class="head-content flex justify-between items-center flex-wrap gap-4 mb-4">
        <h2 class="sm:text-xl text-lg font-[900]">Latest Posts</h2>

        <!-- فلترة التصنيفات -->
        <form method="get" action="" class="w-full sm:w-auto">
          <select name="cat" onchange="this.form.submit()" class="py-3 px-5 bg-white rounded-lg font-bold select2 w-full sm:w-[200px]">
            <option value="">All Categories</option>
            <?php
              $categories = get_categories();
              $current_cat = isset($_GET['cat']) ? intval($_GET['cat']) : 0;
              foreach ($categories as $cat) {
                $selected = ($current_cat === $cat->term_id) ? 'selected' : '';
                echo "<option value='{$cat->term_id}' $selected>" . esc_html($cat->name) . "</option>";
              }
            ?>
          </select>
        </form>
      </div>

      <!-- الشبكة -->
      <div class="content-wrapper grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-6">
        <?php if (have_posts()): while (have_posts()): the_post(); ?>
          <?php
            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://placehold.co/400x300';
            $author_id = get_post_field('post_author', get_the_ID());
            $photo = get_field('masjid_photo', 'user_' . $author_id);
            $date = get_the_date('d/m/Y');
            $desc = wp_trim_words(strip_tags(get_the_content()), 20);
          ?>
          <div class="card-box rounded-lg overflow-hidden shadow-md">
            <div class="img-box">
              <a href="<?= esc_url(get_permalink()) ?>">
                <img src="<?= esc_url($thumb) ?>" alt="<?= esc_attr(get_the_title()) ?>" class="w-full h-[180px] object-cover" />
              </a>
            </div>
            <div class="text-box px-4 py-5 bg-white flex flex-col">
              <h3 class="title text-lg mb-2 font-[600]">
                <a href="<?= esc_url(get_permalink()) ?>" class="hover:underline">
                  <?= esc_html(get_the_title()) ?>
                </a>
              </h3>
              <p class="desc"><?= esc_html($desc) ?></p>


              <a class="self-end mt-5 text-sm font-bold text-blue-600 hover:underline" href="<?= esc_url(get_permalink()) ?>">Read More</a>
            </div>
          </div>
        <?php endwhile; else: ?>
          <p>No posts found.</p>
        <?php endif; ?>
      </div>

      <!-- الصفحات -->
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

<?php get_footer(); ?>
