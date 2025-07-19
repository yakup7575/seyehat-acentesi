<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header">
    <div class="container">
        <div class="header-main">
            <!-- Logo -->
            <div class="site-logo">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                        <?php bloginfo( 'name' ); ?>
                    </a></h1>
                <?php endif; ?>
            </div>

            <!-- Main Navigation -->
            <nav id="site-navigation" class="main-navigation">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => function() {
                        echo '<ul id="primary-menu">';
                        echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . __( 'Ana Sayfa', 'seyahat-theme' ) . '</a></li>';
                        echo '<li><a href="#">' . __( 'Deneyimler', 'seyahat-theme' ) . '</a></li>';
                        echo '<li><a href="#">' . __( 'Destinasyonlar', 'seyahat-theme' ) . '</a></li>';
                        echo '<li><a href="#">' . __( 'Blog', 'seyahat-theme' ) . '</a></li>';
                        echo '<li><a href="#">' . __( 'İletişim', 'seyahat-theme' ) . '</a></li>';
                        echo '</ul>';
                    }
                ) );
                ?>
            </nav>

            <!-- User Actions -->
            <div class="header-actions">
                <div class="user-menu">
                    <?php if ( is_user_logged_in() ) : ?>
                        <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt"></i> <?php _e( 'Çıkış', 'seyahat-theme' ); ?>
                        </a>
                        <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="btn btn-primary">
                            <i class="fas fa-user"></i> <?php _e( 'Hesabım', 'seyahat-theme' ); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="btn btn-secondary">
                            <i class="fas fa-sign-in-alt"></i> <?php _e( 'Giriş', 'seyahat-theme' ); ?>
                        </a>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> <?php _e( 'Üye Ol', 'seyahat-theme' ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Cart -->
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <div class="header-cart">
                        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-label="<?php _e( 'Menüyü aç', 'seyahat-theme' ); ?>">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Navigation Overlay -->
<div class="mobile-nav-overlay">
    <div class="mobile-nav-content">
        <button class="mobile-nav-close">
            <i class="fas fa-times"></i>
        </button>
        
        <?php
        wp_nav_menu( array(
            'theme_location' => 'mobile',
            'menu_id'        => 'mobile-menu',
            'container'      => 'nav',
            'container_class' => 'mobile-navigation',
            'fallback_cb'    => function() {
                echo '<nav class="mobile-navigation">';
                echo '<ul id="mobile-menu">';
                echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . __( 'Ana Sayfa', 'seyahat-theme' ) . '</a></li>';
                echo '<li><a href="#">' . __( 'Deneyimler', 'seyahat-theme' ) . '</a></li>';
                echo '<li><a href="#">' . __( 'Destinasyonlar', 'seyahat-theme' ) . '</a></li>';
                echo '<li><a href="#">' . __( 'Blog', 'seyahat-theme' ) . '</a></li>';
                echo '<li><a href="#">' . __( 'İletişim', 'seyahat-theme' ) . '</a></li>';
                echo '</ul>';
                echo '</nav>';
            }
        ) );
        ?>
    </div>
</div>

<div id="page" class="site">
    <div id="content" class="site-content">
