<?php
/*
Template Name: Partner Dashboard
*/

get_header();

// Check if user is logged in and has partner capabilities
if (!is_user_logged_in() || !current_user_can('edit_posts')) {
    ?>
    <div class="container">
        <div class="access-denied" style="background: white; padding: 2rem; border-radius: 10px; text-align: center; margin: 2rem 0;">
            <h2>Erişim Engellendi</h2>
            <p>Bu sayfaya erişim yetkiniz bulunmuyor.</p>
            <a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn">Giriş Yap</a>
        </div>
    </div>
    <?php
    get_footer();
    return;
}
?>

<div class="container">
    <div class="partner-dashboard" style="padding: 2rem 0;">
        <?php echo do_shortcode('[partner_dashboard]'); ?>
    </div>
</div>

<?php get_footer(); ?>