<?php
/**
 * Template Name: Register
 * Description: Unified registration page for Users and Masjids with enhanced error handling
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    if (in_array('administrator', $user->roles)) {
        wp_redirect(admin_url()); exit;
    } elseif (in_array('masjid', $user->roles)) {
        wp_redirect(home_url('/masjid-dashboard')); exit;
    } elseif (in_array('subscriber', $user->roles)) {
        wp_redirect(home_url('/user-dashboard')); exit;
    } else {
        wp_redirect(home_url('/')); exit;
    }
}

// Get login page link
$login_page = get_pages([
    'meta_key' => '_wp_page_template',
    'meta_value' => 'template-login.php',
    'number' => 1
]);
$login_link = !empty($login_page) ? get_permalink($login_page[0]->ID) : wp_login_url();

get_header();
?>
<main class="my-20">
  <div class="container mx-auto px-4">
    <div class="max-w-[600px] mx-auto bg-white py-8 px-4 rounded-lg shadow-md">
      <!-- Toggle Buttons -->
      <div class="flex justify-center mb-6 gap-4">
        <button id="userRegisterBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Signup as User</button>
        <button id="masjidRegisterBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Signup as Masjid</button>
      </div>

      <div class="relative w-full overflow-hidden rounded-lg p-6 min-h-[380px]">
        <!-- Message Output -->
        <div id="register-message" class="mb-4 text-sm text-center"></div>

        <!-- User Form -->
        <form id="userForm" class="transition-all duration-500 ease-in-out flex flex-col justify-between">
          <div>
            <h2 class="text-xl font-bold mb-4 text-blue-600">Signup as user</h2>
            <input type="text" name="name" placeholder="Full Name" class="w-full mb-3 px-4 py-2 border rounded text-sm" required />
            <input type="email" name="email" placeholder="Email" class="w-full mb-3 px-4 py-2 border rounded text-sm" required />
            <input type="text" name="phone" placeholder="Phone Number" class="w-full mb-3 px-4 py-2 border rounded text-sm" required />
            <input type="password" name="password" placeholder="Password (min 6 characters)" class="w-full mb-3 px-4 py-2 border rounded text-sm" required minlength="6" />
            <input type="password" name="confirm_password" placeholder="Confirm Password" class="w-full mb-4 px-4 py-2 border rounded text-sm" required />
            <a class="text-blue-600 hover:underline text-sm" href="<?= esc_url($login_link); ?>">Already have an account?</a>
          </div>
          <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Signup as User</button>
        </form>

        <!-- Masjid Form -->
        <form id="masjidForm" class="hidden transition-all duration-500 ease-in-out flex flex-col justify-between">
          <div>
            <h2 class="text-xl font-bold mb-4 text-green-600">Signup as Masjid</h2>
            <input type="text" name="masjid_name" placeholder="Masjid Name" class="w-full mb-3 px-4 py-2 border rounded text-sm" required />
            <select id="country" name="country" class="w-full mb-3 px-4 py-2 border rounded text-sm" required>
              <option value="">Select Country</option>
            </select>
            <select id="state" name="province" class="w-full mb-3 px-4 py-2 border rounded text-sm" required disabled>
              <option value="">Select Province / State</option>
            </select>
            <select id="city" name="city" class="w-full mb-3 px-4 py-2 border rounded text-sm" required disabled>
              <option value="">Select City</option>
            </select>
            <input type="email" name="email" placeholder="Email" class="w-full mb-3 px-4 py-2 border rounded text-sm" required />
            <input type="text" name="phone" placeholder="Phone Number" class="w-full mb-3 px-4 py-2 border rounded text-sm" required />
            <input type="password" name="password" placeholder="Password (min 6 characters)" class="w-full mb-3 px-4 py-2 border rounded text-sm" required minlength="6" />
            <input type="password" name="confirm_password" placeholder="Confirm Password" class="w-full mb-4 px-4 py-2 border rounded text-sm" required />
            <label class="text-sm mb-3 inline-block">
              <input type="checkbox" required> I accept the <a href="/terms" class="underline text-green-600">terms</a>
            </label>
          </div>
          <button type="submit" class="w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Signup as Masjid</button>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const userBtn = document.getElementById("userRegisterBtn");
  const masjidBtn = document.getElementById("masjidRegisterBtn");
  const userForm = document.getElementById("userForm");
  const masjidForm = document.getElementById("masjidForm");
  const messageBox = document.getElementById("register-message");

  // Toggle between forms
  userBtn.addEventListener("click", () => {
    userForm.classList.remove("hidden");
    masjidForm.classList.add("hidden");
    userBtn.classList.add("bg-blue-700");
    userBtn.classList.remove("bg-blue-600");
    masjidBtn.classList.add("bg-green-600");
    masjidBtn.classList.remove("bg-green-700");
    messageBox.innerHTML = "";
  });

  masjidBtn.addEventListener("click", () => {
    masjidForm.classList.remove("hidden");
    userForm.classList.add("hidden");
    masjidBtn.classList.add("bg-green-700");
    masjidBtn.classList.remove("bg-green-600");
    userBtn.classList.add("bg-blue-600");
    userBtn.classList.remove("bg-blue-700");
    messageBox.innerHTML = "";
  });

  // Handle form submissions
  const handleSubmit = (form, type) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(form);
      formData.append("action", type === "subscriber" ? "register_user_account" : "register_masjid_account");
      formData.append("user_type", type);
      formData.append("security", "<?php echo wp_create_nonce('register_nonce'); ?>");

      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<span class="inline-block animate-spin">↻</span> Processing...';
      submitBtn.disabled = true;

      messageBox.innerHTML = '';
      messageBox.className = 'mb-4 text-sm text-center';

      fetch("<?= admin_url('admin-ajax.php'); ?>", {
        method: "POST",
        body: formData,
      })
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
      })
      .then(data => {
        if (data.success) {
          messageBox.className = "mb-4 text-sm text-center text-green-600";
          messageBox.innerHTML = "Registration successful! Redirecting to your dashboard...";
          setTimeout(() => {
            if (data.redirect) {
              window.location.href = data.redirect;
            } else {
              window.location.href = type === "subscriber" ? "<?= home_url('/user-dashboard'); ?>" : "<?= home_url('/masjid-dashboard'); ?>";
            }
          }, 1500);
        } else {
          messageBox.className = "mb-4 text-sm text-center text-red-600";
          if (data.errors) {
            let errorHtml = '<ul class="text-left">';
            for (const [field, message] of Object.entries(data.errors)) {
              errorHtml += `<li>${message}</li>`;
            }
            errorHtml += '</ul>';
            messageBox.innerHTML = errorHtml;
          } else if (data.data?.errors) {
            let errorHtml = '<ul class="text-left">';
            for (const [field, message] of Object.entries(data.data.errors)) {
              errorHtml += `<li>${message}</li>`;
            }
            errorHtml += '</ul>';
            messageBox.innerHTML = errorHtml;
          } else {
            messageBox.innerHTML = data.message || "Registration failed. Please check your information and try again.";
          }
        }
      })
      .catch(error => {
        messageBox.className = "mb-4 text-sm text-center text-red-600";
        messageBox.innerHTML = "Network error. Please check your connection and try again.";
      })
      .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      });
    });
  };

  handleSubmit(userForm, "subscriber");
  handleSubmit(masjidForm, "masjid");

  // Select2 Initialization Helper
  function initSelect2() {
    jQuery('#country').select2({ placeholder: "Select Country", width: '100%' });
    jQuery('#state').select2({ placeholder: "Select Province / State", width: '100%' });
    jQuery('#city').select2({ placeholder: "Select City", width: '100%' });
  }
  function refreshSelect2(id) {
    jQuery(`#${id}`).select2('destroy').select2({ width: '100%' });
  }

  // Country-State-City dropdowns
// Country-State-City dropdowns
fetch("<?= get_stylesheet_directory_uri(); ?>/assets/data/countries-states-cities.json")
  .then((res) => res.json())
  .then((data) => {
    const countrySelect = document.getElementById("country");
    const stateSelect = document.getElementById("state");
    const citySelect = document.getElementById("city");

    // Clear existing options
    countrySelect.innerHTML = '<option value="">Select Country</option>';
    stateSelect.innerHTML = '<option value="">Select Province / State</option>';
    citySelect.innerHTML = '<option value="">Select City</option>';

    // Populate countries
    data.forEach((country) => {
      const option = document.createElement("option");
      option.value = country.name;
      option.dataset.id = country.id;
      option.textContent = country.name;
      countrySelect.appendChild(option);
    });

    // Initialize Select2
    initSelect2();

    // Country change event
    jQuery('#country').on('change', function() {
      const countryId = this.options[this.selectedIndex].dataset.id;
      const country = data.find(c => c.id == countryId);
      
      // Reset state and city dropdowns
      stateSelect.innerHTML = '<option value="">Select Province / State</option>';
      stateSelect.disabled = false;
      citySelect.innerHTML = '<option value="">Select City</option>';
      citySelect.disabled = true;

      if (country && country.states) {
        country.states.forEach(state => {
          const option = document.createElement("option");
          option.value = state.name;
          option.dataset.id = state.id;
          option.textContent = state.name;
          stateSelect.appendChild(option);
        });

        // Reinitialize Select2 for state dropdown
        jQuery('#state').select2('destroy').select2({ 
          placeholder: "Select Province / State", 
          width: '100%' 
        });
      }
      
      // Reinitialize Select2 for city dropdown
      jQuery('#city').select2('destroy').select2({ 
        placeholder: "Select City", 
        width: '100%' 
      });
    });

    // State change event
    jQuery('#state').on('change', function() {
      const countryId = jQuery('#country option:selected').data('id');
      const stateId = this.options[this.selectedIndex].dataset.id;
      const country = data.find(c => c.id == countryId);
      
      // Reset city dropdown
      citySelect.innerHTML = '<option value="">Select City</option>';
      citySelect.disabled = false;

      if (country?.states) {
        const state = country.states.find(s => s.id == stateId);
        if (state?.cities) {
          state.cities.forEach(city => {
            const option = document.createElement("option");
            option.value = city.name;
            option.textContent = city.name;
            citySelect.appendChild(option);
          });
        }
      }

      // Reinitialize Select2 for city dropdown
      jQuery('#city').select2('destroy').select2({ 
        placeholder: "Select City", 
        width: '100%' 
      });
    });
  })
  .catch(error => {
    console.error("Error loading location data:", error);
  });});
</script>
<?php get_footer(); ?>