<?php
/**
 * Frontend functionality class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seyahat_Frontend {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        add_shortcode( 'seyahat_search', array( $this, 'search_shortcode' ) );
        add_shortcode( 'seyahat_tours', array( $this, 'tours_shortcode' ) );
        add_shortcode( 'seyahat_vendor_registration', array( $this, 'vendor_registration_shortcode' ) );
    }
    
    /**
     * Search form shortcode
     */
    public function search_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'style' => 'default',
        ), $atts );
        
        ob_start();
        ?>
        <div class="seyahat-search-form">
            <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form">
                <div class="search-fields">
                    <input type="text" name="destination" placeholder="<?php _e( 'Nereye gitmek istiyorsunuz?', 'seyahat-marketplace' ); ?>" />
                    <input type="date" name="date" />
                    <select name="guests">
                        <option value="1">1 <?php _e( 'kişi', 'seyahat-marketplace' ); ?></option>
                        <option value="2">2 <?php _e( 'kişi', 'seyahat-marketplace' ); ?></option>
                        <option value="3">3 <?php _e( 'kişi', 'seyahat-marketplace' ); ?></option>
                        <option value="4">4+ <?php _e( 'kişi', 'seyahat-marketplace' ); ?></option>
                    </select>
                    <button type="submit"><?php _e( 'Ara', 'seyahat-marketplace' ); ?></button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Tours listing shortcode
     */
    public function tours_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 12,
            'category' => '',
            'destination' => '',
        ), $atts );
        
        $args = array(
            'post_type' => 'seyahat_tour',
            'post_status' => 'publish',
            'posts_per_page' => intval( $atts['posts_per_page'] ),
        );
        
        if ( $atts['category'] ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'tour_category',
                'field' => 'slug',
                'terms' => $atts['category'],
            );
        }
        
        if ( $atts['destination'] ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'destination',
                'field' => 'slug',
                'terms' => $atts['destination'],
            );
        }
        
        $tours = new WP_Query( $args );
        
        ob_start();
        
        if ( $tours->have_posts() ) {
            echo '<div class="tours-grid">';
            
            while ( $tours->have_posts() ) {
                $tours->the_post();
                $this->render_tour_card( get_the_ID() );
            }
            
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . __( 'Tur bulunamadı.', 'seyahat-marketplace' ) . '</p>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Vendor registration form shortcode
     */
    public function vendor_registration_shortcode( $atts ) {
        ob_start();
        ?>
        <div class="vendor-registration-form">
            <h3><?php _e( 'Satıcı Olarak Katılın', 'seyahat-marketplace' ); ?></h3>
            <form id="vendor-registration-form">
                <div class="form-group">
                    <label for="company_name"><?php _e( 'Şirket Adı', 'seyahat-marketplace' ); ?></label>
                    <input type="text" id="company_name" name="company_name" required />
                </div>
                
                <div class="form-group">
                    <label for="email"><?php _e( 'E-posta', 'seyahat-marketplace' ); ?></label>
                    <input type="email" id="email" name="email" required />
                </div>
                
                <div class="form-group">
                    <label for="phone"><?php _e( 'Telefon', 'seyahat-marketplace' ); ?></label>
                    <input type="tel" id="phone" name="phone" required />
                </div>
                
                <div class="form-group">
                    <label for="address"><?php _e( 'Adres', 'seyahat-marketplace' ); ?></label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="tax_number"><?php _e( 'Vergi Numarası', 'seyahat-marketplace' ); ?></label>
                    <input type="text" id="tax_number" name="tax_number" required />
                </div>
                
                <button type="submit"><?php _e( 'Başvuru Gönder', 'seyahat-marketplace' ); ?></button>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#vendor-registration-form').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: seyahat_marketplace.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'register_vendor',
                        nonce: seyahat_marketplace.nonce,
                        company_name: $('#company_name').val(),
                        email: $('#email').val(),
                        phone: $('#phone').val(),
                        address: $('#address').val(),
                        tax_number: $('#tax_number').val(),
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            $('#vendor-registration-form')[0].reset();
                        } else {
                            alert(response.data.message);
                        }
                    }
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render tour card
     */
    private function render_tour_card( $tour_id ) {
        $price = get_post_meta( $tour_id, '_tour_price', true );
        $currency = get_post_meta( $tour_id, '_tour_currency', true );
        $location = get_post_meta( $tour_id, '_tour_location', true );
        ?>
        <div class="tour-card">
            <div class="tour-thumbnail">
                <?php if ( has_post_thumbnail( $tour_id ) ) : ?>
                    <?php echo get_the_post_thumbnail( $tour_id, 'medium' ); ?>
                <?php endif; ?>
            </div>
            
            <div class="tour-content">
                <h3><a href="<?php echo get_permalink( $tour_id ); ?>"><?php echo get_the_title( $tour_id ); ?></a></h3>
                
                <?php if ( $location ) : ?>
                <div class="tour-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo esc_html( $location ); ?>
                </div>
                <?php endif; ?>
                
                <div class="tour-excerpt">
                    <?php echo wp_trim_words( get_the_excerpt( $tour_id ), 15 ); ?>
                </div>
                
                <?php if ( $price ) : ?>
                <div class="tour-price">
                    <?php echo esc_html( $price . ' ' . $currency ); ?>
                    <span class="price-unit"><?php _e( '/ kişi', 'seyahat-marketplace' ); ?></span>
                </div>
                <?php endif; ?>
                
                <a href="<?php echo get_permalink( $tour_id ); ?>" class="btn btn-primary">
                    <?php _e( 'Detayları Gör', 'seyahat-marketplace' ); ?>
                </a>
            </div>
        </div>
        <?php
    }
}
