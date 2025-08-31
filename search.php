<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Resalate
 */

get_header();
?>

<!-- Start breadcrumb -->
<div class="aximo-breadcrumb">
  <div class="container">
    <h1 class="post__title">
      <?php
      /* translators: %s: search query. */
      printf(esc_html__('Search Results for: %s', 'resalate'), '<span>' . get_search_query() . '</span>');
      ?>
    </h1>
    <nav class="breadcrumbs">
      <ul>
        <li><a href="<?= home_url('/'); ?>"><?php _e('Home', 'resalate'); ?></a></li>
        <li aria-current="page"><?= get_search_query() ?></li>
      </ul>
    </nav>
  </div>
</div>
<!-- End breadcrumb -->

<!-- Start Blog -->
<div class="section aximo-section-padding2">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div class="row">
          <?php
          if (have_posts()) :
            $delay = 0.1; // Initial animation delay
            while (have_posts()) : the_post();
              $delay += 0.1; // Increment animation delay
          ?>
            <div class="col-xl-6">
              <div class="single-post-item wow fadeInUpX" data-wow-delay="<?php echo $delay; ?>s">
                <div class="post-thumbnail">
                  <?php if (has_post_thumbnail()) : ?>
                    <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                  <?php else : ?>
                    <img src="<?= get_template_directory_uri(); ?>/assets/images/default.png" alt="Default Image">
                  <?php endif; ?>
                </div>
                <div class="post-content">
                  <div class="post-meta">
                    <div class="post-category">
                      <?php
                      $categories = get_the_category();
                      if (!empty($categories)) {
                        echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
                      }
                      ?>
                    </div>
                    <div class="post-date">
                      <?php echo get_the_date(); ?>
                    </div>
                  </div>
                  <a href="<?php the_permalink(); ?>">
                    <h3 class="entry-title">
                      <?php the_title(); ?>
                    </h3>
                  </a>
                  <a class="post-read-more" href="<?php the_permalink(); ?>">read more <img src="<?= get_template_directory_uri(); ?>/assets/images/icon/arrow-right.svg" alt=""></a>
                </div>
              </div>
            </div>
          <?php
            endwhile;
            wp_reset_postdata(); // Reset the query to prevent conflicts
          else :
          ?>
            <p><?php _e('No search results found.', 'resalate'); ?></p>
          <?php endif; ?>
        </div>

        <!-- navigation -->
        <div class="aximo-navigation">
          <nav class="navigation pagination" aria-label="Posts">
            <div class="nav-links">
              <?php
              global $wp_query; // Access the global query object
              echo paginate_links(array(
                'total' => $wp_query->max_num_pages, // Use the global query's max_num_pages
                'current' => max(1, get_query_var('paged')), // Current page number
                'prev_text' => '<img style="transform: rotate(180deg);" src="' . get_template_directory_uri() . '/assets/images/icon/arrow-right8.svg" alt="Previous">', // Previous button
                'next_text' => '<img src="' . get_template_directory_uri() . '/assets/images/icon/arrow-right8.svg" alt="Next">', // Next button
              ));
              ?>
            </div>
          </nav>
        </div>
      </div>
      <div class="col-lg-4">
        <?php get_sidebar(); ?>
      </div>
    </div>
  </div>
</div>
<!-- End Blog -->

<?php get_footer(); ?>
