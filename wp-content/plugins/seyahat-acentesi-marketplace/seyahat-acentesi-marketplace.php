<?php
/**
 * Plugin Name: Seyahat Acentesi Marketplace
 * Plugin URI: https://github.com/yakup7575/seyehat-acentesi
 * Description: A comprehensive marketplace plugin for travel agencies to manage tours, partners, and bookings
 * Version: 1.0.0
 * Author: Travel Marketplace Team
 * License: GPL v2 or later
 * Text Domain: seyahat-acentesi-marketplace
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SAM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SAM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SAM_PLUGIN_VERSION', '1.0.0');

// Main plugin class
class SeyahatAcentesiMarketplace {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Partner registration hooks
        add_action('wp_ajax_submit_partner_application', array($this, 'handle_partner_application'));
        add_action('wp_ajax_nopriv_submit_partner_application', array($this, 'handle_partner_application'));
        
        // Partner management hooks
        add_action('wp_ajax_approve_partner', array($this, 'approve_partner'));
        add_action('wp_ajax_reject_partner', array($this, 'reject_partner'));
        
        // Custom user roles
        add_action('init', array($this, 'add_custom_roles'));
        
        // Shortcodes
        add_shortcode('partner_registration_form', array($this, 'partner_registration_form_shortcode'));
        add_shortcode('partner_dashboard', array($this, 'partner_dashboard_shortcode'));
        add_shortcode('tour_search', array($this, 'tour_search_shortcode'));
    }
    
    public function init() {
        // Plugin initialization
        $this->create_tables();
        $this->setup_rewrite_rules();
    }
    
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Partner applications table
        $table_name = $wpdb->prefix . 'partner_applications';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            company_name varchar(255) NOT NULL,
            tax_number varchar(50) NOT NULL,
            phone varchar(20) NOT NULL,
            address text NOT NULL,
            website varchar(255),
            description text,
            documents longtext,
            status varchar(20) DEFAULT 'pending',
            applied_date datetime DEFAULT CURRENT_TIMESTAMP,
            reviewed_date datetime,
            reviewed_by bigint(20) UNSIGNED,
            notes text,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Tour bookings table (for future WooCommerce integration)
        $bookings_table = $wpdb->prefix . 'tour_bookings';
        
        $sql2 = "CREATE TABLE $bookings_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            tour_id bigint(20) UNSIGNED NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            booking_date datetime DEFAULT CURRENT_TIMESTAMP,
            travel_date date,
            participants int(11) DEFAULT 1,
            total_price decimal(10,2),
            status varchar(20) DEFAULT 'pending',
            payment_status varchar(20) DEFAULT 'pending',
            special_requests text,
            PRIMARY KEY (id),
            KEY tour_id (tour_id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        dbDelta($sql2);
    }
    
    public function add_custom_roles() {
        // Add partner role
        add_role('partner', 'Partner', array(
            'read' => true,
            'edit_posts' => true,
            'edit_published_posts' => true,
            'publish_posts' => true,
            'delete_posts' => true,
            'delete_published_posts' => true,
            'upload_files' => true,
            'edit_tours' => true,
            'publish_tours' => true,
            'delete_tours' => true,
        ));
        
        // Add custom capabilities to administrator
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->add_cap('manage_partners');
            $admin_role->add_cap('approve_partners');
            $admin_role->add_cap('manage_tours');
        }
    }
    
    public function setup_rewrite_rules() {
        add_rewrite_rule('^partner-kayit/?$', 'index.php?pagename=partner-kayit', 'top');
        add_rewrite_rule('^partner-dashboard/?$', 'index.php?pagename=partner-dashboard', 'top');
        flush_rewrite_rules();
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script('sam-frontend', SAM_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), SAM_PLUGIN_VERSION, true);
        wp_enqueue_style('sam-frontend', SAM_PLUGIN_URL . 'assets/css/frontend.css', array(), SAM_PLUGIN_VERSION);
        
        wp_localize_script('sam-frontend', 'sam_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sam_nonce'),
        ));
    }
    
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'seyahat-acentesi') !== false) {
            wp_enqueue_script('sam-admin', SAM_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), SAM_PLUGIN_VERSION, true);
            wp_enqueue_style('sam-admin', SAM_PLUGIN_URL . 'assets/css/admin.css', array(), SAM_PLUGIN_VERSION);
            
            wp_localize_script('sam-admin', 'sam_admin_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('sam_admin_nonce'),
            ));
        }
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Seyahat Acentesi Marketplace',
            'Marketplace',
            'manage_options',
            'seyahat-acentesi-marketplace',
            array($this, 'admin_dashboard'),
            'dashicons-palmtree',
            30
        );
        
        add_submenu_page(
            'seyahat-acentesi-marketplace',
            'Partner Başvuruları',
            'Partner Başvuruları',
            'manage_options',
            'partner-applications',
            array($this, 'partner_applications_page')
        );
        
        add_submenu_page(
            'seyahat-acentesi-marketplace',
            'Tur Yönetimi',
            'Tur Yönetimi',
            'manage_options',
            'tour-management',
            array($this, 'tour_management_page')
        );
        
        add_submenu_page(
            'seyahat-acentesi-marketplace',
            'Rezervasyonlar',
            'Rezervasyonlar',
            'manage_options',
            'bookings-management',
            array($this, 'bookings_management_page')
        );
        
        add_submenu_page(
            'seyahat-acentesi-marketplace',
            'Ayarlar',
            'Ayarlar',
            'manage_options',
            'marketplace-settings',
            array($this, 'settings_page')
        );
    }
    
    public function admin_dashboard() {
        include SAM_PLUGIN_PATH . 'templates/admin/dashboard.php';
    }
    
    public function partner_applications_page() {
        include SAM_PLUGIN_PATH . 'templates/admin/partner-applications.php';
    }
    
    public function tour_management_page() {
        include SAM_PLUGIN_PATH . 'templates/admin/tour-management.php';
    }
    
    public function bookings_management_page() {
        include SAM_PLUGIN_PATH . 'templates/admin/bookings-management.php';
    }
    
    public function settings_page() {
        include SAM_PLUGIN_PATH . 'templates/admin/settings.php';
    }
    
    public function partner_registration_form_shortcode($atts) {
        ob_start();
        include SAM_PLUGIN_PATH . 'templates/frontend/partner-registration-form.php';
        return ob_get_clean();
    }
    
    public function partner_dashboard_shortcode($atts) {
        if (!is_user_logged_in() || !current_user_can('edit_posts')) {
            return '<p>Bu sayfaya erişim yetkiniz yok.</p>';
        }
        
        ob_start();
        include SAM_PLUGIN_PATH . 'templates/frontend/partner-dashboard.php';
        return ob_get_clean();
    }
    
    public function tour_search_shortcode($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => 12,
            'category' => '',
        ), $atts);
        
        ob_start();
        include SAM_PLUGIN_PATH . 'templates/frontend/tour-search.php';
        return ob_get_clean();
    }
    
    public function handle_partner_application() {
        check_ajax_referer('sam_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        
        $user_id = get_current_user_id();
        $company_name = sanitize_text_field($_POST['company_name']);
        $tax_number = sanitize_text_field($_POST['tax_number']);
        $phone = sanitize_text_field($_POST['phone']);
        $address = sanitize_textarea_field($_POST['address']);
        $website = esc_url_raw($_POST['website']);
        $description = sanitize_textarea_field($_POST['description']);
        
        // Handle file uploads
        $documents = array();
        if (!empty($_FILES['documents'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            
            foreach ($_FILES['documents']['name'] as $key => $name) {
                if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                    $upload = wp_handle_upload(array(
                        'name' => $_FILES['documents']['name'][$key],
                        'type' => $_FILES['documents']['type'][$key],
                        'tmp_name' => $_FILES['documents']['tmp_name'][$key],
                        'error' => $_FILES['documents']['error'][$key],
                        'size' => $_FILES['documents']['size'][$key],
                    ), array('test_form' => false));
                    
                    if (!isset($upload['error'])) {
                        $documents[] = $upload['url'];
                    }
                }
            }
        }
        
        $table_name = $wpdb->prefix . 'partner_applications';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'company_name' => $company_name,
                'tax_number' => $tax_number,
                'phone' => $phone,
                'address' => $address,
                'website' => $website,
                'description' => $description,
                'documents' => json_encode($documents),
                'status' => 'pending',
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result) {
            wp_send_json_success('Başvurunuz başarıyla gönderildi. En kısa sürede değerlendirilerek size dönüş yapılacaktır.');
        } else {
            wp_send_json_error('Başvuru gönderilirken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }
    
    public function approve_partner() {
        check_ajax_referer('sam_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_partners')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        
        $application_id = intval($_POST['application_id']);
        $table_name = $wpdb->prefix . 'partner_applications';
        
        // Get application details
        $application = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $application_id
        ));
        
        if ($application) {
            // Update application status
            $wpdb->update(
                $table_name,
                array(
                    'status' => 'approved',
                    'reviewed_date' => current_time('mysql'),
                    'reviewed_by' => get_current_user_id(),
                ),
                array('id' => $application_id),
                array('%s', '%s', '%d'),
                array('%d')
            );
            
            // Update user role to partner
            $user = new WP_User($application->user_id);
            $user->set_role('partner');
            
            // Send approval email
            $user_info = get_userdata($application->user_id);
            $subject = 'Partner Başvurunuz Onaylandı';
            $message = "Merhaba {$user_info->display_name},\n\n";
            $message .= "Partner başvurunuz onaylandı. Artık platformumuzda tur yayınlayabilirsiniz.\n\n";
            $message .= "İyi çalışmalar dileriz.";
            
            wp_mail($user_info->user_email, $subject, $message);
            
            wp_send_json_success('Partner başvurusu onaylandı.');
        } else {
            wp_send_json_error('Başvuru bulunamadı.');
        }
    }
    
    public function reject_partner() {
        check_ajax_referer('sam_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_partners')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        
        $application_id = intval($_POST['application_id']);
        $reason = sanitize_textarea_field($_POST['reason']);
        $table_name = $wpdb->prefix . 'partner_applications';
        
        // Get application details
        $application = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $application_id
        ));
        
        if ($application) {
            // Update application status
            $wpdb->update(
                $table_name,
                array(
                    'status' => 'rejected',
                    'reviewed_date' => current_time('mysql'),
                    'reviewed_by' => get_current_user_id(),
                    'notes' => $reason,
                ),
                array('id' => $application_id),
                array('%s', '%s', '%d', '%s'),
                array('%d')
            );
            
            // Send rejection email
            $user_info = get_userdata($application->user_id);
            $subject = 'Partner Başvurunuz Hakkında';
            $message = "Merhaba {$user_info->display_name},\n\n";
            $message .= "Partner başvurunuz değerlendirildi ancak şu anda onaylanamadı.\n\n";
            if ($reason) {
                $message .= "Sebep: {$reason}\n\n";
            }
            $message .= "Eksiklikleri tamamladıktan sonra tekrar başvurabilirsiniz.";
            
            wp_mail($user_info->user_email, $subject, $message);
            
            wp_send_json_success('Partner başvurusu reddedildi.');
        } else {
            wp_send_json_error('Başvuru bulunamadı.');
        }
    }
}

// Initialize the plugin
new SeyahatAcentesiMarketplace();

// Helper functions
function sam_get_partner_applications($status = 'all') {
    global $wpdb;
    $table_name = $wpdb->prefix . 'partner_applications';
    
    $where = '';
    if ($status !== 'all') {
        $where = $wpdb->prepare("WHERE status = %s", $status);
    }
    
    return $wpdb->get_results("SELECT * FROM $table_name $where ORDER BY applied_date DESC");
}

function sam_get_user_application($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'partner_applications';
    
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d ORDER BY applied_date DESC LIMIT 1",
        $user_id
    ));
}

function sam_is_partner_approved($user_id) {
    $application = sam_get_user_application($user_id);
    return $application && $application->status === 'approved';
}
?>