<?php
/*
  Template Name: FAQ
*/
get_header(); ?>

<!-- Accordion FAQ -->
<?php if (have_rows('faq')): ?>
<section class="bg-[#f8f6f1] py-16">
  <div class="container mx-auto px-4">
    <h2 class="text-center text-xl sm:text-2xl font-semibold mb-8 text-gray-800"><?php echo get_field("faq_title"); ?></h2>

    <div class="space-y-4 max-w-3xl mx-auto" id="faqAccordion">
      <?php $i = 0; while (have_rows('faq')): the_row(); $i++; ?>
        <?php 
          $title = get_sub_field('title');
          $desc = get_sub_field('description');
        ?>
        <div class="border border-gray-300 rounded-xl overflow-hidden">
          <button type="button"
            class="w-full text-left px-5 py-4 flex items-center justify-between font-medium text-gray-800 hover:bg-gray-100 transition"
            onclick="toggleFaq(<?= $i ?>)">
            <?= esc_html($title) ?>
            <svg id="arrow-<?= $i ?>" class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div id="faq-body-<?= $i ?>" class="max-h-0 overflow-hidden transition-all duration-300 bg-white px-5">
            <div class="py-4 text-gray-700"><?= wp_kses_post($desc) ?></div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<script>
  function toggleFaq(id) {
    const body = document.getElementById(`faq-body-${id}`);
    const arrow = document.getElementById(`arrow-${id}`);
    const isOpen = body.style.maxHeight && body.style.maxHeight !== '0px';

    // Close all open answers
    document.querySelectorAll('[id^="faq-body-"]').forEach(el => el.style.maxHeight = '0px');
    document.querySelectorAll('[id^="arrow-"]').forEach(icon => icon.classList.remove('rotate-180'));

    // Toggle the clicked one
    if (!isOpen) {
      body.style.maxHeight = body.scrollHeight + "px";
      arrow.classList.add('rotate-180');
    }
  }
</script>

<?php get_footer(); ?>
