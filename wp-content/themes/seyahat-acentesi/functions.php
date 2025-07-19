<?php
/**
 * Theme Name: Seyahat Acentesi Marketplace
 * Description: A WordPress theme for travel agency marketplace
 * Version: 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme setup
function seyahat_acentesi_setup() {
    // Add theme support for various features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption'
    ));
    add_theme_support('custom-logo');
    add_theme_support('woocommerce');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'seyahat-acentesi'),
        'footer' => __('Footer Menu', 'seyahat-acentesi')
    ));
    
    // Set content width
    $GLOBALS['content_width'] = 1200;
}
add_action('after_setup_theme', 'seyahat_acentesi_setup');

// Enqueue styles and scripts
function seyahat_acentesi_scripts() {
    wp_enqueue_style('seyahat-acentesi-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_script('seyahat-acentesi-script', get_template_directory_uri() . '/js/script.js', array('jquery'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('seyahat-acentesi-script', 'seyahat_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('seyahat_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'seyahat_acentesi_scripts');

// Register widget areas
function seyahat_acentesi_widgets_init() {
    register_sidebar(array(
        'name' => __('Sidebar', 'seyahat-acentesi'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'seyahat-acentesi'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => __('Footer 1', 'seyahat-acentesi'),
        'id' => 'footer-1',
        'description' => __('Footer widget area 1', 'seyahat-acentesi'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => __('Footer 2', 'seyahat-acentesi'),
        'id' => 'footer-2',
        'description' => __('Footer widget area 2', 'seyahat-acentesi'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => __('Footer 3', 'seyahat-acentesi'),
        'id' => 'footer-3',
        'description' => __('Footer widget area 3', 'seyahat-acentesi'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'seyahat_acentesi_widgets_init');

// Custom post types for tours
function register_tour_post_type() {
    $labels = array(
        'name' => _x('Tours', 'Post type general name', 'seyahat-acentesi'),
        'singular_name' => _x('Tour', 'Post type singular name', 'seyahat-acentesi'),
        'menu_name' => _x('Tours', 'Admin Menu text', 'seyahat-acentesi'),
        'add_new' => __('Add New', 'seyahat-acentesi'),
        'add_new_item' => __('Add New Tour', 'seyahat-acentesi'),
        'new_item' => __('New Tour', 'seyahat-acentesi'),
        'edit_item' => __('Edit Tour', 'seyahat-acentesi'),
        'view_item' => __('View Tour', 'seyahat-acentesi'),
        'all_items' => __('All Tours', 'seyahat-acentesi'),
        'search_items' => __('Search Tours', 'seyahat-acentesi'),
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'tours'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-palmtree',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'author'),
        'show_in_rest' => true,
    );
    
    register_post_type('tour', $args);
}
add_action('init', 'register_tour_post_type');

// Register tour categories taxonomy
function register_tour_taxonomy() {
    $labels = array(
        'name' => _x('Tour Categories', 'taxonomy general name', 'seyahat-acentesi'),
        'singular_name' => _x('Tour Category', 'taxonomy singular name', 'seyahat-acentesi'),
        'search_items' => __('Search Categories', 'seyahat-acentesi'),
        'all_items' => __('All Categories', 'seyahat-acentesi'),
        'parent_item' => __('Parent Category', 'seyahat-acentesi'),
        'parent_item_colon' => __('Parent Category:', 'seyahat-acentesi'),
        'edit_item' => __('Edit Category', 'seyahat-acentesi'),
        'update_item' => __('Update Category', 'seyahat-acentesi'),
        'add_new_item' => __('Add New Category', 'seyahat-acentesi'),
        'new_item_name' => __('New Category Name', 'seyahat-acentesi'),
        'menu_name' => __('Categories', 'seyahat-acentesi'),
    );
    
    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'tour-category'),
        'show_in_rest' => true,
    );
    
    register_taxonomy('tour_category', array('tour'), $args);
}
add_action('init', 'register_tour_taxonomy');

// Add custom fields support
function add_tour_meta_boxes() {
    add_meta_box(
        'tour_details',
        __('Tour Details', 'seyahat-acentesi'),
        'tour_details_callback',
        'tour',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_tour_meta_boxes');

function tour_details_callback($post) {
    wp_nonce_field('save_tour_details', 'tour_details_nonce');
    
    $price = get_post_meta($post->ID, '_tour_price', true);
    $duration = get_post_meta($post->ID, '_tour_duration', true);
    $location = get_post_meta($post->ID, '_tour_location', true);
    $max_participants = get_post_meta($post->ID, '_tour_max_participants', true);
    $start_date = get_post_meta($post->ID, '_tour_start_date', true);
    $agency_id = get_post_meta($post->ID, '_tour_agency_id', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="tour_price">Price (₺)</label></th>';
    echo '<td><input type="number" id="tour_price" name="tour_price" value="' . esc_attr($price) . '" /></td></tr>';
    
    echo '<tr><th><label for="tour_duration">Duration (days)</label></th>';
    echo '<td><input type="number" id="tour_duration" name="tour_duration" value="' . esc_attr($duration) . '" /></td></tr>';
    
    echo '<tr><th><label for="tour_location">Location</label></th>';
    echo '<td><input type="text" id="tour_location" name="tour_location" value="' . esc_attr($location) . '" /></td></tr>';
    
    echo '<tr><th><label for="tour_max_participants">Max Participants</label></th>';
    echo '<td><input type="number" id="tour_max_participants" name="tour_max_participants" value="' . esc_attr($max_participants) . '" /></td></tr>';
    
    echo '<tr><th><label for="tour_start_date">Start Date</label></th>';
    echo '<td><input type="date" id="tour_start_date" name="tour_start_date" value="' . esc_attr($start_date) . '" /></td></tr>';
    
    echo '</table>';
}

function save_tour_details($post_id) {
    if (!isset($_POST['tour_details_nonce']) || !wp_verify_nonce($_POST['tour_details_nonce'], 'save_tour_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $fields = array('tour_price', 'tour_duration', 'tour_location', 'tour_max_participants', 'tour_start_date');
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    // Set agency ID to current user if they are a partner
    if (current_user_can('edit_posts') && !current_user_can('manage_options')) {
        update_post_meta($post_id, '_tour_agency_id', get_current_user_id());
    }
}
add_action('save_post', 'save_tour_details');

// Helper functions
function get_tour_price($post_id) {
    return get_post_meta($post_id, '_tour_price', true);
}

function get_tour_duration($post_id) {
    return get_post_meta($post_id, '_tour_duration', true);
}

function get_tour_location($post_id) {
    return get_post_meta($post_id, '_tour_location', true);
}

function get_tour_max_participants($post_id) {
    return get_post_meta($post_id, '_tour_max_participants', true);
}

function get_tour_start_date($post_id) {
    return get_post_meta($post_id, '_tour_start_date', true);
}

function get_tour_agency_id($post_id) {
    return get_post_meta($post_id, '_tour_agency_id', true);
}

// Template functions
function seyahat_acentesi_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    $time_string = sprintf($time_string,
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date())
    );
    
    $posted_on = sprintf(
        /* translators: %s: post date. */
        esc_html_x('Posted on %s', 'post date', 'seyahat-acentesi'),
        '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
    );
    
    echo '<span class="posted-on">' . $posted_on . '</span>';
}

function seyahat_acentesi_posted_by() {
    $byline = sprintf(
        /* translators: %s: post author. */
        esc_html_x('by %s', 'post author', 'seyahat-acentesi'),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
    );
    
    echo '<span class="byline"> ' . $byline . '</span>';
}
?>