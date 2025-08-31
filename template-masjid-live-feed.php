<?php
/**
 * Template Name: Masjid live_feeds
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['live_feed_submit'])) {
    error_log("🚀 FORM SUBMITTED: live_feed_submit exists");
    
    $current_user_id = get_current_user_id(); 
    
    $title = sanitize_text_field($_POST['text-title']);
    $content = wp_kses_post($_POST['live_feed_content'] ?? '');
    $image_id = 0;
    $live_feed_id = isset($_POST['live_feed_id']) ? intval($_POST['live_feed_id']) : 0;
    
    // Validate fields
    if (empty($title)) {
        error_log("❌ Title is missing.");
        wp_die('live_feed title is required.');
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
        'post_type'    => 'live-feed'
    ];
    
    if ($live_feed_id && get_post_field('post_author', $live_feed_id) == $current_user_id) {
        $post_args['ID'] = $live_feed_id;
        $post_id = wp_update_post($post_args, true);
        error_log("✏️ Trying to update live_feed ID = $live_feed_id");
    } else {
        $post_id = wp_insert_post($post_args, true);
        error_log("🆕 Trying to insert new live_feed");
    }
    
    if (is_wp_error($post_id)) {
        error_log("❌ Post insert/update failed: " . $post_id->get_error_message());
        wp_die('Error saving live_feed: ' . $post_id->get_error_message());
    }
    
    $iframe_url = esc_url_raw($_POST['iframe_url'] ?? '');
    if (!empty($iframe_url)) {
        update_post_meta($post_id, 'iframe_url', $iframe_url);
    }
    
    
    error_log("✅ live_feed saved successfully. ID = $post_id");
    
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

          <div class="wrapper live_feeds flex-1">
            <div
              class="head-content flex justify-between items-center flex-wrap gap-4 mb-4"
            >
              <h2 class="sm:text-xl text-lg font-[900]">All Live Feeds</h2>

              <!-- Upload Button -->
              <div class="new-live_feed-btn mb-6">
                <button
                  class="primary-btn px-4 py-2 rounded transition-colors"
                  onclick="openlive_feedModal()"
                >
                  Upload New Live Feed
                </button>
              </div>
            </div>

            <!-- Modal -->
            <div
              id="live_feedModal"
              class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-300 px-4"
              onclick="closelive_feedModalByOverlay(event)"
            >
              <div
                class="bg-white rounded-xl max-w-3xl w-full p-6 relative overflow-y-auto max-h-[90vh] transform scale-95 transition-transform duration-300"
                id="live_feedModalContent"
              >
                <!-- Close Button -->
                <button
                  onclick="closelive_feedModal()"
                  class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl"
                >
                  &times;
                </button>

                <!-- Modal Content -->
                <div class="head-content">
                  <h2 class="sm:text-xl text-lg font-[900] mb-4">
                    Add New Live Feed
                  </h2>
                </div>

                <form action="<?php echo esc_url(get_permalink()); ?>" method="post" enctype="multipart/form-data" id="new-live_feed" class="flex flex-col gap-6">
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
                          for="live_feed-image"
                          class="success-btn block font-bold mb-2 cursor-pointer py-4 px-8 rounded-lg"
                        >
                          Upload Profile Image
                        </label>

                        <!-- Hidden File Input -->
                        <input
                          type="file"
                          id="live_feed-image"
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
                          id="live_feedImagePreview"
                          class="relative mt-4 hidden w-fit"
                        >
                          <button
                            type="button"
                            class="absolute -top-2 -right-2 text-white z-10"
                            id="removelive_feedImage"
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
                        <div id="new-live_feeds-toolbar" class="mb-2">
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
                          id="new-live_feeds-container"
                          style="height: 300px"
                          class="border rounded"
                        ></div>
                      </div>
                      
                      <!-- Iframe URL Input -->
                        <div>
                          <label for="iframe_url" class="inline-block mb-2 font-semibold">Iframe URL</label>
                          <input
                            type="url"
                            id="iframe_url"
                            name="iframe_url"
                            placeholder="مثال: https://www.youtube.com/embed/XXXX"
                            class="w-full border px-4 py-3 rounded"
                          />
                          <small class="text-gray-500 block mt-2">مثال: https://www.youtube.com/embed/xxxxxxxxx</small>
                        
                          <!-- Iframe Preview -->
                          <div id="iframePreviewWrapper" class="mt-4 hidden">
                            <iframe id="iframePreview" class="w-full h-64 rounded border" allowfullscreen></iframe>
                          </div>
                        </div>

                      
                    </div>
                  </div>
                    <input type="hidden" name="live_feed_id" id="live_feed_id" value="">
                    <input type="hidden" name="live_feed_submit" value="1">
                    <button type="submit" name="live_feed_submit" class="success-btn px-8 py-3 text-lg font-bold rounded w-fit self-end transition-colors mt-4 inline-block">Publish Live Feed</button>
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
                <h2 class="text-xl font-bold mb-4">Delete live_feed</h2>
                <p class="mb-6 text-gray-700">
                  Are you sure you want to delete this live_feed? This action
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
              <input type="text" id="live_feedSearch" placeholder="Find the Live Feeds..." 
                     class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring focus:border-green-400" />
            </div>

            <!-- CARDS WRAPPER -->
            <div
              class="content-wrapper grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6"
            >
            <?php
            $user_live_feeds = get_posts([
                'post_type' => 'live-feed',
                'author' => $current_user_id,
                'posts_per_page' => -1,
            ]);
            
            foreach ($user_live_feeds as $live_feed):
                $thumb = get_the_post_thumbnail_url($live_feed->ID, 'medium') ?: 'https://placehold.co/600x400';
                $cat_slug = '';
                ?>
                <div class="card-box rounded-lg overflow-hidden shadow-md">
                    <div class="img-box">
                        <img src="<?php echo esc_url($thumb); ?>" alt="live_feed Image" />
                    </div>
                    <div class="text-box px-4 py-5 bg-white flex flex-col">
                        <h3 class="title text-lg mb-2 font-[600]"><?php echo esc_html($live_feed->post_title); ?></h3>
                        <p class="desc"><?php echo wp_trim_words(wp_strip_all_tags($live_feed->post_content), 20); ?></p>
                        <div class="mt-6 grid grid-cols-2 gap-3">
                        <button
                          class="primary-btn py-2 px-6 rounded-lg"
                          onclick='editlive_feed(<?php echo json_encode([
                            'ID' => $live_feed->ID,
                            'title' => $live_feed->post_title,
                            'content' => $live_feed->post_content,
                            'image' => $thumb,
                            'iframe' => get_post_meta($live_feed->ID, 'iframe_url', true),
                          ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'
                        >
                          Edit
                        </button>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <?php wp_nonce_field('delete_live_feed_' . $live_feed->ID); ?>
                                <input type="hidden" name="action" value="delete_live_feed">
                                <input type="hidden" name="live_feed_id" value="<?php echo esc_attr($live_feed->ID); ?>">
                                <button type="submit" class="danger-btn py-2 px-6 rounded-lg" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($user_live_feeds)): ?>
              <p class="text-gray-500 text-lg">No Live Feeds found.</p>
            <?php endif; ?>
            
            </div>
            
            
            <p id="nolive_feedsMsg" class="text-gray-500 text-lg hidden">No Live Feeds found.</p>

            
          </div>
        </div>
      </main>


<?php
get_footer();

?>

<script>
    const modal = document.getElementById("live_feedModal");
const modalContent = document.getElementById("live_feedModalContent");

function openlive_feedModal() {
  // إظهار المودال
  modal.classList.remove("pointer-events-none", "opacity-0");
  modal.classList.add("opacity-100");

  modalContent.classList.remove("scale-95");
  modalContent.classList.add("scale-100");

  // 🧹 تفريغ الحقول
  document.getElementById('live_feed_id').value = '';
  document.getElementById('text-title').value = '';
  live_feedImageImg.src = '';
  live_feedImagePreview.classList.add('hidden');
  live_feedImageInput.value = '';
  
  const quill = Quill.find(document.querySelector('#new-live_feeds-container'));
  quill.setText(''); // يفرغ المحتوى
}

function closelive_feedModal() {
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
function closelive_feedModalByOverlay(event) {
  if (event.target === modal) {
    closelive_feedModal();
  }
}

// Initialize Quill Editor
let quill;
window.addEventListener("DOMContentLoaded", () => {
    
  quill = new Quill("#new-live_feeds-container", {
    theme: "snow",
    modules: {
      toolbar: "#new-live_feeds-toolbar",
    },
    placeholder: "Write your live feed content here...",
  });

    const iframeInput = document.getElementById("iframe_url");
    const iframePreviewWrapper = document.getElementById("iframePreviewWrapper");
    const iframePreview = document.getElementById("iframePreview");
    
    iframeInput.addEventListener("input", function () {
      const url = this.value.trim();
      if (url.startsWith("http")) {
        iframePreview.src = url;
        iframePreviewWrapper.classList.remove("hidden");
      } else {
        iframePreview.src = "";
        iframePreviewWrapper.classList.add("hidden");
      }
    });


  // Handle form submission
  document.getElementById('new-live_feed').addEventListener('submit', function(e) {
    // Add Quill content to form
    const contentInput = document.createElement('input');
    contentInput.type = 'hidden';
    contentInput.name = 'live_feed_content';
    contentInput.value = quill.root.innerHTML;
    this.appendChild(contentInput);
    
    // Disable submit button
    const submitBtn = this.querySelector('[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publishing...';
  });
});

const live_feedImageInput = document.getElementById("live_feed-image");
const imageError = document.getElementById("imageError");
const live_feedImagePreview = document.getElementById("live_feedImagePreview");
const live_feedImageImg = live_feedImagePreview.querySelector("img");
const removelive_feedImageBtn = document.getElementById("removelive_feedImage");

live_feedImageInput.addEventListener("change", function () {
  const file = this.files[0];
  if (!file) return;

  if (!file.type.startsWith("image/")) {
    imageError.classList.remove("hidden");
    live_feedImagePreview.classList.add("hidden");
    live_feedImageImg.src = "";
    return;
  }

  imageError.classList.add("hidden");

  const reader = new FileReader();
  reader.onload = function (e) {
    live_feedImageImg.src = e.target.result;
    live_feedImagePreview.classList.remove("hidden");
  };
  reader.readAsDataURL(file);
});

removelive_feedImageBtn.addEventListener("click", function () {
  live_feedImageImg.src = "";
  live_feedImagePreview.classList.add("hidden");
  live_feedImageInput.value = "";
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
function editlive_feed(data) {
  openlive_feedModal();

  document.getElementById('text-title').value = data.title;
  document.getElementById('live_feed_id').value = data.ID;

  // ✅ تحديث المحرر
  quill.root.innerHTML = data.content;

  // ✅ تحديث الصورة
  if (data.image && data.image !== 'https://placehold.co/600x400') {
    live_feedImageImg.src = data.image;
    live_feedImagePreview.classList.remove('hidden');
  } else {
    live_feedImageImg.src = '';
    live_feedImagePreview.classList.add('hidden');
  }
  
 document.getElementById('iframe_url').value = data.iframe || '';
const previewFrame = document.getElementById('iframePreview');
if (data.iframe && data.iframe !== '') {
  previewFrame.src = data.iframe;
  iframePreviewWrapper.classList.remove('hidden');
} else {
  previewFrame.src = '';
  iframePreviewWrapper.classList.add('hidden');
}
 
  
}

document.getElementById('new-live_feed').addEventListener('submit', function(e) {
  const submitBtn = this.querySelector('[type="submit"]');
  submitBtn.disabled = true;
});


// قبل إرسال الفورم، أضف المحتوى إلى حقل مخفي
document.getElementById('new-live_feed').addEventListener('submit', function(e) {
  const quill = Quill.find(document.querySelector('#new-live_feeds-container'));
  const content = document.createElement('textarea');
  content.name = 'live_feed_content';
  content.value = quill.root.innerHTML;
  this.appendChild(content);
});

const searchInput = document.getElementById("live_feedSearch");

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
    const nolive_feedsMsg = document.getElementById("nolive_feedsMsg");
    if (nolive_feedsMsg) {
      nolive_feedsMsg.style.display = visibleCount === 0 ? "block" : "none";
    }

    // إزالة تفعيل الفلاتر إذا تم البحث
    if (keyword !== "") {
      document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    }
  });
}

</script>