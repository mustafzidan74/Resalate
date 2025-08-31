<?php
/**
 * Template Name: Masjid From masjid to masjid
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['masjid_to_masjid_submit'])) {
    error_log("🚀 FORM SUBMITTED: masjid_to_masjid_submit exists");
    
    $current_user_id = get_current_user_id(); 
    
    $title = sanitize_text_field($_POST['text-title']);
    $content = wp_kses_post($_POST['masjid_to_masjid_content'] ?? '');
    $category_id = intval($_POST['upload-category']);
    $image_id = 0;
    $masjid_to_masjid_id = isset($_POST['masjid_to_masjid_id']) ? intval($_POST['masjid_to_masjid_id']) : 0;
    
    // Validate fields
    if (empty($title)) {
        error_log("❌ Title is missing.");
        wp_die('masjid_to_masjid title is required.');
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
        'post_type'    => 'masjid-to-masjid'
    ];
    
    if ($masjid_to_masjid_id && get_post_field('post_author', $masjid_to_masjid_id) == $current_user_id) {
        $post_args['ID'] = $masjid_to_masjid_id;
        $post_id = wp_update_post($post_args, true);
        error_log("✏️ Trying to update masjid_to_masjid ID = $masjid_to_masjid_id");
    } else {
        $post_id = wp_insert_post($post_args, true);
        error_log("🆕 Trying to insert new masjid_to_masjid");
    }
    
    if (is_wp_error($post_id)) {
        error_log("❌ Post insert/update failed: " . $post_id->get_error_message());
        wp_die('Error saving masjid_to_masjid: ' . $post_id->get_error_message());
    }
    
    error_log("✅ masjid_to_masjid saved successfully. ID = $post_id");
    
    // ربط التصنيف والصورة
    wp_set_post_terms($post_id, [$category_id], 'from-masjid-to-masjid-category');
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

          <div class="wrapper masjid_to_masjids flex-1">
            <div
              class="head-content flex justify-between items-center flex-wrap gap-4 mb-4"
            >
              <h2 class="sm:text-xl text-lg font-[900]">All From Masjid to Masjids</h2>

              <!-- Upload Button -->
              <div class="new-masjid_to_masjid-btn mb-6">
                <button
                  class="primary-btn px-4 py-2 rounded transition-colors"
                  onclick="openmasjid_to_masjidModal()"
                >
                  Upload New From Masjid to Masjids
                </button>
              </div>
            </div>

            <!-- Modal -->
            <div
              id="masjid_to_masjidModal"
              class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-300 px-4"
              onclick="closemasjid_to_masjidModalByOverlay(event)"
            >
              <div
                class="bg-white rounded-xl max-w-3xl w-full p-6 relative overflow-y-auto max-h-[90vh] transform scale-95 transition-transform duration-300"
                id="masjid_to_masjidModalContent"
              >
                <!-- Close Button -->
                <button
                  onclick="closemasjid_to_masjidModal()"
                  class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl"
                >
                  &times;
                </button>

                <!-- Modal Content -->
                <div class="head-content">
                  <h2 class="sm:text-xl text-lg font-[900] mb-4">
                    Add New From Masjid to Masjids
                  </h2>
                </div>

                <form action="<?php echo esc_url(get_permalink()); ?>" method="post" enctype="multipart/form-data" id="new-masjid_to_masjid" class="flex flex-col gap-6">
                  <div class="input-wrapper py-4 px-2 rounded-lg">
                    <div class="box-wrapper space-y-6">
                      <!-- Select Box -->
                      <div>
                        <label for="category" class="inline-block mb-2"
                          >Category</label
                        >
                        <select id="category" name="upload-category" class="w-full border px-4 py-3 rounded appearance-none">
                          <option value="">اختر تصنيفًا</option>
                          <?php
                          $terms = get_terms([
                            'taxonomy' => 'from-masjid-to-masjid-category',
                            'hide_empty' => false,
                          ]);
                          foreach ($terms as $term) {
                            echo '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
                          }
                          ?>
                        </select>
                        </div>

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
                          for="masjid_to_masjid-image"
                          class="success-btn block font-bold mb-2 cursor-pointer py-4 px-8 rounded-lg"
                        >
                          Upload Profile Image
                        </label>

                        <!-- Hidden File Input -->
                        <input
                          type="file"
                          id="masjid_to_masjid-image"
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
                          id="masjid_to_masjidImagePreview"
                          class="relative mt-4 hidden w-fit"
                        >
                          <button
                            type="button"
                            class="absolute -top-2 -right-2 text-white z-10"
                            id="removemasjid_to_masjidImage"
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
                        <div id="new-masjid_to_masjids-toolbar" class="mb-2">
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
                          id="new-masjid_to_masjids-container"
                          style="height: 300px"
                          class="border rounded"
                        ></div>
                      </div>
                    </div>
                  </div>
                    <input type="hidden" name="masjid_to_masjid_id" id="masjid_to_masjid_id" value="">
                    <input type="hidden" name="masjid_to_masjid_submit" value="1">
                    <button type="submit" name="masjid_to_masjid_submit" class="success-btn px-8 py-3 text-lg font-bold rounded w-fit self-end transition-colors mt-4 inline-block">Publish From Masjid to Masjids</button>
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
                <h2 class="text-xl font-bold mb-4">Delete masjid_to_masjid</h2>
                <p class="mb-6 text-gray-700">
                  Are you sure you want to delete this masjid_to_masjid? This action
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

            <!-- FILTER BUTTONS -->
            <?php
            $user_masjid_to_masjids = get_posts([
                'post_type' => 'masjid-to-masjid',
                'author' => $current_user_id,
                'posts_per_page' => -1,
                'fields' => 'ids',
            ]);
            
            $used_terms = [];
            
            if (!empty($user_masjid_to_masjids)) {
                foreach ($user_masjid_to_masjids as $post_id) {
                    $terms = wp_get_post_terms($post_id, 'from-masjid-to-masjid-category');
                    foreach ($terms as $term) {
                        $used_terms[$term->slug] = $term->name;
                    }
                }
            }
            ?>
            
            <div class="mb-6">
              <input type="text" id="masjid_to_masjidSearch" placeholder="Find the From Masjid to Masjids..." 
                     class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring focus:border-green-400" />
            </div>
            
            <?php if (!empty($used_terms)): ?>
              <div class="mb-6 flex flex-wrap gap-3">
                <button class="filter-btn success-btn py-2 px-4 rounded active" data-filter="all">All</button>
                <?php foreach ($used_terms as $slug => $name): ?>
                  <button class="filter-btn success-btn py-2 px-4 rounded" data-filter="<?php echo esc_attr($slug); ?>">
                    <?php echo esc_html($name); ?>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
            

            <!-- CARDS WRAPPER -->
            <div
              class="content-wrapper grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6"
            >
            <?php
            $user_masjid_to_masjids = get_posts([
                'post_type' => 'masjid-to-masjid',
                'author' => $current_user_id,
                'posts_per_page' => -1,
            ]);
            
            foreach ($user_masjid_to_masjids as $masjid_to_masjid):
                $thumb = get_the_post_thumbnail_url($masjid_to_masjid->ID, 'medium') ?: 'https://placehold.co/600x400';
                $category = get_the_terms($masjid_to_masjid->ID, 'from-masjid-to-masjid-category');
                $cat_slug = $category ? $category[0]->slug : 'uncategorized';
                ?>
                <div class="card-box rounded-lg overflow-hidden shadow-md" data-category="<?php echo esc_attr($cat_slug); ?>">
                    <div class="img-box">
                        <img src="<?php echo esc_url($thumb); ?>" alt="masjid_to_masjid Image" />
                    </div>
                    <div class="text-box px-4 py-5 bg-white flex flex-col">
                        <h3 class="title text-lg mb-2 font-[600]"><?php echo esc_html($masjid_to_masjid->post_title); ?></h3>
<p class="desc"><?php echo wp_trim_words(wp_strip_all_tags($masjid_to_masjid->post_content), 20); ?></p>
                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <button
                              class="primary-btn py-2 px-6 rounded-lg"
                            onclick='editmasjid_to_masjid(<?php echo htmlspecialchars(
                                wp_json_encode([
                                    'ID'       => $masjid_to_masjid->ID,
                                    'title'    => $masjid_to_masjid->post_title,
                                    'content'  => $masjid_to_masjid->post_content,
                                    'category' => $category ? $category[0]->term_id : 0,
                                    'image'    => $thumb
                                ], JSON_UNESCAPED_UNICODE),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>)'
                            >
                              Edit
                            </button>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <?php wp_nonce_field('delete_masjid_to_masjid_' . $masjid_to_masjid->ID); ?>
                                <input type="hidden" name="action" value="delete_masjid_to_masjid">
                                <input type="hidden" name="masjid_to_masjid_id" value="<?php echo esc_attr($masjid_to_masjid->ID); ?>">
                                <button type="submit" class="danger-btn py-2 px-6 rounded-lg" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($user_masjid_to_masjids)): ?>
              <p class="text-gray-500 text-lg">No From Masjid to Masjids found.</p>
            <?php endif; ?>
            
            </div>
            
            
            <p id="nomasjid_to_masjidsMsg" class="text-gray-500 text-lg hidden">No From Masjid to Masjids found.</p>

            
          </div>
        </div>
      </main>


<?php
get_footer();

?>

<script>
    const modal = document.getElementById("masjid_to_masjidModal");
const modalContent = document.getElementById("masjid_to_masjidModalContent");

function openmasjid_to_masjidModal() {
  // إظهار المودال
  modal.classList.remove("pointer-events-none", "opacity-0");
  modal.classList.add("opacity-100");

  modalContent.classList.remove("scale-95");
  modalContent.classList.add("scale-100");

  // 🧹 تفريغ الحقول
  document.getElementById('masjid_to_masjid_id').value = '';
  document.getElementById('text-title').value = '';
  document.querySelector('[name="upload-category"]').selectedIndex = 0;
  masjid_to_masjidImageImg.src = '';
  masjid_to_masjidImagePreview.classList.add('hidden');
  masjid_to_masjidImageInput.value = '';
  
  const quill = Quill.find(document.querySelector('#new-masjid_to_masjids-container'));
  quill.setText(''); // يفرغ المحتوى
}

function closemasjid_to_masjidModal() {
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
function closemasjid_to_masjidModalByOverlay(event) {
  if (event.target === modal) {
    closemasjid_to_masjidModal();
  }
}

// Initialize Quill Editor
let quill;
window.addEventListener("DOMContentLoaded", () => {
  quill = new Quill("#new-masjid_to_masjids-container", {
    theme: "snow",
    modules: {
      toolbar: "#new-masjid_to_masjids-toolbar",
    },
    placeholder: "Write your From Masjid to Masjids",
  });

  // Handle form submission
  document.getElementById('new-masjid_to_masjid').addEventListener('submit', function(e) {
    // Add Quill content to form
    const contentInput = document.createElement('input');
    contentInput.type = 'hidden';
    contentInput.name = 'masjid_to_masjid_content';
    contentInput.value = quill.root.innerHTML;
    this.appendChild(contentInput);
    
    // Disable submit button
    const submitBtn = this.querySelector('[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publishing...';
  });
});

const masjid_to_masjidImageInput = document.getElementById("masjid_to_masjid-image");
const imageError = document.getElementById("imageError");
const masjid_to_masjidImagePreview = document.getElementById("masjid_to_masjidImagePreview");
const masjid_to_masjidImageImg = masjid_to_masjidImagePreview.querySelector("img");
const removemasjid_to_masjidImageBtn = document.getElementById("removemasjid_to_masjidImage");

masjid_to_masjidImageInput.addEventListener("change", function () {
  const file = this.files[0];
  if (!file) return;

  if (!file.type.startsWith("image/")) {
    imageError.classList.remove("hidden");
    masjid_to_masjidImagePreview.classList.add("hidden");
    masjid_to_masjidImageImg.src = "";
    return;
  }

  imageError.classList.add("hidden");

  const reader = new FileReader();
  reader.onload = function (e) {
    masjid_to_masjidImageImg.src = e.target.result;
    masjid_to_masjidImagePreview.classList.remove("hidden");
  };
  reader.readAsDataURL(file);
});

removemasjid_to_masjidImageBtn.addEventListener("click", function () {
  masjid_to_masjidImageImg.src = "";
  masjid_to_masjidImagePreview.classList.add("hidden");
  masjid_to_masjidImageInput.value = "";
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
function editmasjid_to_masjid(data) {
  openmasjid_to_masjidModal();

  document.getElementById('text-title').value = data.title;
  document.getElementById('masjid_to_masjid_id').value = data.ID;

  // ✅ تحديث التصنيف مع Select2
  const $category = jQuery('#category');
  $category.val(data.category).trigger('change'); // ← مهم

  // ✅ تحديث المحرر
  quill.root.innerHTML = data.content;

  // ✅ تحديث الصورة
  if (data.image && data.image !== 'https://placehold.co/600x400') {
    masjid_to_masjidImageImg.src = data.image;
    masjid_to_masjidImagePreview.classList.remove('hidden');
  } else {
    masjid_to_masjidImageImg.src = '';
    masjid_to_masjidImagePreview.classList.add('hidden');
  }
}

document.getElementById('new-masjid_to_masjid').addEventListener('submit', function(e) {
  const submitBtn = this.querySelector('[type="submit"]');
  submitBtn.disabled = true;
});


// قبل إرسال الفورم، أضف المحتوى إلى حقل مخفي
document.getElementById('new-masjid_to_masjid').addEventListener('submit', function(e) {
  const quill = Quill.find(document.querySelector('#new-masjid_to_masjids-container'));
  const content = document.createElement('textarea');
  content.name = 'masjid_to_masjid_content';
  content.value = quill.root.innerHTML;
  this.appendChild(content);
});


document.addEventListener("DOMContentLoaded", function () {
  // تأكد أن jQuery و select2 متاحان
  if (typeof jQuery !== "undefined" && jQuery().select2) {
    jQuery('#category').select2({
      width: '100%',
      placeholder: "Select category",
      allowClear: true
    });
  } else {
    console.warn("⚠️ Select2 or jQuery not loaded.");
  }
});

document.querySelectorAll('.filter-btn').forEach(button => {
  button.addEventListener('click', function () {
    // إزالة الزر الفعّال من كل الأزرار
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));

    // تفعيل الزر الحالي
    this.classList.add('active');

    const filter = this.dataset.filter;
    const cards = document.querySelectorAll('.card-box');

    cards.forEach(card => {
      if (filter === 'all' || card.dataset.category === filter) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });

    // إظهار أو إخفاء رسالة لا يوجد دروس
    const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
    const nomasjid_to_masjidsMsg = document.getElementById('nomasjid_to_masjidsMsg');

    if (nomasjid_to_masjidsMsg) {
      nomasjid_to_masjidsMsg.style.display = visibleCards.length === 0 ? 'block' : 'none';
    }
  });
});

const searchInput = document.getElementById("masjid_to_masjidSearch");

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
    const nomasjid_to_masjidsMsg = document.getElementById("nomasjid_to_masjidsMsg");
    if (nomasjid_to_masjidsMsg) {
      nomasjid_to_masjidsMsg.style.display = visibleCount === 0 ? "block" : "none";
    }

    // إزالة تفعيل الفلاتر إذا تم البحث
    if (keyword !== "") {
      document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    }
  });
}

</script>