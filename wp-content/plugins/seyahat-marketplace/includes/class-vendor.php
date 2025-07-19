<?php
/**
 * Vendor management class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_Vendor {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        add_action( 'wp_ajax_register_vendor', array( $this, 'register_vendor' ) );
        add_action( 'wp_ajax_nopriv_register_vendor', array( $this, 'register_vendor' ) );
        add_action( 'template_redirect', array( $this, 'handle_vendor_dashboard' ) );
        add_filter( 'user_register', array( $this, 'set_vendor_role' ) );
    }
    
    /**
     * Handle vendor registration
     */
    public function register_vendor() {
        check_ajax_referer( 'seyahat_marketplace_nonce', 'nonce' );
        
        $company_name = sanitize_text_field( $_POST['company_name'] );
        $email = sanitize_email( $_POST['email'] );
        $phone = sanitize_text_field( $_POST['phone'] );
        $address = sanitize_textarea_field( $_POST['address'] );
        $tax_number = sanitize_text_field( $_POST['tax_number'] );
        
        // Create user account
        $user_id = wp_create_user( $email, wp_generate_password(), $email );
        
        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
        }
        
        // Set user role to vendor
        $user = new WP_User( $user_id );
        $user->set_role( 'vendor' );
        
        // Create vendor post
        $vendor_id = wp_insert_post( array(
            'post_title'  => $company_name,
            'post_type'   => 'seyahat_vendor',
            'post_status' => 'pending',
            'post_author' => $user_id,
        ) );
        
        if ( $vendor_id ) {
            // Save vendor meta data
            update_post_meta( $vendor_id, '_vendor_email', $email );
            update_post_meta( $vendor_id, '_vendor_phone', $phone );
            update_post_meta( $vendor_id, '_vendor_address', $address );
            update_post_meta( $vendor_id, '_vendor_tax_number', $tax_number );
            update_post_meta( $vendor_id, '_vendor_user_id', $user_id );
            update_post_meta( $vendor_id, '_vendor_status', 'pending' );
            
            // Link user to vendor
            update_user_meta( $user_id, '_vendor_id', $vendor_id );
            
            wp_send_json_success( array( 'message' => __( 'Satıcı kaydınız başarıyla oluşturuldu. Onay bekliyor.', 'seyahat-marketplace' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Satıcı kaydı oluşturulamadı.', 'seyahat-marketplace' ) ) );
        }
    }
    
    /**
     * Set vendor role on registration
     */
    public function set_vendor_role( $user_id ) {
        if ( isset( $_POST['register_as_vendor'] ) ) {
            $user = new WP_User( $user_id );
            $user->set_role( 'vendor' );
        }
        return $user_id;
    }
    
    /**
     * Handle vendor dashboard access
     */
    public function handle_vendor_dashboard() {
        global $wp_query;
        
        if ( isset( $wp_query->query_vars['vendor-dashboard'] ) ) {
            if ( ! is_user_logged_in() || ! current_user_can( 'vendor' ) ) {
                wp_redirect( wp_login_url() );
                exit;
            }
            
            $this->load_vendor_dashboard();
        }
    }
    
    /**
     * Load vendor dashboard template
     */
    private function load_vendor_dashboard() {
        include SEYAHAT_MARKETPLACE_PLUGIN_DIR . 'templates/vendor-dashboard.php';
        exit;
    }
    
    /**
     * Get vendor by user ID
     */
    public static function get_vendor_by_user( $user_id ) {
        $vendor_id = get_user_meta( $user_id, '_vendor_id', true );
        if ( $vendor_id ) {
            return get_post( $vendor_id );
        }
        return false;
    }
    
    /**
     * Get vendor earnings
     */
    public static function get_vendor_earnings( $vendor_id, $status = 'all' ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seyahat_vendor_earnings';
        $where_clause = "WHERE vendor_id = %d";
        $params = array( $vendor_id );
        
        if ( $status !== 'all' ) {
            $where_clause .= " AND status = %s";
            $params[] = $status;
        }
        
        $sql = "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC";
        
        return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
    }
    
    /**
     * Approve vendor
     */
    public static function approve_vendor( $vendor_id ) {
        wp_update_post( array(
            'ID' => $vendor_id,
            'post_status' => 'publish'
        ) );
        
        update_post_meta( $vendor_id, '_vendor_status', 'approved' );
        
        // Send approval email
        $vendor_user_id = get_post_meta( $vendor_id, '_vendor_user_id', true );
        if ( $vendor_user_id ) {
            $user = get_userdata( $vendor_user_id );
            // Send email notification
            wp_mail( 
                $user->user_email, 
                __( 'Satıcı Başvurunuz Onaylandı', 'seyahat-marketplace' ),
                __( 'Tebrikler! Satıcı başvurunuz onaylandı. Artık ürünlerinizi satabilirsiniz.', 'seyahat-marketplace' )
            );
        }
        
        return true;
    }
    
    /**
     * Reject vendor
     */
    public static function reject_vendor( $vendor_id, $reason = '' ) {
        update_post_meta( $vendor_id, '_vendor_status', 'rejected' );
        update_post_meta( $vendor_id, '_rejection_reason', $reason );
        
        // Send rejection email
        $vendor_user_id = get_post_meta( $vendor_id, '_vendor_user_id', true );
        if ( $vendor_user_id ) {
            $user = get_userdata( $vendor_user_id );
            $message = __( 'Üzgünüz, satıcı başvurunuz reddedildi.', 'seyahat-marketplace' );
            if ( $reason ) {
                $message .= "\n\n" . __( 'Sebep: ', 'seyahat-marketplace' ) . $reason;
            }
            
            wp_mail( 
                $user->user_email, 
                __( 'Satıcı Başvurunuz Reddedildi', 'seyahat-marketplace' ),
                $message
            );
        }
        
        return true;
    }
}
