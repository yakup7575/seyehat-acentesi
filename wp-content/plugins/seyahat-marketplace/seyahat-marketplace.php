<?php
/**
 * Plugin Name: Seyahat Marketplace
 * Plugin URI: https://seyahatacentesi.com
 * Description: Comprehensive travel marketplace plugin for multi-vendor travel experiences platform
 * Version: 1.0.0
 * Author: Seyahat Acentesi Team
 * License: GPL v2 or later
 * Text Domain: seyahat-marketplace
 * Domain Path: /languages
 * 
 * This plugin provides core marketplace functionality including:
 * - Multi-vendor system
 * - Tour/Activity management
 * - Advanced booking system
 * - Commission management
 * - Vendor dashboard
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants
define( 'SEYAHAT_MARKETPLACE_VERSION', '1.0.0' );
define( 'SEYAHAT_MARKETPLACE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SEYAHAT_MARKETPLACE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SEYAHAT_MARKETPLACE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Seyahat Marketplace Class
 */
class Seyahat_Marketplace {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Check if WooCommerce is active
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
            return;
        }
        
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Core includes
        require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'includes/class-vendor.php';
        require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'includes/class-tour.php';
        require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'includes/class-booking.php';
        require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'includes/class-commission.php';
        require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'includes/class-dashboard.php';
        require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'includes/class-ajax.php';
        require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'includes/class-api.php';
        
        // Admin includes
        if ( is_admin() ) {
            require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'admin/class-admin.php';
            require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'admin/class-settings.php';
        }
        
        // Frontend includes
        if ( ! is_admin() ) {
            require_once SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'public/class-frontend.php';
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Register post types
        add_action( 'init', array( $this, 'register_post_types' ) );
        
        // Register taxonomies
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        
        // Add custom user roles
        add_action( 'init', array( $this, 'add_user_roles' ) );
        
        // Enqueue scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        
        // Add custom endpoints
        add_action( 'init', array( $this, 'add_rewrite_endpoints' ) );
        
        // Initialize classes
        new Seyahat_Vendor();
        new Seyahat_Tour();
        new Seyahat_Booking();
        new Seyahat_Commission();
        new Seyahat_Dashboard();
        new Seyahat_Ajax();
        new Seyahat_API();
        
        if ( is_admin() ) {
            new Seyahat_Admin();
            new Seyahat_Settings();
        } else {
            new Seyahat_Frontend();
        }
    }
    
    /**
     * Register custom post types
     */
    public function register_post_types() {
        // Tour/Experience post type
        register_post_type( 'seyahat_tour', array(
            'labels' => array(
                'name'               => __( 'Turlar', 'seyahat-marketplace' ),
                'singular_name'      => __( 'Tur', 'seyahat-marketplace' ),
                'menu_name'          => __( 'Turlar', 'seyahat-marketplace' ),
                'add_new'            => __( 'Yeni Tur', 'seyahat-marketplace' ),
                'add_new_item'       => __( 'Yeni Tur Ekle', 'seyahat-marketplace' ),
                'edit_item'          => __( 'Turu Düzenle', 'seyahat-marketplace' ),
                'new_item'           => __( 'Yeni Tur', 'seyahat-marketplace' ),
                'view_item'          => __( 'Turu Görüntüle', 'seyahat-marketplace' ),
                'search_items'       => __( 'Tur Ara', 'seyahat-marketplace' ),
                'not_found'          => __( 'Tur bulunamadı', 'seyahat-marketplace' ),
                'not_found_in_trash' => __( 'Çöp kutusunda tur bulunamadı', 'seyahat-marketplace' ),
            ),
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'tour' ),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-location-alt',
            'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments' ),
            'show_in_rest'        => true,
        ) );
        
        // Vendor post type
        register_post_type( 'seyahat_vendor', array(
            'labels' => array(
                'name'               => __( 'Satıcılar', 'seyahat-marketplace' ),
                'singular_name'      => __( 'Satıcı', 'seyahat-marketplace' ),
                'menu_name'          => __( 'Satıcılar', 'seyahat-marketplace' ),
                'add_new'            => __( 'Yeni Satıcı', 'seyahat-marketplace' ),
                'add_new_item'       => __( 'Yeni Satıcı Ekle', 'seyahat-marketplace' ),
                'edit_item'          => __( 'Satıcıyı Düzenle', 'seyahat-marketplace' ),
                'new_item'           => __( 'Yeni Satıcı', 'seyahat-marketplace' ),
                'view_item'          => __( 'Satıcıyı Görüntüle', 'seyahat-marketplace' ),
                'search_items'       => __( 'Satıcı Ara', 'seyahat-marketplace' ),
                'not_found'          => __( 'Satıcı bulunamadı', 'seyahat-marketplace' ),
                'not_found_in_trash' => __( 'Çöp kutusunda satıcı bulunamadı', 'seyahat-marketplace' ),
            ),
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'vendor' ),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 21,
            'menu_icon'           => 'dashicons-businessman',
            'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'show_in_rest'        => true,
        ) );
        
        // Booking post type
        register_post_type( 'seyahat_booking', array(
            'labels' => array(
                'name'               => __( 'Rezervasyonlar', 'seyahat-marketplace' ),
                'singular_name'      => __( 'Rezervasyon', 'seyahat-marketplace' ),
                'menu_name'          => __( 'Rezervasyonlar', 'seyahat-marketplace' ),
                'add_new'            => __( 'Yeni Rezervasyon', 'seyahat-marketplace' ),
                'add_new_item'       => __( 'Yeni Rezervasyon Ekle', 'seyahat-marketplace' ),
                'edit_item'          => __( 'Rezervasyonu Düzenle', 'seyahat-marketplace' ),
                'new_item'           => __( 'Yeni Rezervasyon', 'seyahat-marketplace' ),
                'view_item'          => __( 'Rezervasyonu Görüntüle', 'seyahat-marketplace' ),
                'search_items'       => __( 'Rezervasyon Ara', 'seyahat-marketplace' ),
                'not_found'          => __( 'Rezervasyon bulunamadı', 'seyahat-marketplace' ),
                'not_found_in_trash' => __( 'Çöp kutusunda rezervasyon bulunamadı', 'seyahat-marketplace' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 22,
            'menu_icon'           => 'dashicons-calendar-alt',
            'supports'            => array( 'title', 'editor', 'author' ),
            'show_in_rest'        => true,
        ) );
    }
    
    /**
     * Register custom taxonomies
     */
    public function register_taxonomies() {
        // Tour categories
        register_taxonomy( 'tour_category', 'seyahat_tour', array(
            'labels' => array(
                'name'              => __( 'Tur Kategorileri', 'seyahat-marketplace' ),
                'singular_name'     => __( 'Tur Kategorisi', 'seyahat-marketplace' ),
                'search_items'      => __( 'Kategori Ara', 'seyahat-marketplace' ),
                'all_items'         => __( 'Tüm Kategoriler', 'seyahat-marketplace' ),
                'parent_item'       => __( 'Üst Kategori', 'seyahat-marketplace' ),
                'parent_item_colon' => __( 'Üst Kategori:', 'seyahat-marketplace' ),
                'edit_item'         => __( 'Kategoriyi Düzenle', 'seyahat-marketplace' ),
                'update_item'       => __( 'Kategoriyi Güncelle', 'seyahat-marketplace' ),
                'add_new_item'      => __( 'Yeni Kategori Ekle', 'seyahat-marketplace' ),
                'new_item_name'     => __( 'Yeni Kategori Adı', 'seyahat-marketplace' ),
                'menu_name'         => __( 'Kategoriler', 'seyahat-marketplace' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'tour-category' ),
            'show_in_rest'      => true,
        ) );
        
        // Destinations
        register_taxonomy( 'destination', 'seyahat_tour', array(
            'labels' => array(
                'name'              => __( 'Destinasyonlar', 'seyahat-marketplace' ),
                'singular_name'     => __( 'Destinasyon', 'seyahat-marketplace' ),
                'search_items'      => __( 'Destinasyon Ara', 'seyahat-marketplace' ),
                'all_items'         => __( 'Tüm Destinasyonlar', 'seyahat-marketplace' ),
                'parent_item'       => __( 'Üst Destinasyon', 'seyahat-marketplace' ),
                'parent_item_colon' => __( 'Üst Destinasyon:', 'seyahat-marketplace' ),
                'edit_item'         => __( 'Destinasyonu Düzenle', 'seyahat-marketplace' ),
                'update_item'       => __( 'Destinasyonu Güncelle', 'seyahat-marketplace' ),
                'add_new_item'      => __( 'Yeni Destinasyon Ekle', 'seyahat-marketplace' ),
                'new_item_name'     => __( 'Yeni Destinasyon Adı', 'seyahat-marketplace' ),
                'menu_name'         => __( 'Destinasyonlar', 'seyahat-marketplace' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'destination' ),
            'show_in_rest'      => true,
        ) );
    }
    
    /**
     * Add custom user roles
     */
    public function add_user_roles() {
        // Vendor role
        add_role( 'vendor', __( 'Satıcı', 'seyahat-marketplace' ), array(
            'read'                   => true,
            'edit_posts'             => true,
            'edit_others_posts'      => false,
            'publish_posts'          => true,
            'edit_published_posts'   => true,
            'delete_posts'           => true,
            'delete_published_posts' => true,
            'upload_files'           => true,
            'manage_seyahat_tours'   => true,
            'edit_seyahat_tours'     => true,
            'publish_seyahat_tours'  => true,
        ) );
        
        // Tour guide role
        add_role( 'tour_guide', __( 'Tur Rehberi', 'seyahat-marketplace' ), array(
            'read'                  => true,
            'edit_posts'            => false,
            'edit_seyahat_tours'    => true,
            'manage_seyahat_bookings' => true,
        ) );
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 
            'seyahat-marketplace-style', 
            SEYAHAT_MARKETPLACE_PLUGIN_URL . 'assets/css/frontend.css', 
            array(), 
            SEYAHAT_MARKETPLACE_VERSION 
        );
        
        wp_enqueue_script( 
            'seyahat-marketplace-script', 
            SEYAHAT_MARKETPLACE_PLUGIN_URL . 'assets/js/frontend.js', 
            array( 'jquery' ), 
            SEYAHAT_MARKETPLACE_VERSION, 
            true 
        );
        
        wp_localize_script( 'seyahat-marketplace-script', 'seyahat_marketplace', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'seyahat_marketplace_nonce' ),
            'strings'  => array(
                'loading'           => __( 'Yükleniyor...', 'seyahat-marketplace' ),
                'error'             => __( 'Bir hata oluştu.', 'seyahat-marketplace' ),
                'success'           => __( 'İşlem başarılı.', 'seyahat-marketplace' ),
                'confirm_delete'    => __( 'Bu işlemi silmek istediğinizden emin misiniz?', 'seyahat-marketplace' ),
                'booking_confirmed' => __( 'Rezervasyonunuz onaylandı.', 'seyahat-marketplace' ),
            ),
        ) );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_style( 
            'seyahat-marketplace-admin-style', 
            SEYAHAT_MARKETPLACE_PLUGIN_URL . 'assets/css/admin.css', 
            array(), 
            SEYAHAT_MARKETPLACE_VERSION 
        );
        
        wp_enqueue_script( 
            'seyahat-marketplace-admin-script', 
            SEYAHAT_MARKETPLACE_PLUGIN_URL . 'assets/js/admin.js', 
            array( 'jquery', 'wp-color-picker' ), 
            SEYAHAT_MARKETPLACE_VERSION, 
            true 
        );
    }
    
    /**
     * Add custom rewrite endpoints
     */
    public function add_rewrite_endpoints() {
        add_rewrite_endpoint( 'vendor-dashboard', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'vendor-tours', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'vendor-bookings', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'vendor-earnings', EP_ROOT | EP_PAGES );
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'seyahat-marketplace', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
    
    /**
     * Plugin activation hook
     */
    public function activate() {
        // Create necessary database tables
        $this->create_tables();
        
        // Add default options
        $this->add_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation hook
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create necessary database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Bookings table
        $table_name = $wpdb->prefix . 'seyahat_bookings';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            tour_id mediumint(9) NOT NULL,
            vendor_id mediumint(9) NOT NULL,
            user_id mediumint(9) NOT NULL,
            booking_date datetime DEFAULT CURRENT_TIMESTAMP,
            tour_date date NOT NULL,
            guests int(3) NOT NULL DEFAULT 1,
            total_amount decimal(10,2) NOT NULL,
            commission_amount decimal(10,2) NOT NULL DEFAULT 0,
            status varchar(20) NOT NULL DEFAULT 'pending',
            payment_status varchar(20) NOT NULL DEFAULT 'pending',
            payment_id varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Vendor earnings table
        $table_name_earnings = $wpdb->prefix . 'seyahat_vendor_earnings';
        $sql_earnings = "CREATE TABLE $table_name_earnings (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            vendor_id mediumint(9) NOT NULL,
            booking_id mediumint(9) NOT NULL,
            amount decimal(10,2) NOT NULL,
            commission decimal(10,2) NOT NULL,
            net_amount decimal(10,2) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            paid_date datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        dbDelta( $sql_earnings );
    }
    
    /**
     * Add default options
     */
    private function add_default_options() {
        add_option( 'seyahat_marketplace_version', SEYAHAT_MARKETPLACE_VERSION );
        add_option( 'seyahat_commission_rate', 10 ); // Default 10% commission
        add_option( 'seyahat_currency', 'TRY' );
        add_option( 'seyahat_vendor_registration', 'open' );
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        echo '<div class="notice notice-error"><p>';
        echo __( 'Seyahat Marketplace eklentisi WooCommerce\'in aktif olmasını gerektiriyor.', 'seyahat-marketplace' );
        echo '</p></div>';
    }
}

// Initialize the plugin
new Seyahat_Marketplace();
