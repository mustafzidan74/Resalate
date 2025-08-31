<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Resalate
 */

get_header();
?>

<main class="my-20">
  <div class="container mx-auto px-4 text-center">
    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg px-6 py-10">
      <div class="mb-6">
        <img src="<?= get_template_directory_uri(); ?>/assets/images/about/404.png" alt="404 Not Found" class="mx-auto w-64 h-auto">
      </div>

      <h1 class="text-3xl sm:text-4xl font-extrabold mb-3 text-gray-800">
        Sorry, we couldn’t find the page
      </h1>

      <p class="text-gray-600 text-lg mb-6">
        The page you’re looking for might have been removed or is temporarily unavailable.
      </p>

      <a href="<?= home_url('/'); ?>" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200" style="color: #fff;">
        Return to Homepage
        <img src="<?= get_template_directory_uri(); ?>/assets/images/icon/arrow-right.svg" alt="arrow" class="ml-2 w-5 h-5">
      </a>
    </div>
  </div>
</main>

<?php
get_footer();
?>
