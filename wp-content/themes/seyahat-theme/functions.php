<?php
/**
 * Theme Name: Seyahat Acentesi Theme
 * Description: GetYourGuide benzeri tam kapsamlı seyahat pazaryeri platformu teması
 * Author: Seyahat Acentesi Team
 * Version: 1.0.0
 * License: GPL v2 or later
 * Text Domain: seyahat-theme
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Theme version
define( 'SEYAHAT_THEME_VERSION', '1.0.0' );

// Theme directory paths
define( 'SEYAHAT_THEME_DIR', get_template_directory() );
define( 'SEYAHAT_THEME_URI', get_template_directory_uri() );

/**
 * Theme setup
 */
function seyahat_theme_setup() {
    // Add theme support for various features
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'custom-background' );
    add_theme_support( 'custom-header' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
    
    // WooCommerce support
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    
    // Register navigation menus
    register_nav_menus( array(
        'primary'   => __( 'Ana Menü', 'seyahat-theme' ),
        'footer'    => __( 'Alt Menü', 'seyahat-theme' ),
        'mobile'    => __( 'Mobil Menü', 'seyahat-theme' ),
        'vendor'    => __( 'Satıcı Menüsü', 'seyahat-theme' ),
    ) );
    
    // Set content width
    if ( ! isset( $content_width ) ) {
        $content_width = 1200;
    }
    
    // Load text domain for translations
    load_theme_textdomain( 'seyahat-theme', SEYAHAT_THEME_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'seyahat_theme_setup' );

/**
 * Enqueue scripts and styles
 */
function seyahat_theme_scripts() {
    // Main stylesheet
    wp_enqueue_style( 'seyahat-style', get_stylesheet_uri(), array(), SEYAHAT_THEME_VERSION );
    
    // Bootstrap CSS
    wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0' );
    
    // Font Awesome
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );
    
    // jQuery (already included in WordPress)
    wp_enqueue_script( 'jquery' );
    
    // Bootstrap JS
    wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), '5.3.0', true );
    
    // Custom scripts
    wp_enqueue_script( 'seyahat-scripts', SEYAHAT_THEME_URI . '/assets/js/scripts.js', array( 'jquery', 'bootstrap' ), SEYAHAT_THEME_VERSION, true );
    
    // Localize script for AJAX
    wp_localize_script( 'seyahat-scripts', 'seyahat_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'seyahat_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'seyahat_theme_scripts' );

/**
 * Register widget areas
 */
function seyahat_theme_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Ana Kenar Çubuğu', 'seyahat-theme' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Ana kenar çubuğu widget alanı', 'seyahat-theme' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    
    register_sidebar( array(
        'name'          => __( 'Alt Çubuk', 'seyahat-theme' ),
        'id'            => 'footer-1',
        'description'   => __( 'Alt çubuk widget alanı', 'seyahat-theme' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 'seyahat_theme_widgets_init' );
