<?php
/**
 * Dashboard management class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_Dashboard {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        add_action( 'template_redirect', array( $this, 'handle_dashboard_pages' ) );
        add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
    }
    
    /**
     * Add custom query vars
     */
    public function add_query_vars( $vars ) {
        $vars[] = 'vendor-dashboard';
        $vars[] = 'vendor-tours';
        $vars[] = 'vendor-bookings';
        $vars[] = 'vendor-earnings';
        return $vars;
    }
    
    /**
     * Handle dashboard page requests
     */
    public function handle_dashboard_pages() {
        global $wp_query;
        
        if ( isset( $wp_query->query_vars['vendor-dashboard'] ) ) {
            $this->load_vendor_dashboard();
        } elseif ( isset( $wp_query->query_vars['vendor-tours'] ) ) {
            $this->load_vendor_tours();
        } elseif ( isset( $wp_query->query_vars['vendor-bookings'] ) ) {
            $this->load_vendor_bookings();
        } elseif ( isset( $wp_query->query_vars['vendor-earnings'] ) ) {
            $this->load_vendor_earnings();
        }
    }
    
    /**
     * Load vendor dashboard
     */
    private function load_vendor_dashboard() {
        if ( ! is_user_logged_in() || ! current_user_can( 'vendor' ) ) {
            wp_redirect( wp_login_url() );
            exit;
        }
        
        get_header();
        $this->render_vendor_dashboard();
        get_footer();
        exit;
    }
    
    /**
     * Render vendor dashboard
     */
    private function render_vendor_dashboard() {
        $user_id = get_current_user_id();
        $vendor = Seyahat_Vendor::get_vendor_by_user( $user_id );
        
        if ( ! $vendor ) {
            echo '<div class="container"><p>' . __( 'Satıcı profili bulunamadı.', 'seyahat-marketplace' ) . '</p></div>';
            return;
        }
        
        $vendor_id = $vendor->ID;
        $earnings_summary = Seyahat_Commission::get_vendor_earnings_summary( $vendor_id );
        $recent_bookings = Seyahat_Booking::get_vendor_bookings( $vendor_id, 'all' );
        $recent_bookings = array_slice( $recent_bookings, 0, 5 ); // Latest 5 bookings
        ?>
        <div class="container vendor-dashboard">
            <div class="dashboard-header">
                <h1><?php printf( __( 'Hoş geldiniz, %s', 'seyahat-marketplace' ), esc_html( $vendor->post_title ) ); ?></h1>
                <p><?php _e( 'Satıcı kontrol paneli', 'seyahat-marketplace' ); ?></p>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3><?php _e( 'Toplam Satış', 'seyahat-marketplace' ); ?></h3>
                    <div class="stat-value">₺<?php echo number_format( $earnings_summary->total_sales ?? 0, 2 ); ?></div>
                </div>
                <div class="stat-card">
                    <h3><?php _e( 'Bekleyen Kazanç', 'seyahat-marketplace' ); ?></h3>
                    <div class="stat-value">₺<?php echo number_format( $earnings_summary->pending_earnings ?? 0, 2 ); ?></div>
                </div>
                <div class="stat-card">
                    <h3><?php _e( 'Toplam Rezervasyon', 'seyahat-marketplace' ); ?></h3>
                    <div class="stat-value"><?php echo intval( $earnings_summary->total_bookings ?? 0 ); ?></div>
                </div>
            </div>
            
            <div class="dashboard-navigation">
                <a href="<?php echo home_url( '/vendor-tours/' ); ?>" class="nav-item">
                    <i class="fas fa-map-marked-alt"></i>
                    <?php _e( 'Turlarım', 'seyahat-marketplace' ); ?>
                </a>
                <a href="<?php echo home_url( '/vendor-bookings/' ); ?>" class="nav-item">
                    <i class="fas fa-calendar-check"></i>
                    <?php _e( 'Rezervasyonlar', 'seyahat-marketplace' ); ?>
                </a>
                <a href="<?php echo home_url( '/vendor-earnings/' ); ?>" class="nav-item">
                    <i class="fas fa-chart-line"></i>
                    <?php _e( 'Kazançlar', 'seyahat-marketplace' ); ?>
                </a>
                <a href="<?php echo admin_url( 'post-new.php?post_type=seyahat_tour' ); ?>" class="nav-item">
                    <i class="fas fa-plus"></i>
                    <?php _e( 'Yeni Tur Ekle', 'seyahat-marketplace' ); ?>
                </a>
            </div>
            
            <?php if ( ! empty( $recent_bookings ) ) : ?>
            <div class="recent-bookings">
                <h2><?php _e( 'Son Rezervasyonlar', 'seyahat-marketplace' ); ?></h2>
                <div class="bookings-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?php _e( 'Tur', 'seyahat-marketplace' ); ?></th>
                                <th><?php _e( 'Tarih', 'seyahat-marketplace' ); ?></th>
                                <th><?php _e( 'Misafir', 'seyahat-marketplace' ); ?></th>
                                <th><?php _e( 'Tutar', 'seyahat-marketplace' ); ?></th>
                                <th><?php _e( 'Durum', 'seyahat-marketplace' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $recent_bookings as $booking ) : ?>
                            <tr>
                                <td><?php echo esc_html( get_the_title( $booking->tour_id ) ); ?></td>
                                <td><?php echo esc_html( date( 'd.m.Y', strtotime( $booking->tour_date ) ) ); ?></td>
                                <td><?php echo intval( $booking->guests ); ?></td>
                                <td>₺<?php echo number_format( $booking->total_amount, 2 ); ?></td>
                                <td><span class="status status-<?php echo esc_attr( $booking->status ); ?>"><?php echo esc_html( ucfirst( $booking->status ) ); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <style>
        .vendor-dashboard {
            margin: 2rem auto;
            max-width: 1200px;
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
            margin-top: 0.5rem;
        }
        
        .dashboard-navigation {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 3rem;
        }
        
        .nav-item {
            background: #007bff;
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .nav-item:hover {
            transform: translateY(-5px);
            color: white;
        }
        
        .nav-item i {
            display: block;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .recent-bookings {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: bold;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        </style>
        <?php
    }
    
    /**
     * Load vendor tours page
     */
    private function load_vendor_tours() {
        if ( ! is_user_logged_in() || ! current_user_can( 'vendor' ) ) {
            wp_redirect( wp_login_url() );
            exit;
        }
        
        get_header();
        echo '<div class="container"><h1>' . __( 'Turlarım', 'seyahat-marketplace' ) . '</h1></div>';
        get_footer();
        exit;
    }
    
    /**
     * Load vendor bookings page
     */
    private function load_vendor_bookings() {
        if ( ! is_user_logged_in() || ! current_user_can( 'vendor' ) ) {
            wp_redirect( wp_login_url() );
            exit;
        }
        
        get_header();
        echo '<div class="container"><h1>' . __( 'Rezervasyonlar', 'seyahat-marketplace' ) . '</h1></div>';
        get_footer();
        exit;
    }
    
    /**
     * Load vendor earnings page
     */
    private function load_vendor_earnings() {
        if ( ! is_user_logged_in() || ! current_user_can( 'vendor' ) ) {
            wp_redirect( wp_login_url() );
            exit;
        }
        
        get_header();
        echo '<div class="container"><h1>' . __( 'Kazançlar', 'seyahat-marketplace' ) . '</h1></div>';
        get_footer();
        exit;
    }
}
