<?php
/**
 * AJAX handlers class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_Ajax {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        // Search functionality
        add_action( 'wp_ajax_seyahat_search', array( $this, 'handle_search' ) );
        add_action( 'wp_ajax_nopriv_seyahat_search', array( $this, 'handle_search' ) );
    }
    
    /**
     * Handle search requests
     */
    public function handle_search() {
        check_ajax_referer( 'seyahat_nonce', 'nonce' );
        
        $query = sanitize_text_field( $_POST['query'] );
        
        if ( strlen( $query ) < 3 ) {
            wp_send_json_error( array( 'message' => __( 'En az 3 karakter giriniz.', 'seyahat-marketplace' ) ) );
        }
        
        $results = array(
            'tours' => $this->search_tours( $query ),
            'destinations' => $this->search_destinations( $query ),
        );
        
        wp_send_json_success( $results );
    }
    
    /**
     * Search tours
     */
    private function search_tours( $query ) {
        $args = array(
            'post_type' => 'seyahat_tour',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            's' => $query,
        );
        
        $tours = new WP_Query( $args );
        $results = array();
        
        if ( $tours->have_posts() ) {
            while ( $tours->have_posts() ) {
                $tours->the_post();
                $results[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
                    'price' => get_post_meta( get_the_ID(), '_tour_price', true ),
                    'location' => get_post_meta( get_the_ID(), '_tour_location', true ),
                );
            }
            wp_reset_postdata();
        }
        
        return $results;
    }
    
    /**
     * Search destinations
     */
    private function search_destinations( $query ) {
        $terms = get_terms( array(
            'taxonomy' => 'destination',
            'hide_empty' => false,
            'search' => $query,
            'number' => 10,
        ) );
        
        $results = array();
        
        if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
            foreach ( $terms as $term ) {
                $results[] = array(
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'count' => $term->count,
                    'link' => get_term_link( $term ),
                );
            }
        }
        
        return $results;
    }
}
