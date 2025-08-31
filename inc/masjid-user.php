<?php
// ******************************* Create masjid Role ********************************* //
function create_masjid_role() {
    // إزالة الدور القديم إذا كان موجودًا
    if (get_role('masjid')) {
        remove_role('masjid');
    }

    // إنشاء الدور الجديد بصلاحيات تسمح بإضافة دروس فقط
    add_role('masjid', 'Masjid', [
        'read' => true,
        'edit_posts' => true, // ✅ ضروري للسماح بإنشاء الدروس
        'publish_posts' => true,
        'edit_published_posts' => true,
        'upload_files' => true, // للسماح برفع الصور
    ]);
}
add_action('init', 'create_masjid_role');
function hide_admin_bar_for_masjid_users() {
    if (current_user_can('masjid')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'hide_admin_bar_for_masjid_users');
add_action('admin_menu', function () {
    if (current_user_can('masjid')) {
        remove_menu_page('edit.php'); // يخفي مقالات ووردبريس العادية
    }
});
