<?php
/**
 * Template Name: Forget Password
 */
get_header();
?>

<main class="my-20">
  <div class="container mx-auto px-4">
    <div class="form-box">

      <!-- Step 1: Email Input -->
      <div id="step-email">
        <h3 class="text-[20px] font-bold text-center">Reset your password</h3>
        <form id="reset-password-send-email">
          <div class="inputs-wrapper flex flex-col gap-5 mb-10">
            <div class="input-container">
              <label for="email-address">Email Address</label>
              <input id="email-address" type="email" name="email" placeholder="Enter your email address" required />
            </div>
          </div>
          <div class="submit-btn">
            <button class="submit w-full" type="submit">Next</button>
          </div>
          <p id="step-email-error" class="text-red-500 mt-3 text-sm text-center"></p>
        </form>
      </div>

      <!-- Step 2: Code Verification -->
      <div id="step-code" style="display:none;">
        <h3 class="text-[20px] font-bold text-center">Enter the code</h3>
        <p class="text-center">A <b>verification code</b> has been sent to your email.</p>
        <form id="reset-password-code-verification">
          <div class="inputs-wrapper flex flex-col gap-5 mb-10">
            <div class="input-container">
              <label>Verification Code</label>
              <div id="otp-inputs" class="flex gap-3 mt-2">
                <?php for ($i = 0; $i < 6; $i++): ?>
                  <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border rounded" <?= $i > 0 ? 'disabled' : '' ?> />
                <?php endfor; ?>
              </div>
            </div>
          </div>
          <div class="submit-btn">
            <button class="submit w-full" type="submit">Verify</button>
          </div>
          <p id="step-code-error" class="text-red-500 mt-3 text-sm text-center"></p>
        </form>
      </div>

      <!-- Step 3: New Password -->
      <div id="step-new-password" style="display:none;">
        <h3 class="text-[20px] font-bold text-center">Set a new password</h3>
        <form id="reset-password-final">
          <div class="inputs-wrapper flex flex-col gap-5 mb-10">
            <div class="input-container">
              <label for="new-password">New Password</label>
              <input id="new-password" type="password" name="password" placeholder="Password" required />
            </div>
            <div class="input-container">
              <label for="confirm-password">Confirm Password</label>
              <input id="confirm-password" type="password" name="confirm" placeholder="Confirm Password" required />
            </div>
          </div>
          <div class="submit-btn">
            <button class="submit w-full" type="submit">Save Password</button>
          </div>
          <p id="step-password-error" class="text-red-500 mt-3 text-sm text-center"></p>
        </form>
      </div>

    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const otpInputs = document.querySelectorAll('.otp-input');
  let currentEmail = '';

  otpInputs.forEach((input, index) => {
    input.addEventListener('input', () => {
      if (input.value.length === 1 && index < otpInputs.length - 1) {
        otpInputs[index + 1].disabled = false;
        otpInputs[index + 1].focus();
      }
    });
  });

  // Step 1: Send OTP
  document.getElementById('reset-password-send-email').addEventListener('submit', function (e) {
    e.preventDefault();
    const email = document.getElementById('email-address').value.trim();
    currentEmail = email;
    const errorField = document.getElementById('step-email-error');
    errorField.textContent = '';

    fetch(resetPassAjax.ajax_url, {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({ action: 'send_reset_otp', email })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById('step-email').style.display = 'none';
        document.getElementById('step-code').style.display = 'block';
      } else {
        errorField.textContent = data.data;
      }
    });
  });

  // Step 2: Verify OTP
  document.getElementById('reset-password-code-verification').addEventListener('submit', function (e) {
    e.preventDefault();
    const otp = [...document.querySelectorAll('.otp-input')].map(input => input.value).join('');
    const errorField = document.getElementById('step-code-error');
    errorField.textContent = '';

    fetch(resetPassAjax.ajax_url, {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({ action: 'verify_reset_otp', email: currentEmail, otp })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById('step-code').style.display = 'none';
        document.getElementById('step-new-password').style.display = 'block';
      } else {
        errorField.textContent = data.data;
      }
    });
  });

  // Step 3: Save new password
  document.getElementById('reset-password-final').addEventListener('submit', function (e) {
    e.preventDefault();
    const password = document.getElementById('new-password').value;
    const confirm = document.getElementById('confirm-password').value;
    const errorField = document.getElementById('step-password-error');
    errorField.textContent = '';

    fetch(resetPassAjax.ajax_url, {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({
        action: 'save_new_password',
        email: currentEmail,
        password: password,
        confirm: confirm
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Password updated successfully! Redirecting to login page...');
        window.location.href = '/login';
      } else {
        errorField.textContent = data.data;
      }
    });
  });
});
</script>

<?php get_footer(); ?>
