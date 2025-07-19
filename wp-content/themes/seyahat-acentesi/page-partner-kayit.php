<?php
/*
Template Name: Partner Kayıt
*/

get_header();
?>

<div class="container">
    <div class="page-content" style="padding: 2rem 0;">
        <h1 style="text-align: center; color: #2c5aa0; margin-bottom: 2rem;">Seyahat Acentesi Partner Kayıt</h1>
        
        <?php echo do_shortcode('[partner_registration_form]'); ?>
    </div>
</div>

<?php get_footer(); ?>