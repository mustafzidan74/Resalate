<?php
/**
 * Template Name: Masjids
 */

get_header();

// قراءة الفلاتر من الرابط
$selected_country = $_GET['country'] ?? '';
$selected_province = $_GET['province'] ?? '';
$selected_city = $_GET['city'] ?? '';

// استعلام كل المساجد
$masjids = get_users([
    'role' => 'masjid',
    'number' => -1,
]);
?>

<main class="my-20">
  <div class="container mx-auto px-4 flex flex-col gap-6">
    
    <!-- العنوان -->
    <div class="head-content w-full mb-2">
      <h2 class="sm:text-xl text-lg font-[900]">All Masjids</h2>
    </div>

    <!-- الفلاتر -->
    <form method="GET" id="masjid-filter-form" class="grid grid-cols-1 sm:grid-cols-4 gap-3 w-full">
      <select id="country" name="country" class="select2-filter py-3 px-4 bg-white rounded-lg font-bold">
        <option value="">Select Country</option>
      </select>

      <select id="province" name="province" class="select2-filter py-3 px-4 bg-white rounded-lg font-bold" disabled>
        <option value="">Select Province</option>
      </select>

      <select id="city" name="city" class="select2-filter py-3 px-4 bg-white rounded-lg font-bold" disabled>
        <option value="">Select City</option>
      </select>

      <button type="submit" class="py-3 px-4 bg-blue-600 text-white rounded hover:bg-blue-700 w-full sm:w-auto">
        Filter
      </button>
    </form>

    <!-- قائمة المساجد -->
<div class="content-wrapper grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
  <?php
    $filtered_masjids = [];

    foreach ($masjids as $masjid) {
      $user_id = $masjid->ID;
      $country = get_user_meta($user_id, 'country', true);
      $province = get_user_meta($user_id, 'province', true);
      $city = get_user_meta($user_id, 'city', true);

      if (
        ($selected_country && $selected_country !== $country) ||
        ($selected_province && $selected_province !== $province) ||
        ($selected_city && $selected_city !== $city)
      ) {
        continue;
      }

      $filtered_masjids[] = $masjid;
    }

    if (empty($filtered_masjids)) :
  ?>
    <div class="col-span-full text-center py-10 text-lg font-semibold text-gray-600">
      No masjids found for the selected location.
    </div>
  <?php else: ?>
    <?php foreach ($filtered_masjids as $masjid): ?>
      <?php
        $user_id = $masjid->ID;
        $name = $masjid->display_name;
        $email = $masjid->user_email;
        $photo = get_field('masjid_photo', 'user_' . $user_id);
        $cover = get_field('masjid_cover', 'user_' . $user_id);
        $description = get_field('masjid_description', 'user_' . $user_id);
        $phone = get_field('phone', 'user_' . $user_id);
        $languages = get_field('languages', 'user_' . $user_id) ?: [];
        $profile_url = get_author_posts_url($user_id);
      ?>
      <div class="card-box rounded-lg overflow-hidden shadow-md">
        <div class="img-box">
          <img 
            src="<?= esc_url($cover['url'] ?? 'https://placehold.co/600x400?text=Mosque') ?>" 
            alt="<?= esc_attr($name) ?>" 
            class="w-full h-[200px] object-cover"
          />
        </div>
        <div class="text-box px-4 py-5 bg-white flex flex-col">
          <h3 class="title text-lg mb-2 font-[600]"><?= esc_html($name) ?></h3>

          <?php if (!empty($description)): ?>
            <p class="desc line-clamp-3"><?= wp_trim_words(wp_kses_post($description), 30, '...') ?></p>
          <?php endif; ?>

          <div class="flex flex-col gap-3 my-4">
            <?php if (!empty($email)): ?>
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-envelope sm:text-xl text-lg"></i>
                <div class="font-[600]"><?= esc_html($email) ?></div>
              </div>
            <?php endif; ?>

            <?php if (!empty($phone)): ?>
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-phone sm:text-xl text-lg"></i>
                <div class="font-[600]"><?= esc_html($phone) ?></div>
              </div>
            <?php endif; ?>

            <?php if (!empty($languages)): ?>
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-language sm:text-xl text-lg"></i>
                <div class="font-[600]">[<?= implode(', ', array_column($languages, 'title')) ?>]</div>
              </div>
            <?php endif; ?>
          </div>

          <a href="<?= esc_url($profile_url) ?>" class="self-end mt-4 text-sm font-bold text-blue-600 hover:underline">
            Show Profile
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
  </div>
</main>

<!-- ✅ JavaScript: تفعيل Select2 وربط المدن بالمحافظات والدول -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const countrySelect = jQuery('#country');
  const provinceSelect = jQuery('#province');
  const citySelect = jQuery('#city');

  // تفعيل select2
  jQuery('.select2-filter').select2({ width: '100%' });

  // تحميل ملف JSON
  fetch("<?= get_stylesheet_directory_uri(); ?>/assets/data/countries-states-cities.json")
    .then(res => res.json())
    .then(data => {
      // تعبئة الدول
      data.forEach(country => {
        const option = new Option(country.name, country.name, false, country.name === "<?= esc_js($selected_country) ?>");
        option.dataset.id = country.id;
        countrySelect.append(option);
      });

      // إذا كانت الدولة مختارة مسبقًا → فعل المحافظات
      const selectedCountry = countrySelect.val();
      if (selectedCountry) {
        const country = data.find(c => c.name === selectedCountry);
        provinceSelect.prop('disabled', false);
        provinceSelect.find('option:gt(0)').remove();

        if (country?.states) {
          country.states.forEach(state => {
            const option = new Option(state.name, state.name, false, state.name === "<?= esc_js($selected_province) ?>");
            option.dataset.id = state.id;
            provinceSelect.append(option);
          });
        }
      }

      // إذا كانت المحافظة مختارة → فعل المدن
      const selectedProvince = provinceSelect.val();
      if (selectedProvince && selectedCountry) {
        const country = data.find(c => c.name === selectedCountry);
        const state = country?.states?.find(s => s.name === selectedProvince);
        citySelect.prop('disabled', false);
        citySelect.find('option:gt(0)').remove();

        if (state?.cities) {
          state.cities.forEach(city => {
            const option = new Option(city.name, city.name, false, city.name === "<?= esc_js($selected_city) ?>");
            citySelect.append(option);
          });
        }
      }

      // عند تغيير الدولة
      countrySelect.on('change', function () {
        const countryName = jQuery(this).val();
        const country = data.find(c => c.name === countryName);

        provinceSelect.prop('disabled', false).empty().append(new Option("Select Province", ""));
        citySelect.prop('disabled', true).empty().append(new Option("Select City", ""));

        if (country?.states) {
          country.states.forEach(state => {
            const option = new Option(state.name, state.name);
            option.dataset.id = state.id;
            provinceSelect.append(option);
          });
        }

        provinceSelect.trigger('change.select2');
        citySelect.trigger('change.select2');
      });

      // عند تغيير المحافظة
      provinceSelect.on('change', function () {
        const countryName = countrySelect.val();
        const provinceName = jQuery(this).val();

        const country = data.find(c => c.name === countryName);
        const state = country?.states?.find(s => s.name === provinceName);

        citySelect.prop('disabled', false).empty().append(new Option("Select City", ""));

        if (state?.cities) {
          state.cities.forEach(city => {
            citySelect.append(new Option(city.name, city.name));
          });
        }

        citySelect.trigger('change.select2');
      });
    })
    .catch(error => console.error("Location JSON Load Error:", error));
});
</script>

<?php get_footer(); ?>
