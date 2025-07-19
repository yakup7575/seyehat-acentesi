<?php
/**
 * Tour management class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_Tour {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        add_action( 'add_meta_boxes', array( $this, 'add_tour_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_tour_meta' ) );
        add_action( 'wp_ajax_search_tours', array( $this, 'search_tours' ) );
        add_action( 'wp_ajax_nopriv_search_tours', array( $this, 'search_tours' ) );
        add_filter( 'the_content', array( $this, 'add_tour_details' ) );
    }
    
    /**
     * Add tour meta boxes
     */
    public function add_tour_meta_boxes() {
        add_meta_box(
            'tour-details',
            __( 'Tur Detayları', 'seyahat-marketplace' ),
            array( $this, 'tour_details_meta_box' ),
            'seyahat_tour',
            'normal',
            'high'
        );
        
        add_meta_box(
            'tour-pricing',
            __( 'Fiyatlandırma', 'seyahat-marketplace' ),
            array( $this, 'tour_pricing_meta_box' ),
            'seyahat_tour',
            'side',
            'default'
        );
        
        add_meta_box(
            'tour-schedule',
            __( 'Program', 'seyahat-marketplace' ),
            array( $this, 'tour_schedule_meta_box' ),
            'seyahat_tour',
            'normal',
            'default'
        );
    }
    
    /**
     * Tour details meta box
     */
    public function tour_details_meta_box( $post ) {
        wp_nonce_field( 'save_tour_meta', 'tour_meta_nonce' );
        
        $duration = get_post_meta( $post->ID, '_tour_duration', true );
        $max_guests = get_post_meta( $post->ID, '_tour_max_guests', true );
        $min_age = get_post_meta( $post->ID, '_tour_min_age', true );
        $location = get_post_meta( $post->ID, '_tour_location', true );
        $meeting_point = get_post_meta( $post->ID, '_tour_meeting_point', true );
        $included = get_post_meta( $post->ID, '_tour_included', true );
        $excluded = get_post_meta( $post->ID, '_tour_excluded', true );
        $languages = get_post_meta( $post->ID, '_tour_languages', true );
        ?>
        <table class="form-table">
            <tr>
                <th><label for="tour_duration"><?php _e( 'Süre', 'seyahat-marketplace' ); ?></label></th>
                <td><input type="text" id="tour_duration" name="tour_duration" value="<?php echo esc_attr( $duration ); ?>" class="regular-text" placeholder="<?php _e( 'Örn: 3 saat', 'seyahat-marketplace' ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="tour_max_guests"><?php _e( 'Maksimum Misafir', 'seyahat-marketplace' ); ?></label></th>
                <td><input type="number" id="tour_max_guests" name="tour_max_guests" value="<?php echo esc_attr( $max_guests ); ?>" min="1" max="100" /></td>
            </tr>
            <tr>
                <th><label for="tour_min_age"><?php _e( 'Minimum Yaş', 'seyahat-marketplace' ); ?></label></th>
                <td><input type="number" id="tour_min_age" name="tour_min_age" value="<?php echo esc_attr( $min_age ); ?>" min="0" max="99" /></td>
            </tr>
            <tr>
                <th><label for="tour_location"><?php _e( 'Konum', 'seyahat-marketplace' ); ?></label></th>
                <td><input type="text" id="tour_location" name="tour_location" value="<?php echo esc_attr( $location ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="tour_meeting_point"><?php _e( 'Buluşma Noktası', 'seyahat-marketplace' ); ?></label></th>
                <td><textarea id="tour_meeting_point" name="tour_meeting_point" rows="3" class="large-text"><?php echo esc_textarea( $meeting_point ); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="tour_included"><?php _e( 'Dahil Olanlar', 'seyahat-marketplace' ); ?></label></th>
                <td><textarea id="tour_included" name="tour_included" rows="4" class="large-text"><?php echo esc_textarea( $included ); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="tour_excluded"><?php _e( 'Dahil Olmayanlar', 'seyahat-marketplace' ); ?></label></th>
                <td><textarea id="tour_excluded" name="tour_excluded" rows="4" class="large-text"><?php echo esc_textarea( $excluded ); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="tour_languages"><?php _e( 'Diller', 'seyahat-marketplace' ); ?></label></th>
                <td><input type="text" id="tour_languages" name="tour_languages" value="<?php echo esc_attr( $languages ); ?>" class="regular-text" placeholder="<?php _e( 'Türkçe, İngilizce, Almanca', 'seyahat-marketplace' ); ?>" /></td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Tour pricing meta box
     */
    public function tour_pricing_meta_box( $post ) {
        $price = get_post_meta( $post->ID, '_tour_price', true );
        $price_type = get_post_meta( $post->ID, '_tour_price_type', true );
        $currency = get_post_meta( $post->ID, '_tour_currency', true );
        ?>
        <p>
            <label for="tour_price"><?php _e( 'Fiyat', 'seyahat-marketplace' ); ?></label>
            <input type="number" id="tour_price" name="tour_price" value="<?php echo esc_attr( $price ); ?>" step="0.01" min="0" style="width: 100%;" />
        </p>
        <p>
            <label for="tour_price_type"><?php _e( 'Fiyat Türü', 'seyahat-marketplace' ); ?></label>
            <select id="tour_price_type" name="tour_price_type" style="width: 100%;">
                <option value="per_person" <?php selected( $price_type, 'per_person' ); ?>><?php _e( 'Kişi Başı', 'seyahat-marketplace' ); ?></option>
                <option value="per_group" <?php selected( $price_type, 'per_group' ); ?>><?php _e( 'Grup', 'seyahat-marketplace' ); ?></option>
            </select>
        </p>
        <p>
            <label for="tour_currency"><?php _e( 'Para Birimi', 'seyahat-marketplace' ); ?></label>
            <select id="tour_currency" name="tour_currency" style="width: 100%;">
                <option value="TRY" <?php selected( $currency, 'TRY' ); ?>>TRY - Türk Lirası</option>
                <option value="USD" <?php selected( $currency, 'USD' ); ?>>USD - Dolar</option>
                <option value="EUR" <?php selected( $currency, 'EUR' ); ?>>EUR - Euro</option>
            </select>
        </p>
        <?php
    }
    
    /**
     * Tour schedule meta box
     */
    public function tour_schedule_meta_box( $post ) {
        $schedule = get_post_meta( $post->ID, '_tour_schedule', true );
        ?>
        <p>
            <label for="tour_schedule"><?php _e( 'Günlük Program', 'seyahat-marketplace' ); ?></label>
            <?php
            wp_editor( $schedule, 'tour_schedule', array(
                'textarea_name' => 'tour_schedule',
                'media_buttons' => false,
                'textarea_rows' => 10,
                'teeny'         => true,
            ) );
            ?>
        </p>
        <?php
    }
    
    /**
     * Save tour meta data
     */
    public function save_tour_meta( $post_id ) {
        if ( ! isset( $_POST['tour_meta_nonce'] ) || ! wp_verify_nonce( $_POST['tour_meta_nonce'], 'save_tour_meta' ) ) {
            return;
        }
        
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        $meta_fields = array(
            'tour_duration',
            'tour_max_guests',
            'tour_min_age',
            'tour_location',
            'tour_meeting_point',
            'tour_included',
            'tour_excluded',
            'tour_languages',
            'tour_price',
            'tour_price_type',
            'tour_currency',
            'tour_schedule',
        );
        
        foreach ( $meta_fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
            }
        }
    }
    
    /**
     * Search tours via AJAX
     */
    public function search_tours() {
        check_ajax_referer( 'seyahat_marketplace_nonce', 'nonce' );
        
        $query = sanitize_text_field( $_POST['query'] );
        $destination = sanitize_text_field( $_POST['destination'] );
        $date = sanitize_text_field( $_POST['date'] );
        $guests = intval( $_POST['guests'] );
        
        $args = array(
            'post_type'      => 'seyahat_tour',
            'post_status'    => 'publish',
            'posts_per_page' => 20,
            's'              => $query,
            'meta_query'     => array(),
        );
        
        if ( $guests > 0 ) {
            $args['meta_query'][] = array(
                'key'     => '_tour_max_guests',
                'value'   => $guests,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }
        
        if ( $destination ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'destination',
                    'field'    => 'name',
                    'terms'    => $destination,
                ),
            );
        }
        
        $tours = new WP_Query( $args );
        $results = array();
        
        if ( $tours->have_posts() ) {
            while ( $tours->have_posts() ) {
                $tours->the_post();
                $results[] = array(
                    'id'       => get_the_ID(),
                    'title'    => get_the_title(),
                    'excerpt'  => get_the_excerpt(),
                    'price'    => get_post_meta( get_the_ID(), '_tour_price', true ),
                    'currency' => get_post_meta( get_the_ID(), '_tour_currency', true ),
                    'duration' => get_post_meta( get_the_ID(), '_tour_duration', true ),
                    'location' => get_post_meta( get_the_ID(), '_tour_location', true ),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
                );
            }
            wp_reset_postdata();
        }
        
        wp_send_json_success( $results );
    }
    
    /**
     * Add tour details to content
     */
    public function add_tour_details( $content ) {
        if ( is_singular( 'seyahat_tour' ) ) {
            $tour_details = $this->get_tour_details_html( get_the_ID() );
            $content .= $tour_details;
        }
        return $content;
    }
    
    /**
     * Get tour details HTML
     */
    private function get_tour_details_html( $tour_id ) {
        $duration = get_post_meta( $tour_id, '_tour_duration', true );
        $max_guests = get_post_meta( $tour_id, '_tour_max_guests', true );
        $min_age = get_post_meta( $tour_id, '_tour_min_age', true );
        $location = get_post_meta( $tour_id, '_tour_location', true );
        $price = get_post_meta( $tour_id, '_tour_price', true );
        $currency = get_post_meta( $tour_id, '_tour_currency', true );
        $included = get_post_meta( $tour_id, '_tour_included', true );
        $excluded = get_post_meta( $tour_id, '_tour_excluded', true );
        
        ob_start();
        ?>
        <div class="tour-details">
            <div class="tour-info-grid">
                <div class="tour-info-item">
                    <i class="fas fa-clock"></i>
                    <span><?php echo esc_html( $duration ); ?></span>
                </div>
                <div class="tour-info-item">
                    <i class="fas fa-users"></i>
                    <span><?php printf( __( 'Maks %d kişi', 'seyahat-marketplace' ), $max_guests ); ?></span>
                </div>
                <div class="tour-info-item">
                    <i class="fas fa-child"></i>
                    <span><?php printf( __( '%d+ yaş', 'seyahat-marketplace' ), $min_age ); ?></span>
                </div>
                <div class="tour-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo esc_html( $location ); ?></span>
                </div>
            </div>
            
            <div class="tour-price">
                <span class="price-amount"><?php echo esc_html( $price . ' ' . $currency ); ?></span>
                <span class="price-unit"><?php _e( '/ kişi', 'seyahat-marketplace' ); ?></span>
            </div>
            
            <?php if ( $included ) : ?>
            <div class="tour-included">
                <h3><?php _e( 'Dahil Olanlar', 'seyahat-marketplace' ); ?></h3>
                <p><?php echo nl2br( esc_html( $included ) ); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ( $excluded ) : ?>
            <div class="tour-excluded">
                <h3><?php _e( 'Dahil Olmayanlar', 'seyahat-marketplace' ); ?></h3>
                <p><?php echo nl2br( esc_html( $excluded ) ); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="tour-booking">
                <button class="btn btn-primary btn-book-tour" data-tour-id="<?php echo esc_attr( $tour_id ); ?>">
                    <?php _e( 'Rezervasyon Yap', 'seyahat-marketplace' ); ?>
                </button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
