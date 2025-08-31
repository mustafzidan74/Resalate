document.addEventListener('DOMContentLoaded', function () {
  const followButton = document.getElementById('followButton');
  if (!followButton || typeof masjidFollowData === 'undefined') return;

  followButton.addEventListener('click', function () {
    const isFollowing = followButton.dataset.following === '1';
    const masjidId = followButton.dataset.masjid;

    followButton.disabled = true;

    fetch(masjidFollowData.ajax_url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        action: 'toggle_follow_masjid',
        masjid_id: masjidId,
        security: masjidFollowData.nonce,
      })
    })
    .then(async (response) => {
      const contentType = response.headers.get("content-type");
      if (!contentType || !contentType.includes("application/json")) {
        const text = await response.text();
        throw new Error("Non-JSON response:\n" + text);
      }

      return response.json();
    })
    .then(data => {
      if (data.success && data.data) {
        const followData = data.data;

        followButton.dataset.following = followData.is_following ? '1' : '0';
        followButton.classList.toggle('bg-red-600', followData.is_following);
        followButton.classList.toggle('text-white', followData.is_following);
        followButton.classList.toggle('primary-btn', !followData.is_following);

        const statusEl = followButton.querySelector('.status');
        if (statusEl) {
          statusEl.textContent = followData.is_following ? 'Unfollow' : 'Follow';
        }

        const iconEl = followButton.querySelector('.icon-symbol');
        if (iconEl) {
          iconEl.textContent = followData.is_following ? '−' : '+';
        }

        const countEl = followButton.querySelector('.number-of-follower');
        if (countEl) {
          countEl.textContent = `(${followData.total})`;
        }
      } else {
        alert(data.message || 'حدث خطأ أثناء المتابعة');
      }
    })
    .catch(error => {
      console.error('Follow error:', error);
      alert('حدث خطأ غير متوقع: ' + error.message);
    })
    .finally(() => {
      followButton.disabled = false;
    });
  });
});
