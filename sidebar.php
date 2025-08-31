<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Resalate
 */

?>
<div class="right-sidebar">
  <!-- Search Widget -->
  <div class="widget">
    <div class="wp-block-search__inside-wrapper" style="display: block;">
      <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="search" name="s" placeholder="<?php _e("Type keyword here", "resalate") ?>" class="wp-block-search__input" value="<?php echo get_search_query(); ?>">
        <button id="wp-block-search__button" type="submit">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon/search.svg" alt="Search">
        </button>
      </form>
    </div>
  </div>

  <!-- Categories Widget -->
  <?php if (get_categories()) : ?>
    <div class="widget">
      <h3 class="wp-block-heading"><?php _e("Categories:", "resalate") ?></h3>
      <ul>
        <?php
        wp_list_categories([
          'title_li' => '',
          'orderby' => 'name',
          'show_count' => true,
          'style' => 'list',
        ]);
        ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Recent Posts Widget -->
  <?php
  $recent_posts = wp_get_recent_posts([
    'numberposts' => 3,
    'post_status' => 'publish',
  ]);
  if (!empty($recent_posts)) : ?>
    <div class="widget aximo_recent_posts_Widget">
      <h3 class="wp-block-heading"><?php _e("Recent Posts:", "resalate") ?></h3>
      <?php foreach ($recent_posts as $post) : ?>
        <div class="post-item">
          <div class="post-thumb">
            <a href="<?php echo get_permalink($post['ID']); ?>">
              <?php echo get_the_post_thumbnail($post['ID'], 'thumbnail'); ?>
            </a>
          </div>
          <div class="post-text">
            <div class="post-date">
              <?php echo get_the_date('F j, Y', $post['ID']); ?>
            </div>
            <a class="post-title" href="<?php echo get_permalink($post['ID']); ?>">
              <?php echo esc_html($post['post_title']); ?>
            </a>
          </div>
        </div>
      <?php endforeach; wp_reset_query(); ?>
    </div>
  <?php endif; ?>

  <!-- Tags Widget -->
  <?php
  $tags = get_tags();
  if (!empty($tags)) : ?>
    <div class="widget">
      <h3 class="wp-block-heading"><?php _e("Tags:", "resalate") ?></h3>
      <div class="wp-block-tag-cloud">
        <?php
        foreach ($tags as $tag) {
          echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a>';
        }
        ?>
      </div>
    </div>
  <?php endif; ?>
</div>
