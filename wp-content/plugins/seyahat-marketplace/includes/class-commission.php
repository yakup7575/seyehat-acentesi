<?php
/**
 * Commission management class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_Commission {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        // Hook into booking completion to calculate earnings
        add_action( 'seyahat_booking_completed', array( $this, 'calculate_vendor_earnings' ) );
    }
    
    /**
     * Calculate vendor earnings when booking is completed
     */
    public function calculate_vendor_earnings( $booking_id ) {
        global $wpdb;
        
        $booking = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seyahat_bookings WHERE id = %d",
            $booking_id
        ) );
        
        if ( ! $booking ) {
            return false;
        }
        
        $commission = $booking->commission_amount;
        $net_amount = $booking->total_amount - $commission;
        
        // Insert earnings record
        $result = $wpdb->insert(
            $wpdb->prefix . 'seyahat_vendor_earnings',
            array(
                'vendor_id' => $booking->vendor_id,
                'booking_id' => $booking_id,
                'amount' => $booking->total_amount,
                'commission' => $commission,
                'net_amount' => $net_amount,
                'status' => 'pending',
            ),
            array( '%d', '%d', '%f', '%f', '%f', '%s' )
        );
        
        return $result ? $wpdb->insert_id : false;
    }
    
    /**
     * Process vendor payout
     */
    public static function process_payout( $vendor_id, $earnings_ids = array() ) {
        global $wpdb;
        
        if ( empty( $earnings_ids ) ) {
            // Get all pending earnings for vendor
            $earnings_ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}seyahat_vendor_earnings WHERE vendor_id = %d AND status = 'pending'",
                $vendor_id
            ) );
        }
        
        if ( empty( $earnings_ids ) ) {
            return false;
        }
        
        $placeholders = implode( ',', array_fill( 0, count( $earnings_ids ), '%d' ) );
        
        // Calculate total payout
        $total_payout = $wpdb->get_var( $wpdb->prepare(
            "SELECT SUM(net_amount) FROM {$wpdb->prefix}seyahat_vendor_earnings WHERE id IN ($placeholders)",
            $earnings_ids
        ) );
        
        // Mark as paid
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$wpdb->prefix}seyahat_vendor_earnings SET status = 'paid', paid_date = NOW() WHERE id IN ($placeholders)",
            $earnings_ids
        ) );
        
        // Log payout (could integrate with payment systems)
        do_action( 'seyahat_vendor_payout_processed', $vendor_id, $total_payout, $earnings_ids );
        
        return $total_payout;
    }
    
    /**
     * Get commission settings
     */
    public static function get_commission_rate( $vendor_id = null ) {
        if ( $vendor_id ) {
            // Check for vendor-specific commission rate
            $vendor_rate = get_post_meta( $vendor_id, '_commission_rate', true );
            if ( $vendor_rate ) {
                return floatval( $vendor_rate );
            }
        }
        
        // Return global commission rate
        return floatval( get_option( 'seyahat_commission_rate', 10 ) );
    }
    
    /**
     * Set vendor commission rate
     */
    public static function set_vendor_commission_rate( $vendor_id, $rate ) {
        return update_post_meta( $vendor_id, '_commission_rate', floatval( $rate ) );
    }
    
    /**
     * Get vendor earnings summary
     */
    public static function get_vendor_earnings_summary( $vendor_id ) {
        global $wpdb;
        
        $summary = $wpdb->get_row( $wpdb->prepare(
            "SELECT 
                COUNT(*) as total_bookings,
                SUM(amount) as total_sales,
                SUM(commission) as total_commission,
                SUM(net_amount) as total_earnings,
                SUM(CASE WHEN status = 'pending' THEN net_amount ELSE 0 END) as pending_earnings,
                SUM(CASE WHEN status = 'paid' THEN net_amount ELSE 0 END) as paid_earnings
             FROM {$wpdb->prefix}seyahat_vendor_earnings 
             WHERE vendor_id = %d",
            $vendor_id
        ) );
        
        return $summary;
    }
}
