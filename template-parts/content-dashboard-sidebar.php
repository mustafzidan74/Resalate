<!-- Sidebar Toggle -->
<div class="toggler">
<button
  id="menuToggle"
  class="focus:outline-none md:hidden flex justify-center items-center rounded-lg"
>
  <!-- Right Arrow Icon -->
  Menu
  <svg
    class="w-5 h-5"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    viewBox="0 0 24 24"
  >
    <path
      stroke-linecap="round"
      stroke-linejoin="round"
      d="M9 5l7 7-7 7"
    />
  </svg>
</button>
</div>

<aside id="sidebar" class="w-64 md:rounded-lg bg-white shadow-lg z-50 fixed bottom-[-100%] left-0 md:sticky md:top-[100px] md:h-auto h-screen transform transition-transform duration-300 md:translate-x-0 -translate-x-full -translate-y-full md:translate-y-0">
    <div class="flex items-center justify-between p-4 border-b">
        <h2 class="text-xl font-bold">Menu</h2>
        <button id="closeSidebar" class="hover:text-red-500 transition md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <nav class="p-4 flex flex-col space-y-6">
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('masjid-dashboard'))); ?>" class="font-[600] sm:text-[1.1rem] text-[1.2rem] transition-colors flex items-center gap-2 <?php if (is_page_template('template-masjid-dashboard.php')) echo 'active'; ?>">
            <i class="fa-solid fa-address-card"></i>
            Account Information
        </a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('masjid-lessons'))); ?>" class="font-[600] sm:text-[1.1rem] text-[1.2rem] transition-colors flex items-center gap-2 <?php if (is_page_template('template-masjid-lessons.php')) echo 'active'; ?>">
            <i class="fa-solid fa-pen"></i>
            Lessons
        </a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('masjid-funerals'))); ?>" class="font-[600] sm:text-[1.1rem] text-[1.2rem] transition-colors flex items-center gap-2 <?php if (is_page_template('template-masjid-funerals.php')) echo 'active'; ?>">
            <i class="fa-solid fa-person-praying"></i>
            Funerals
        </a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('masjid-live-feed'))); ?>" class="font-[600] sm:text-[1.1rem] text-[1.2rem] transition-colors flex items-center gap-2 <?php if (is_page_template('template-masjid-live-feed.php')) echo 'active'; ?>">
            <i class="fa-solid fa-video"></i>
            Live Feed
        </a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('masjid-from-masjid-to-masjid'))); ?>" class="font-[600] sm:text-[1.1rem] text-[1.2rem] transition-colors flex items-center gap-2 <?php if (is_page_template('template-masjid-from-masjid-to-masjid.php')) echo 'active'; ?>">
            <i class="fa-solid fa-arrows-left-right"></i>
            From masjid to masjid
        </a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('masjid-donations'))); ?>" class="font-[600] sm:text-[1.1rem] text-[1.2rem] transition-colors flex items-center gap-2 <?php if (is_page_template('template-masjid-donations.php')) echo 'active'; ?>">
            <i class="fa-solid fa-hand-holding-dollar"></i>
            Donations
        </a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('masjid-followers'))); ?>" class="font-[600] sm:text-[1.1rem] text-[1.2rem] transition-colors flex items-center gap-2 <?php if (is_page_template('template-masjid-followers.php')) echo 'active'; ?>">
            <i class="fa-solid fa-users"></i>
            My Followers
        </a>
    </nav>
</aside>
