<?php
/**
 * Admin functionality class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_Admin {
    
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'init_admin' ) );
    }
    
    /**
     * Add admin menu items
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Seyahat Marketplace', 'seyahat-marketplace' ),
            __( 'Marketplace', 'seyahat-marketplace' ),
            'manage_options',
            'seyahat-marketplace',
            array( $this, 'admin_dashboard' ),
            'dashicons-location-alt',
            30
        );
        
        add_submenu_page(
            'seyahat-marketplace',
            __( 'Satıcılar', 'seyahat-marketplace' ),
            __( 'Satıcılar', 'seyahat-marketplace' ),
            'manage_options',
            'seyahat-vendors',
            array( $this, 'vendors_page' )
        );
        
        add_submenu_page(
            'seyahat-marketplace',
            __( 'Rezervasyonlar', 'seyahat-marketplace' ),
            __( 'Rezervasyonlar', 'seyahat-marketplace' ),
            'manage_options',
            'seyahat-bookings',
            array( $this, 'bookings_page' )
        );
        
        add_submenu_page(
            'seyahat-marketplace',
            __( 'Ayarlar', 'seyahat-marketplace' ),
            __( 'Ayarlar', 'seyahat-marketplace' ),
            'manage_options',
            'seyahat-settings',
            array( $this, 'settings_page' )
        );
    }
    
    /**
     * Initialize admin functionality
     */
    public function init_admin() {
        // Add any admin initialization here
    }
    
    /**
     * Admin dashboard page
     */
    public function admin_dashboard() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Seyahat Marketplace Dashboard', 'seyahat-marketplace' ); ?></h1>
            
            <div class="marketplace-dashboard">
                <div class="dashboard-widgets">
                    <div class="widget-box">
                        <h3><?php _e( 'Toplam Satıcılar', 'seyahat-marketplace' ); ?></h3>
                        <div class="widget-value"><?php echo $this->get_total_vendors(); ?></div>
                    </div>
                    
                    <div class="widget-box">
                        <h3><?php _e( 'Toplam Turlar', 'seyahat-marketplace' ); ?></h3>
                        <div class="widget-value"><?php echo $this->get_total_tours(); ?></div>
                    </div>
                    
                    <div class="widget-box">
                        <h3><?php _e( 'Bu Ay Rezervasyonlar', 'seyahat-marketplace' ); ?></h3>
                        <div class="widget-value"><?php echo $this->get_monthly_bookings(); ?></div>
                    </div>
                    
                    <div class="widget-box">
                        <h3><?php _e( 'Bu Ay Gelir', 'seyahat-marketplace' ); ?></h3>
                        <div class="widget-value">₺<?php echo number_format( $this->get_monthly_revenue(), 2 ); ?></div>
                    </div>
                </div>
                
                <div class="recent-activity">
                    <h2><?php _e( 'Son Aktiviteler', 'seyahat-marketplace' ); ?></h2>
                    <!-- Recent activity content -->
                </div>
            </div>
        </div>
        
        <style>
        .marketplace-dashboard {
            margin-top: 20px;
        }
        
        .dashboard-widgets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .widget-box {
            background: #fff;
            padding: 20px;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            text-align: center;
        }
        
        .widget-value {
            font-size: 2em;
            font-weight: bold;
            color: #0073aa;
            margin-top: 10px;
        }
        
        .recent-activity {
            background: #fff;
            padding: 20px;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
        }
        </style>
        <?php
    }
    
    /**
     * Vendors management page
     */
    public function vendors_page() {
        echo '<div class="wrap"><h1>' . __( 'Satıcı Yönetimi', 'seyahat-marketplace' ) . '</h1></div>';
    }
    
    /**
     * Bookings management page
     */
    public function bookings_page() {
        echo '<div class="wrap"><h1>' . __( 'Rezervasyon Yönetimi', 'seyahat-marketplace' ) . '</h1></div>';
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        echo '<div class="wrap"><h1>' . __( 'Marketplace Ayarları', 'seyahat-marketplace' ) . '</h1></div>';
    }
    
    /**
     * Get total vendors count
     */
    private function get_total_vendors() {
        $vendors = get_posts( array(
            'post_type' => 'seyahat_vendor',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids',
        ) );
        return count( $vendors );
    }
    
    /**
     * Get total tours count
     */
    private function get_total_tours() {
        $tours = get_posts( array(
            'post_type' => 'seyahat_tour',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids',
        ) );
        return count( $tours );
    }
    
    /**
     * Get monthly bookings count
     */
    private function get_monthly_bookings() {
        global $wpdb;
        
        $current_month = date( 'Y-m' );
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}seyahat_bookings WHERE DATE_FORMAT(booking_date, '%%Y-%%m') = %s",
            $current_month
        ) );
        
        return intval( $count );
    }
    
    /**
     * Get monthly revenue
     */
    private function get_monthly_revenue() {
        global $wpdb;
        
        $current_month = date( 'Y-m' );
        $revenue = $wpdb->get_var( $wpdb->prepare(
            "SELECT SUM(total_amount) FROM {$wpdb->prefix}seyahat_bookings WHERE DATE_FORMAT(booking_date, '%%Y-%%m') = %s AND payment_status = 'completed'",
            $current_month
        ) );
        
        return floatval( $revenue );
    }
}
