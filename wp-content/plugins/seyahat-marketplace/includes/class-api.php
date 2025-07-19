<?php
/**
 * REST API class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_API {
    
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route( 'seyahat/v1', '/tours', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_tours' ),
            'permission_callback' => '__return_true',
        ) );
        
        register_rest_route( 'seyahat/v1', '/tours/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_tour' ),
            'permission_callback' => '__return_true',
        ) );
        
        register_rest_route( 'seyahat/v1', '/bookings', array(
            'methods' => 'POST',
            'callback' => array( $this, 'create_booking' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );
    }
    
    /**
     * Get tours endpoint
     */
    public function get_tours( $request ) {
        $params = $request->get_params();
        
        $args = array(
            'post_type' => 'seyahat_tour',
            'post_status' => 'publish',
            'posts_per_page' => isset( $params['per_page'] ) ? intval( $params['per_page'] ) : 10,
            'paged' => isset( $params['page'] ) ? intval( $params['page'] ) : 1,
        );
        
        if ( isset( $params['search'] ) ) {
            $args['s'] = sanitize_text_field( $params['search'] );
        }
        
        $tours = new WP_Query( $args );
        $data = array();
        
        if ( $tours->have_posts() ) {
            while ( $tours->have_posts() ) {
                $tours->the_post();
                $data[] = $this->format_tour_data( get_the_ID() );
            }
            wp_reset_postdata();
        }
        
        return rest_ensure_response( $data );
    }
    
    /**
     * Get single tour endpoint
     */
    public function get_tour( $request ) {
        $id = intval( $request['id'] );
        $tour = get_post( $id );
        
        if ( ! $tour || $tour->post_type !== 'seyahat_tour' ) {
            return new WP_Error( 'tour_not_found', __( 'Tur bulunamadı.', 'seyahat-marketplace' ), array( 'status' => 404 ) );
        }
        
        return rest_ensure_response( $this->format_tour_data( $id ) );
    }
    
    /**
     * Create booking endpoint
     */
    public function create_booking( $request ) {
        $params = $request->get_params();
        
        // Implementation would be similar to the AJAX booking creation
        return rest_ensure_response( array( 'message' => 'Booking created' ) );
    }
    
    /**
     * Check authentication
     */
    public function check_auth() {
        return is_user_logged_in();
    }
    
    /**
     * Format tour data for API response
     */
    private function format_tour_data( $tour_id ) {
        return array(
            'id' => $tour_id,
            'title' => get_the_title( $tour_id ),
            'content' => get_post_field( 'post_content', $tour_id ),
            'excerpt' => get_the_excerpt( $tour_id ),
            'permalink' => get_permalink( $tour_id ),
            'thumbnail' => get_the_post_thumbnail_url( $tour_id, 'large' ),
            'price' => get_post_meta( $tour_id, '_tour_price', true ),
            'currency' => get_post_meta( $tour_id, '_tour_currency', true ),
            'duration' => get_post_meta( $tour_id, '_tour_duration', true ),
            'max_guests' => get_post_meta( $tour_id, '_tour_max_guests', true ),
            'location' => get_post_meta( $tour_id, '_tour_location', true ),
            'categories' => wp_get_post_terms( $tour_id, 'tour_category', array( 'fields' => 'names' ) ),
            'destinations' => wp_get_post_terms( $tour_id, 'destination', array( 'fields' => 'names' ) ),
        );
    }
}
