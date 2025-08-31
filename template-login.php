<?php
/**
 * Template Name: Login
 */

// Start output buffering to prevent headers already sent issues
ob_start();

get_header();

// Redirect if user is already logged in
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    ob_end_clean();
    
    if (in_array('administrator', $user->roles)) {
        wp_redirect(admin_url());
    } elseif (in_array('masjid', $user->roles)) {
        wp_redirect(home_url('/masjid-dashboard'));
    } elseif (in_array('subscriber', $user->roles)) {
        wp_redirect(home_url('/user-dashboard'));
    } else {
        wp_redirect(home_url('/'));
    }
    exit;
}

// Get helper page links
$register_page = get_pages([
    'meta_key' => '_wp_page_template',
    'meta_value' => 'template-register.php',
    'number' => 1
]);
$register_link = !empty($register_page) ? get_permalink($register_page[0]->ID) : wp_registration_url();

$forget_page = get_pages([
    'meta_key' => '_wp_page_template',
    'meta_value' => 'template-forget-password.php',
    'number' => 1
]);
$forget_link = !empty($forget_page) ? get_permalink($forget_page[0]->ID) : wp_lostpassword_url();
?>

<main class="my-20">
  <div class="container mx-auto px-4">
    <div class="form-box max-w-xl mx-auto bg-white shadow p-6 rounded">
      <h3 class="md:text-[28px] sm:text-[24px] text-[20px] font-[700] text-center mb-6">Login</h3>

      <div id="login-message" class="mb-4 hidden p-3 rounded text-center"></div>

      <form name="loginform" id="loginform" method="post" class="masjid-login-form">
        <div class="inputs-wrapper flex flex-col gap-5 mb-6">
          <div class="input-container">
            <label class="!sm:text-[16px] !text-[13px]" for="user_login">Email or Username</label>
            <input class="sm:text-[14px] text-[13px] w-full border px-4 py-2 rounded" id="user_login" type="text" name="log" required placeholder="Enter your email or username" />
          </div>

          <div class="input-container relative">
            <label class="!sm:text-[16px] !text-[13px]" for="user_pass">Password</label>
            <input class="sm:text-[14px] text-[13px] w-full border px-4 py-2 rounded pr-10" id="user_pass" type="password" name="pwd" required placeholder="Enter your password" />
            <span toggle="#user_pass" class="toggle-password absolute right-3 top-9 cursor-pointer text-gray-500">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>

        <div class="flex flex-col gap-3 mb-12">
          <div class="submit-btn flex items-center justify-between sm:flex-nowrap flex-wrap sm:gap-2 gap-6">
            <a href="<?php echo esc_url($forget_link); ?>" class="hover:underline font-[500]">Forgot Password?</a>
            <button class="submit sm:w-fit w-full !sm:text-[16px] !text-[14px] bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition" type="submit">
              Login
            </button>
          </div>
        </div>

        <div class="login-link text-center !sm:text-[16px] !text-[14px]">
          <span>New user?</span>
          <a class="text-green-700 hover:underline" href="<?php echo esc_url($register_link); ?>">Register now</a>
        </div>
      </form>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.querySelector('.toggle-password');
    const passwordField = document.querySelector('#user_pass');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // AJAX login form submission
    const loginForm = document.getElementById('loginform');
    const messageDiv = document.getElementById('login-message');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'masjid_login');
        
        messageDiv.classList.add('hidden');
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Logging in...';
        submitBtn.disabled = true;
        
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                messageDiv.className = 'mb-4 bg-green-100 text-green-700 p-3 rounded text-center';
                messageDiv.textContent = data.message;
                messageDiv.classList.remove('hidden');
                
                // Redirect after showing success message
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                messageDiv.className = 'mb-4 bg-red-100 text-red-700 p-3 rounded text-center';
                messageDiv.textContent = data.message;
                messageDiv.classList.remove('hidden');
            }
        })
        .catch(error => {
            messageDiv.className = 'mb-4 bg-red-100 text-red-700 p-3 rounded text-center';
            messageDiv.textContent = 'Connection error. Please check your internet and try again.';
            messageDiv.classList.remove('hidden');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>

<?php 
ob_end_flush();
get_footer(); 
?>