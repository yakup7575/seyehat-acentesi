<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="site-branding">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
                            <?php bloginfo('name'); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id' => 'primary-menu',
                        'fallback_cb' => false,
                    ));
                    ?>
                    
                    <div class="header-actions">
                        <?php if (is_user_logged_in()) : ?>
                            <?php $current_user = wp_get_current_user(); ?>
                            <div class="user-menu">
                                <span>Merhaba, <?php echo esc_html($current_user->display_name); ?></span>
                                <?php if (current_user_can('edit_posts')) : ?>
                                    <a href="<?php echo admin_url('edit.php?post_type=tour'); ?>" class="btn btn-secondary">Turlarım</a>
                                <?php endif; ?>
                                <?php if (current_user_can('manage_options')) : ?>
                                    <a href="<?php echo admin_url(); ?>" class="btn">Admin Panel</a>
                                <?php endif; ?>
                                <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn btn-secondary">Çıkış</a>
                            </div>
                        <?php else : ?>
                            <div class="auth-links">
                                <a href="<?php echo wp_login_url(); ?>" class="btn btn-secondary">Giriş</a>
                                <a href="<?php echo wp_registration_url(); ?>" class="btn">Kayıt Ol</a>
                                <a href="<?php echo home_url('/partner-kayit'); ?>" class="btn cta-button">Partner Ol</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <div id="content" class="site-content">