<?php
/*
  Template Name: Contact Us
*/
get_header(); ?>

<section class="py-16 bg-[#f8f6f1]">
  <div class="container mx-auto px-4 grid md:grid-cols-2 gap-12">

    <!-- Contact Info Boxes -->
    <div class="space-y-6">
      <h2 class="text-2xl font-bold"><?= esc_html(get_field("contact_title", "option")); ?></h2>

      <div class="bg-white p-4 rounded-lg shadow flex items-start gap-4">
        <i class="fas fa-envelope text-2xl text-blue-600 mt-1"></i>
        <div>
          <strong>Email:</strong><br>
          <a href="mailto:<?= esc_attr(get_field("contact_email", "option")); ?>" class="text-blue-600 hover:underline">
            <?= esc_html(get_field("contact_email", "option")); ?>
          </a>
        </div>
      </div>

      <div class="bg-white p-4 rounded-lg shadow flex items-start gap-4">
        <i class="fas fa-phone-alt text-2xl text-green-600 mt-1"></i>
        <div>
          <strong>Phone:</strong><br>
          <?= esc_html(get_field("contact_phone", "option")); ?>
        </div>
      </div>

      <div class="bg-white p-4 rounded-lg shadow flex items-start gap-4">
        <i class="fas fa-map-marker-alt text-2xl text-red-600 mt-1"></i>
        <div>
          <strong>Address:</strong><br>
          <?= esc_html(get_field("contact_address", "option")); ?>
        </div>
      </div>

      <div class="flex gap-4 pt-2">
        <?php if ($fb = get_field("contact_facebook", "option")): ?>
          <a href="<?= esc_url($fb); ?>" target="_blank" class="text-blue-600 text-2xl"><i class="fab fa-facebook"></i></a>
        <?php endif; ?>
        <?php if ($tw = get_field("contact_twitter", "option")): ?>
          <a href="<?= esc_url($tw); ?>" target="_blank" class="text-blue-400 text-2xl"><i class="fab fa-twitter"></i></a>
        <?php endif; ?>
        <?php if ($ig = get_field("contact_instagram", "option")): ?>
          <a href="<?= esc_url($ig); ?>" target="_blank" class="text-pink-500 text-2xl"><i class="fab fa-instagram"></i></a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Complaint Form AJAX -->
    <div>
      <div id="complaint-response" class="hidden p-4 rounded mb-4"></div>

      <form id="complaint-form" method="post" class="space-y-4 bg-white p-6 rounded shadow">
        <div>
          <label class="block font-semibold mb-1">Your Name</label>
          <input type="text" name="name" required class="w-full border px-4 py-2 rounded">
        </div>
        <div>
          <label class="block font-semibold mb-1">Email</label>
          <input type="email" name="email" required class="w-full border px-4 py-2 rounded">
        </div>
        <div>
          <label class="block font-semibold mb-1">Phone (optional)</label>
          <input type="text" name="phone" class="w-full border px-4 py-2 rounded">
        </div>
        <div>
          <label class="block font-semibold mb-1">Subject</label>
          <input type="text" name="subject" required class="w-full border px-4 py-2 rounded">
        </div>
        <div>
          <label class="block font-semibold mb-1">Message</label>
          <textarea name="message" rows="5" required class="w-full border px-4 py-2 rounded"></textarea>
        </div>
        <div>
          <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Send Complaint</button>
        </div>
      </form>
    </div>
  </div>
</section>

<script>
  document.getElementById("complaint-form").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = e.target;
    const responseBox = document.getElementById("complaint-response");

    const formData = new FormData(form);
    formData.append("action", "submit_complaint_ajax");

    fetch("<?= admin_url('admin-ajax.php'); ?>", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      responseBox.classList.remove("hidden");
      responseBox.className = "p-4 rounded mb-4 " + (data.success ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800");
      responseBox.textContent = data.message;

      if (data.success) form.reset();
    })
    .catch(() => {
      responseBox.classList.remove("hidden");
      responseBox.className = "p-4 rounded mb-4 bg-red-100 text-red-800";
      responseBox.textContent = "An unexpected error occurred.";
    });
  });
</script>

<?php get_footer(); ?>
