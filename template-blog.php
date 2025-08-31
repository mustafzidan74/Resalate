<?php
/*
  ** Template Name: Blog
  *
*/

get_header(); ?>

<!-- Start breadcrumb -->
<div class="aximo-breadcrumb">
<div class="container">
  <h1 class="post__title"><?php the_title(); ?></h1>
  <nav class="breadcrumbs">
    <ul>
      <li><a href="<?= home_url("/"); ?>"><?php _e("Home", "resalate") ?></a></li>
      <li aria-current="page"> <?php the_title(); ?></li>
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
        $args = array(
          'post_type' => 'post', // Specify the post type
          'paged' => get_query_var('paged') ? get_query_var('paged') : 1 // Handle pagination
        );
        $custom_query = new WP_Query($args);

        if ($custom_query->have_posts()) : 
          $delay = 0.1; // Initial animation delay
          while ($custom_query->have_posts()) : $custom_query->the_post(); 
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
          <p>No posts found.</p>
        <?php endif; ?>
      </div>

      <!-- navigation -->
      <div class="aximo-navigation">
        <nav class="navigation pagination" aria-label="Posts">
          <div class="nav-links">
            <?php
            echo paginate_links(array(
              'total' => $custom_query->max_num_pages,
              'current' => max(1, get_query_var('paged')),
              'prev_text' => '<img style="transform: rotate(180deg);" src="' . get_template_directory_uri() . '/assets/images/icon/arrow-right8.svg" alt="Previous">',
              'next_text' => '<img src="' . get_template_directory_uri() . '/assets/images/icon/arrow-right8.svg" alt="Next">',
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