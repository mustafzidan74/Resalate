<?php
/**
 * Template Name: Masjid donations
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/masjid-login'));
    exit;
}

$current_user_id = get_current_user_id();
$current_user = wp_get_current_user();
if (!in_array('masjid', (array)$current_user->roles)) {
    wp_die(__('You do not have permission to access this page.', 'text-domain'));
}

// التعامل مع نشر أو تعديل الدرس
error_log("🔍 Checking request method: " . $_SERVER['REQUEST_METHOD']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_submit'])) {
    error_log("🚀 FORM SUBMITTED: donation_submit exists");
    
    $current_user_id = get_current_user_id(); 
    
    $title = sanitize_text_field($_POST['text-title']);
    $content = wp_kses_post($_POST['donation_content'] ?? '');
    $image_id = 0;
    $donation_id = isset($_POST['donation_id']) ? intval($_POST['donation_id']) : 0;
    
    // Validate fields
    if (empty($title)) {
        error_log("❌ Title is missing.");
        wp_die('donation title is required.');
    }
    
    // رفع الصورة
    if (!empty($_FILES['mosque-image']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        $image_id = media_handle_upload('mosque-image', 0);
    
        if (is_wp_error($image_id)) {
            error_log("❌ Image upload error: " . $image_id->get_error_message());
            $image_id = 0;
        } else {
            error_log("✅ Image uploaded. ID = $image_id");
        }
    }
    
    $post_args = [
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_author'  => $current_user_id,
        'post_type'    => 'donations'
    ];
    
    if ($donation_id && get_post_field('post_author', $donation_id) == $current_user_id) {
        $post_args['ID'] = $donation_id;
        $post_id = wp_update_post($post_args, true);
        error_log("✏️ Trying to update donation ID = $donation_id");
    } else {
        $post_id = wp_insert_post($post_args, true);
        error_log("🆕 Trying to insert new donation");
    }
    
    
    if (!empty($_POST['total_amount'])) {
        update_field('total_amount', floatval($_POST['total_amount']), $post_id);
    }
    
    if (!empty($_POST['amount_paid'])) {
        update_field('amount_paid', floatval($_POST['amount_paid']), $post_id);
    }
    
    if (!empty($_POST['currency'])) {
        update_field('currency', sanitize_text_field($_POST['currency']), $post_id);
    }
    
    
    if (is_wp_error($post_id)) {
        error_log("❌ Post insert/update failed: " . $post_id->get_error_message());
        wp_die('Error saving donation: ' . $post_id->get_error_message());
    }
    
    error_log("✅ donation saved successfully. ID = $post_id");
    
    // ربط التصنيف والصورة
    if ($image_id && !is_wp_error($image_id)) {
        set_post_thumbnail($post_id, $image_id);
    }

// wp_redirect(get_permalink($post_id));
// exit;
}

get_header();
?>

      <main class="my-20">
        <div class="container mx-auto px-4 flex items-start gap-6">
          <!-- Sidebar -->
            <?php get_template_part('template-parts/content-dashboard-sidebar'); ?>

          <div class="wrapper donations flex-1">
            <div
              class="head-content flex justify-between items-center flex-wrap gap-4 mb-4"
            >
              <h2 class="sm:text-xl text-lg font-[900]">All donations</h2>

              <!-- Upload Button -->
              <div class="new-donation-btn mb-6">
                <button
                  class="primary-btn px-4 py-2 rounded transition-colors"
                  onclick="opendonationModal()"
                >
                  Upload New donation
                </button>
              </div>
            </div>

            <!-- Modal -->
            <div
              id="donationModal"
              class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-300 px-4"
              onclick="closedonationModalByOverlay(event)"
            >
              <div
                class="bg-white rounded-xl max-w-3xl w-full p-6 relative overflow-y-auto max-h-[90vh] transform scale-95 transition-transform duration-300"
                id="donationModalContent"
              >
                <!-- Close Button -->
                <button
                  onclick="closedonationModal()"
                  class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl"
                >
                  &times;
                </button>

                <!-- Modal Content -->
                <div class="head-content">
                  <h2 class="sm:text-xl text-lg font-[900] mb-4">
                    Add New donation
                  </h2>
                </div>

                <form action="<?php echo esc_url(get_permalink()); ?>" method="post" enctype="multipart/form-data" id="new-donation" class="flex flex-col gap-6">
                  <div class="input-wrapper py-4 px-2 rounded-lg">
                    <div class="box-wrapper space-y-6">

                      <!-- Title Input -->
                      <div>
                        <label for="text-title" class="inline-block mb-2"
                          >Title</label
                        >
                        <input
                          type="text"
                          id="text-title"
                          name="text-title"
                          placeholder="Enter Title"
                          class="w-full border px-4 py-3 rounded"
                        />
                      </div>

                      <div class="input-wrapper rounded-lg">
                        <label
                          for="donation-image"
                          class="success-btn block font-bold mb-2 cursor-pointer py-4 px-8 rounded-lg"
                        >
                          Upload Profile Image
                        </label>

                        <!-- Hidden File Input -->
                        <input
                          type="file"
                          id="donation-image"
                          accept="image/*"
                          name="mosque-image"
                          class="hidden"
                        />

                        <!-- Error Message -->
                        <p
                          id="imageError"
                          class="text-red-500 text-sm mt-1 hidden"
                        >
                          Please select a valid image file (jpg, png, etc.).
                        </p>

                        <!-- Image Preview -->
                        <!-- Image Preview with close button -->
                        <div
                          id="donationImagePreview"
                          class="relative mt-4 hidden w-fit"
                        >
                          <button
                            type="button"
                            class="absolute -top-2 -right-2 text-white z-10"
                            id="removedonationImage"
                            title="Remove image"
                          >
                            <i
                              class="fa-solid fa-circle-xmark text-red-500 text-xl"
                            ></i>
                          </button>
                          <img
                            src=""
                            alt="Preview"
                            class="w-60 h-60 object-cover rounded border"
                          />
                        </div>
                      </div>

                      <!-- Content Editor -->
                      <div>
                        <h2 class="font-[400] mb-2">Content</h2>

                        <!-- Toolbar -->
                        <div id="new-donations-toolbar" class="mb-2">
                          <select class="ql-header">
                            <option value="1">Heading 1</option>
                            <option value="2">Heading 2</option>
                            <option value="3">Heading 3</option>
                            <option value="4">Heading 4</option>
                            <option value="5">Heading 5</option>
                            <option value="6">Heading 6</option>
                            <option value="">Paragraph</option>
                          </select>

                          <button class="ql-bold"></button>
                          <button class="ql-italic"></button>
                          <button class="ql-underline"></button>

                          <select class="ql-color"></select>
                          <select class="ql-background"></select>

                          <button class="ql-link"></button>

                          <select class="ql-align"></select>

                          <button class="ql-list" value="ordered"></button>
                          <button class="ql-list" value="bullet"></button>

                          <button class="ql-image"></button>
                          <button class="ql-clean"></button>
                        </div>

                        <!-- Quill Editor -->
                        <div
                          id="new-donations-container"
                          style="height: 300px"
                          class="border rounded"
                        ></div>
                      </div>
                      
                        <!-- Total Amount -->
                        <div>
                          <label for="total_amount" class="inline-block mb-2">Total Amount</label>
                          <input
                            type="number"
                            id="total_amount"
                            name="total_amount"
                            placeholder="e.g. 10000"
                            class="w-full border px-4 py-3 rounded"
                          />
                        </div>
                        
                        <!-- Amount Paid -->
                        <div>
                          <label for="amount_paid" class="inline-block mb-2">Amount Paid</label>
                          <input
                            type="number"
                            id="amount_paid"
                            name="amount_paid"
                            placeholder="e.g. 2500"
                            class="w-full border px-4 py-3 rounded"
                          />
                        </div>
                        
                        <!-- Currency -->
                        <div>
                          <label for="currency" class="inline-block mb-2">Currency</label>
                          <input
                            type="text"
                            id="currency"
                            name="currency"
                            placeholder="e.g. USD"
                            class="w-full border px-4 py-3 rounded"
                          />
                        </div>
                      
                      
                      
                    </div>
                  </div>
                    <input type="hidden" name="donation_id" id="donation_id" value="">
                    <input type="hidden" name="donation_submit" value="1">
                    <button type="submit" name="donation_submit" class="success-btn px-8 py-3 text-lg font-bold rounded w-fit self-end transition-colors mt-4 inline-block">Publish donation</button>
                </form>
              </div>
            </div>

            <!-- Confirm Delete Modal -->
            <div
              id="confirmDeleteModal"
              class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-300"
              onclick="closeDeleteModalByOverlay(event)"
            >
              <div
                id="confirmDeleteContent"
                class="bg-white p-6 rounded-xl max-w-md w-full text-center transition-transform duration-300 transform scale-95"
                onclick="event.stopPropagation()"
              >
                <h2 class="text-xl font-bold mb-4">Delete donation</h2>
                <p class="mb-6 text-gray-700">
                  Are you sure you want to delete this donation? This action
                  cannot be undone.
                </p>

                <div class="flex justify-center gap-4">
                  <button
                    class="danger-btn px-6 py-2 rounded-lg"
                    onclick="confirmDeleteAction()"
                  >
                    Continue
                  </button>
                  <button
                    class="primary-btn px-6 py-2 rounded-lg"
                    onclick="closeDeleteModal()"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>


            <div class="mb-6">
              <input type="text" id="donationsearch" placeholder="Find the donation..." 
                     class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring focus:border-green-400" />
            </div>

            <!-- CARDS WRAPPER -->
            <div
              class="content-wrapper grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6"
            >
            <?php
            $user_donations = get_posts([
                'post_type' => 'donations',
                'author' => $current_user_id,
                'posts_per_page' => -1,
            ]);
            
            foreach ($user_donations as $donation):
                $thumb = get_the_post_thumbnail_url($donation->ID, 'medium') ?: 'https://placehold.co/600x400';
                $cat_slug = '';
                ?>
                <div class="card-box rounded-lg overflow-hidden shadow-md">
                    <div class="img-box">
                        <img src="<?php echo esc_url($thumb); ?>" alt="donation Image" />
                    </div>
                    <div class="text-box px-4 py-5 bg-white flex flex-col">
                        <h3 class="title text-lg mb-2 font-[600]"><?php echo esc_html($donation->post_title); ?></h3>
                        <p class="desc"><?php echo wp_trim_words(wp_strip_all_tags($donation->post_content), 20); ?></p>
                        <?php
                        $total = get_field('total_amount', $donation->ID);
                        $paid = get_field('amount_paid', $donation->ID);
                        $currency = get_field('currency', $donation->ID);
                        ?>
                        <div class="mt-4 text-sm text-gray-700 space-y-1">
                          <?php if ($total): ?>
                            <p><strong>Total:</strong> <?php echo esc_html($total); ?> <?php echo esc_html($currency ?: ''); ?></p>
                          <?php endif; ?>
                          <?php if ($paid): ?>
                            <p><strong>Paid:</strong> <?php echo esc_html($paid); ?> <?php echo esc_html($currency ?: ''); ?></p>
                          <?php endif; ?>
                        </div>
                        
                        <?php
                        $percentage = 0;
                        if ($total > 0 && $paid >= 0) {
                            $percentage = min(100, round(($paid / $total) * 100));
                        }
                        ?>
                        
                        <?php if ($percentage > 0): ?>
                          <div class="donations-progress-bar mt-12">
                            <h3 class="font-[600] mb-3 sm:text-[1rem] text-[.8rem]">Progress</h3>
                        
                            <div class="parent">
                              <span
                                class="progress-bar"
                                data-progress="<?php echo esc_attr($percentage); ?>%"
                                style="--progress: <?php echo esc_attr($percentage); ?>%"
                              ></span>
                            </div>
                          </div>
                        <?php endif; ?>

                        
                        
                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <button
                              class="primary-btn py-2 px-6 rounded-lg"
onclick='editdonation(<?php echo htmlspecialchars(
    wp_json_encode([
        'ID'           => $donation->ID,
        'title'        => $donation->post_title,
        'content'      => $donation->post_content,
        'image'        => $thumb,
        'total_amount' => get_field('total_amount', $donation->ID),
        'amount_paid'  => get_field('amount_paid', $donation->ID),
        'currency'     => get_field('currency', $donation->ID),
    ], JSON_UNESCAPED_UNICODE),
    ENT_QUOTES,
    'UTF-8'
); ?>)'
                            >
                              Edit
                            </button>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <?php wp_nonce_field('delete_donation_' . $donation->ID); ?>
                                <input type="hidden" name="action" value="delete_donation">
                                <input type="hidden" name="donation_id" value="<?php echo esc_attr($donation->ID); ?>">
                                <button type="submit" class="danger-btn py-2 px-6 rounded-lg" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($user_donations)): ?>
              <p class="text-gray-500 text-lg">No donations found.</p>
            <?php endif; ?>
            
            </div>
            
            
            <p id="nodonationsMsg" class="text-gray-500 text-lg hidden">No donations found.</p>

            
          </div>
        </div>
      </main>


<?php
get_footer();

?>

<script>
    const modal = document.getElementById("donationModal");
const modalContent = document.getElementById("donationModalContent");

function opendonationModal() {
  // إظهار المودال
  modal.classList.remove("pointer-events-none", "opacity-0");
  modal.classList.add("opacity-100");

  modalContent.classList.remove("scale-95");
  modalContent.classList.add("scale-100");

  // 🧹 تفريغ الحقول
  document.getElementById('donation_id').value = '';
  document.getElementById('text-title').value = '';
  donationImageImg.src = '';
  donationImagePreview.classList.add('hidden');
  donationImageInput.value = '';
  
  const quill = Quill.find(document.querySelector('#new-donations-container'));
  quill.setText(''); // يفرغ المحتوى
}

function closedonationModal() {
  modal.classList.add("opacity-0");
  modal.classList.remove("opacity-100");

  modalContent.classList.add("scale-95");
  modalContent.classList.remove("scale-100");

  // Delay hiding to allow animation to complete
  setTimeout(() => {
    modal.classList.add("pointer-events-none");
  }, 300);
}

// Close when clicking the overlay (but not the content)
function closedonationModalByOverlay(event) {
  if (event.target === modal) {
    closedonationModal();
  }
}

// Initialize Quill Editor
let quill;
window.addEventListener("DOMContentLoaded", () => {
  quill = new Quill("#new-donations-container", {
    theme: "snow",
    modules: {
      toolbar: "#new-donations-toolbar",
    },
    placeholder: "Write your donation content here...",
  });

  // Handle form submission
  document.getElementById('new-donation').addEventListener('submit', function(e) {
    // Add Quill content to form
    const contentInput = document.createElement('input');
    contentInput.type = 'hidden';
    contentInput.name = 'donation_content';
    contentInput.value = quill.root.innerHTML;
    this.appendChild(contentInput);
    
    // Disable submit button
    const submitBtn = this.querySelector('[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publishing...';
  });
});

const donationImageInput = document.getElementById("donation-image");
const imageError = document.getElementById("imageError");
const donationImagePreview = document.getElementById("donationImagePreview");
const donationImageImg = donationImagePreview.querySelector("img");
const removedonationImageBtn = document.getElementById("removedonationImage");

donationImageInput.addEventListener("change", function () {
  const file = this.files[0];
  if (!file) return;

  if (!file.type.startsWith("image/")) {
    imageError.classList.remove("hidden");
    donationImagePreview.classList.add("hidden");
    donationImageImg.src = "";
    return;
  }

  imageError.classList.add("hidden");

  const reader = new FileReader();
  reader.onload = function (e) {
    donationImageImg.src = e.target.result;
    donationImagePreview.classList.remove("hidden");
  };
  reader.readAsDataURL(file);
});

removedonationImageBtn.addEventListener("click", function () {
  donationImageImg.src = "";
  donationImagePreview.classList.add("hidden");
  donationImageInput.value = "";
});

const confirmDeleteModal = document.getElementById("confirmDeleteModal");
const confirmDeleteContent = document.getElementById("confirmDeleteContent");

function openDeleteModal() {
  confirmDeleteModal.classList.remove("pointer-events-none", "opacity-0");
  confirmDeleteModal.classList.add("opacity-100");

  confirmDeleteContent.classList.remove("scale-95");
  confirmDeleteContent.classList.add("scale-100");
}

function closeDeleteModal() {
  confirmDeleteModal.classList.add("opacity-0");
  confirmDeleteModal.classList.remove("opacity-100");

  confirmDeleteContent.classList.add("scale-95");
  confirmDeleteContent.classList.remove("scale-100");

  setTimeout(() => {
    confirmDeleteModal.classList.add("pointer-events-none");
  }, 300);
}

function closeDeleteModalByOverlay(event) {
  if (event.target === confirmDeleteModal) {
    closeDeleteModal();
  }
}

// Action on "Continue" click
function confirmDeleteAction() {
  closeDeleteModal();
}
function editdonation(data) {
  opendonationModal();

  document.getElementById('text-title').value = data.title;
  document.getElementById('donation_id').value = data.ID;

  // ✅ تحديث المحرر
  quill.root.innerHTML = data.content;

  // ✅ تحديث الصورة
  if (data.image && data.image !== 'https://placehold.co/600x400') {
    donationImageImg.src = data.image;
    donationImagePreview.classList.remove('hidden');
  } else {
    donationImageImg.src = '';
    donationImagePreview.classList.add('hidden');
  }
  
  document.getElementById('total_amount').value = data.total_amount || '';
    document.getElementById('amount_paid').value = data.amount_paid || '';
    document.getElementById('currency').value = data.currency || '';

  
}

document.getElementById('new-donation').addEventListener('submit', function(e) {
  const submitBtn = this.querySelector('[type="submit"]');
  submitBtn.disabled = true;
});


// قبل إرسال الفورم، أضف المحتوى إلى حقل مخفي
document.getElementById('new-donation').addEventListener('submit', function(e) {
  const quill = Quill.find(document.querySelector('#new-donations-container'));
  const content = document.createElement('textarea');
  content.name = 'donation_content';
  content.value = quill.root.innerHTML;
  this.appendChild(content);
});

const searchInput = document.getElementById("donationsearch");

if (searchInput) {
  searchInput.addEventListener("input", function () {
    const keyword = this.value.toLowerCase().trim();
    const cards = document.querySelectorAll(".card-box");
    let visibleCount = 0;

    cards.forEach(card => {
      const title = card.querySelector(".title")?.innerText.toLowerCase() || "";
      if (title.includes(keyword)) {
        card.style.display = "block";
        visibleCount++;
      } else {
        card.style.display = "none";
      }
    });

    // تحديث رسالة "لا يوجد دروس"
    const nodonationsMsg = document.getElementById("nodonationsMsg");
    if (nodonationsMsg) {
      nodonationsMsg.style.display = visibleCount === 0 ? "block" : "none";
    }

    // إزالة تفعيل الفلاتر إذا تم البحث
    if (keyword !== "") {
      document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    }
  });
}

</script>