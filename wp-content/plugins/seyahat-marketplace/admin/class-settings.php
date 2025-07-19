<?php
/**
 * Settings management class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_Settings {
    
    public function __construct() {
        add_action( 'admin_init', array( $this, 'init_settings' ) );
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        register_setting( 'seyahat_marketplace_settings', 'seyahat_commission_rate' );
        register_setting( 'seyahat_marketplace_settings', 'seyahat_currency' );
        register_setting( 'seyahat_marketplace_settings', 'seyahat_vendor_registration' );
    }
}
