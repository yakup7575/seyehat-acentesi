<?php
/**
 * Booking management class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_Booking {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        add_action( 'wp_ajax_create_booking', array( $this, 'create_booking' ) );
        add_action( 'wp_ajax_nopriv_create_booking', array( $this, 'create_booking' ) );
        add_action( 'wp_ajax_cancel_booking', array( $this, 'cancel_booking' ) );
    }
    
    /**
     * Create new booking
     */
    public function create_booking() {
        check_ajax_referer( 'seyahat_marketplace_nonce', 'nonce' );
        
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'Rezervasyon yapmak için giriş yapmanız gerekiyor.', 'seyahat-marketplace' ) ) );
        }
        
        $tour_id = intval( $_POST['tour_id'] );
        $tour_date = sanitize_text_field( $_POST['tour_date'] );
        $guests = intval( $_POST['guests'] );
        $user_id = get_current_user_id();
        
        // Get tour details
        $tour = get_post( $tour_id );
        if ( ! $tour || $tour->post_type !== 'seyahat_tour' ) {
            wp_send_json_error( array( 'message' => __( 'Geçersiz tur.', 'seyahat-marketplace' ) ) );
        }
        
        // Get vendor ID
        $vendor_id = $tour->post_author;
        
        // Calculate total amount
        $price = get_post_meta( $tour_id, '_tour_price', true );
        $price_type = get_post_meta( $tour_id, '_tour_price_type', true );
        $total_amount = ( $price_type === 'per_group' ) ? $price : $price * $guests;
        
        // Calculate commission
        $commission_rate = get_option( 'seyahat_commission_rate', 10 );
        $commission_amount = ( $total_amount * $commission_rate ) / 100;
        
        global $wpdb;
        
        // Create booking record
        $result = $wpdb->insert(
            $wpdb->prefix . 'seyahat_bookings',
            array(
                'tour_id' => $tour_id,
                'vendor_id' => $vendor_id,
                'user_id' => $user_id,
                'tour_date' => $tour_date,
                'guests' => $guests,
                'total_amount' => $total_amount,
                'commission_amount' => $commission_amount,
                'status' => 'pending',
                'payment_status' => 'pending',
            ),
            array( '%d', '%d', '%d', '%s', '%d', '%f', '%f', '%s', '%s' )
        );
        
        if ( $result ) {
            $booking_id = $wpdb->insert_id;
            
            // Create booking post for management
            $booking_post_id = wp_insert_post( array(
                'post_title' => sprintf( __( 'Rezervasyon #%d', 'seyahat-marketplace' ), $booking_id ),
                'post_type' => 'seyahat_booking',
                'post_status' => 'publish',
                'post_author' => $user_id,
            ) );
            
            if ( $booking_post_id ) {
                update_post_meta( $booking_post_id, '_booking_id', $booking_id );
                update_post_meta( $booking_post_id, '_tour_id', $tour_id );
                update_post_meta( $booking_post_id, '_vendor_id', $vendor_id );
            }
            
            wp_send_json_success( array( 
                'message' => __( 'Rezervasyonunuz oluşturuldu.', 'seyahat-marketplace' ),
                'booking_id' => $booking_id,
                'redirect_url' => $this->get_payment_url( $booking_id )
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Rezervasyon oluşturulamadı.', 'seyahat-marketplace' ) ) );
        }
    }
    
    /**
     * Cancel booking
     */
    public function cancel_booking() {
        check_ajax_referer( 'seyahat_marketplace_nonce', 'nonce' );
        
        $booking_id = intval( $_POST['booking_id'] );
        $user_id = get_current_user_id();
        
        global $wpdb;
        
        // Get booking
        $booking = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seyahat_bookings WHERE id = %d AND user_id = %d",
            $booking_id,
            $user_id
        ) );
        
        if ( ! $booking ) {
            wp_send_json_error( array( 'message' => __( 'Rezervasyon bulunamadı.', 'seyahat-marketplace' ) ) );
        }
        
        if ( $booking->status === 'cancelled' ) {
            wp_send_json_error( array( 'message' => __( 'Bu rezervasyon zaten iptal edilmiş.', 'seyahat-marketplace' ) ) );
        }
        
        // Update booking status
        $result = $wpdb->update(
            $wpdb->prefix . 'seyahat_bookings',
            array( 'status' => 'cancelled' ),
            array( 'id' => $booking_id ),
            array( '%s' ),
            array( '%d' )
        );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => __( 'Rezervasyonunuz iptal edildi.', 'seyahat-marketplace' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Rezervasyon iptal edilemedi.', 'seyahat-marketplace' ) ) );
        }
    }
    
    /**
     * Get payment URL
     */
    private function get_payment_url( $booking_id ) {
        // This would integrate with payment gateways
        return add_query_arg( array(
            'action' => 'payment',
            'booking_id' => $booking_id
        ), home_url() );
    }
    
    /**
     * Get user bookings
     */
    public static function get_user_bookings( $user_id, $status = 'all' ) {
        global $wpdb;
        
        $where_clause = "WHERE user_id = %d";
        $params = array( $user_id );
        
        if ( $status !== 'all' ) {
            $where_clause .= " AND status = %s";
            $params[] = $status;
        }
        
        $sql = "SELECT * FROM {$wpdb->prefix}seyahat_bookings $where_clause ORDER BY booking_date DESC";
        
        return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
    }
    
    /**
     * Get vendor bookings
     */
    public static function get_vendor_bookings( $vendor_id, $status = 'all' ) {
        global $wpdb;
        
        $where_clause = "WHERE vendor_id = %d";
        $params = array( $vendor_id );
        
        if ( $status !== 'all' ) {
            $where_clause .= " AND status = %s";
            $params[] = $status;
        }
        
        $sql = "SELECT * FROM {$wpdb->prefix}seyahat_bookings $where_clause ORDER BY booking_date DESC";
        
        return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
    }
}
