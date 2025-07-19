<?php
/**
 * WooCommerce Integration for Seyahat Acentesi Marketplace
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SAM_WooCommerce_Integration {
    
    public function __construct() {
        // Check if WooCommerce is active
        if (class_exists('WooCommerce')) {
            add_action('init', array($this, 'init'));
        } else {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
        }
    }
    
    public function init() {
        // Product creation hooks
        add_action('save_post_tour', array($this, 'create_tour_product'));
        add_action('wp_trash_post', array($this, 'trash_tour_product'));
        
        // Cart and checkout customizations
        add_filter('woocommerce_add_cart_item_data', array($this, 'add_tour_cart_item_data'), 10, 3);
        add_filter('woocommerce_get_item_data', array($this, 'display_tour_cart_item_data'), 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'save_tour_order_item_data'), 10, 4);
        
        // Order management
        add_action('woocommerce_order_status_completed', array($this, 'handle_completed_order'));
        add_action('woocommerce_order_status_cancelled', array($this, 'handle_cancelled_order'));
        
        // Product customizations
        add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'custom_add_to_cart_text'));
        add_filter('woocommerce_product_add_to_cart_text', array($this, 'custom_add_to_cart_text'));
        
        // Hide WooCommerce products from shop page
        add_action('pre_get_posts', array($this, 'hide_tour_products_from_shop'));
        
        // Custom product type for tours
        add_filter('product_type_selector', array($this, 'add_tour_product_type'));
        add_action('woocommerce_product_options_general_product_data', array($this, 'tour_product_options'));
        add_action('woocommerce_process_product_meta', array($this, 'save_tour_product_options'));
    }
    
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><strong>Seyahat Acentesi Marketplace:</strong> Bu eklenti WooCommerce eklentisinin aktif olmasını gerektirir. Lütfen WooCommerce'i yükleyin ve aktif hale getirin.</p>
        </div>
        <?php
    }
    
    public function create_tour_product($post_id) {
        // Only create product for published tours
        if (get_post_status($post_id) !== 'publish') {
            return;
        }
        
        $tour = get_post($post_id);
        if (!$tour || $tour->post_type !== 'tour') {
            return;
        }
        
        // Check if product already exists
        $existing_product_id = get_post_meta($post_id, '_wc_product_id', true);
        if ($existing_product_id && get_post($existing_product_id)) {
            $this->update_tour_product($post_id, $existing_product_id);
            return;
        }
        
        // Get tour data
        $tour_price = get_tour_price($post_id);
        $tour_location = get_tour_location($post_id);
        $tour_duration = get_tour_duration($post_id);
        $tour_max_participants = get_tour_max_participants($post_id);
        
        // Create WooCommerce product
        $product_data = array(
            'post_title' => $tour->post_title,
            'post_content' => $tour->post_content,
            'post_excerpt' => $tour->post_excerpt,
            'post_status' => 'publish',
            'post_type' => 'product',
            'post_author' => $tour->post_author,
        );
        
        $product_id = wp_insert_post($product_data);
        
        if ($product_id && !is_wp_error($product_id)) {
            // Set product meta
            update_post_meta($product_id, '_tour_id', $post_id);
            update_post_meta($product_id, '_regular_price', $tour_price);
            update_post_meta($product_id, '_price', $tour_price);
            update_post_meta($product_id, '_manage_stock', 'yes');
            update_post_meta($product_id, '_stock', $tour_max_participants ?: 100);
            update_post_meta($product_id, '_stock_status', 'instock');
            update_post_meta($product_id, '_sold_individually', 'no');
            update_post_meta($product_id, '_virtual', 'yes');
            update_post_meta($product_id, '_product_type', 'tour');
            
            // Copy featured image
            if (has_post_thumbnail($post_id)) {
                $thumbnail_id = get_post_thumbnail_id($post_id);
                set_post_thumbnail($product_id, $thumbnail_id);
            }
            
            // Copy tour categories to product categories
            $tour_categories = get_the_terms($post_id, 'tour_category');
            if ($tour_categories && !is_wp_error($tour_categories)) {
                $product_categories = array();
                foreach ($tour_categories as $tour_cat) {
                    // Create or get corresponding product category
                    $product_cat = $this->get_or_create_product_category($tour_cat);
                    if ($product_cat) {
                        $product_categories[] = $product_cat->term_id;
                    }
                }
                wp_set_object_terms($product_id, $product_categories, 'product_cat');
            }
            
            // Link tour to product
            update_post_meta($post_id, '_wc_product_id', $product_id);
            
            // Set product visibility
            $term_ids = array();
            $visibility_term = get_term_by('name', 'exclude-from-catalog', 'product_visibility');
            if ($visibility_term) {
                $term_ids[] = $visibility_term->term_id;
                wp_set_object_terms($product_id, $term_ids, 'product_visibility');
            }
        }
    }
    
    public function update_tour_product($tour_id, $product_id) {
        $tour = get_post($tour_id);
        $tour_price = get_tour_price($tour_id);
        $tour_max_participants = get_tour_max_participants($tour_id);
        
        // Update product data
        wp_update_post(array(
            'ID' => $product_id,
            'post_title' => $tour->post_title,
            'post_content' => $tour->post_content,
            'post_excerpt' => $tour->post_excerpt,
        ));
        
        // Update product meta
        update_post_meta($product_id, '_regular_price', $tour_price);
        update_post_meta($product_id, '_price', $tour_price);
        update_post_meta($product_id, '_stock', $tour_max_participants ?: 100);
        
        // Update featured image
        if (has_post_thumbnail($tour_id)) {
            $thumbnail_id = get_post_thumbnail_id($tour_id);
            set_post_thumbnail($product_id, $thumbnail_id);
        }
    }
    
    public function trash_tour_product($post_id) {
        if (get_post_type($post_id) === 'tour') {
            $product_id = get_post_meta($post_id, '_wc_product_id', true);
            if ($product_id) {
                wp_trash_post($product_id);
            }
        }
    }
    
    public function get_or_create_product_category($tour_category) {
        // Check if corresponding product category exists
        $product_cat = get_term_by('slug', $tour_category->slug, 'product_cat');
        
        if (!$product_cat) {
            // Create new product category
            $result = wp_insert_term(
                $tour_category->name,
                'product_cat',
                array(
                    'description' => $tour_category->description,
                    'slug' => $tour_category->slug,
                )
            );
            
            if (!is_wp_error($result)) {
                $product_cat = get_term($result['term_id'], 'product_cat');
            }
        }
        
        return $product_cat;
    }
    
    public function add_tour_cart_item_data($cart_item_data, $product_id, $variation_id) {
        $tour_id = get_post_meta($product_id, '_tour_id', true);
        
        if ($tour_id && isset($_POST['booking_date']) && isset($_POST['participants'])) {
            $cart_item_data['tour_data'] = array(
                'tour_id' => $tour_id,
                'booking_date' => sanitize_text_field($_POST['booking_date']),
                'participants' => intval($_POST['participants']),
                'special_requests' => sanitize_textarea_field($_POST['special_requests'] ?? ''),
            );
            
            // Make each cart item unique
            $cart_item_data['unique_key'] = md5(microtime().rand());
        }
        
        return $cart_item_data;
    }
    
    public function display_tour_cart_item_data($item_data, $cart_item) {
        if (isset($cart_item['tour_data'])) {
            $tour_data = $cart_item['tour_data'];
            
            $item_data[] = array(
                'name' => 'Seyahat Tarihi',
                'value' => date('d.m.Y', strtotime($tour_data['booking_date'])),
            );
            
            $item_data[] = array(
                'name' => 'Katılımcı Sayısı',
                'value' => $tour_data['participants'] . ' kişi',
            );
            
            if (!empty($tour_data['special_requests'])) {
                $item_data[] = array(
                    'name' => 'Özel İstekler',
                    'value' => $tour_data['special_requests'],
                );
            }
        }
        
        return $item_data;
    }
    
    public function save_tour_order_item_data($item, $cart_item_key, $values, $order) {
        if (isset($values['tour_data'])) {
            $tour_data = $values['tour_data'];
            
            $item->add_meta_data('_tour_id', $tour_data['tour_id']);
            $item->add_meta_data('_booking_date', $tour_data['booking_date']);
            $item->add_meta_data('_participants', $tour_data['participants']);
            
            if (!empty($tour_data['special_requests'])) {
                $item->add_meta_data('_special_requests', $tour_data['special_requests']);
            }
        }
    }
    
    public function handle_completed_order($order_id) {
        $order = wc_get_order($order_id);
        
        foreach ($order->get_items() as $item_id => $item) {
            $tour_id = $item->get_meta('_tour_id');
            
            if ($tour_id) {
                // Create booking record
                $this->create_booking_record($order, $item);
                
                // Send confirmation emails
                $this->send_booking_confirmation($order, $item);
            }
        }
    }
    
    public function handle_cancelled_order($order_id) {
        $order = wc_get_order($order_id);
        
        foreach ($order->get_items() as $item_id => $item) {
            $tour_id = $item->get_meta('_tour_id');
            
            if ($tour_id) {
                // Update booking status
                $this->cancel_booking_record($order, $item);
            }
        }
    }
    
    private function create_booking_record($order, $item) {
        global $wpdb;
        
        $tour_id = $item->get_meta('_tour_id');
        $booking_date = $item->get_meta('_booking_date');
        $participants = $item->get_meta('_participants');
        $special_requests = $item->get_meta('_special_requests');
        
        $table_name = $wpdb->prefix . 'tour_bookings';
        
        $wpdb->insert(
            $table_name,
            array(
                'tour_id' => $tour_id,
                'user_id' => $order->get_user_id(),
                'travel_date' => $booking_date,
                'participants' => $participants,
                'total_price' => $item->get_total(),
                'status' => 'confirmed',
                'payment_status' => 'completed',
                'special_requests' => $special_requests,
                'booking_date' => current_time('mysql'),
            ),
            array('%d', '%d', '%s', '%d', '%f', '%s', '%s', '%s', '%s')
        );
    }
    
    private function cancel_booking_record($order, $item) {
        global $wpdb;
        
        $tour_id = $item->get_meta('_tour_id');
        $table_name = $wpdb->prefix . 'tour_bookings';
        
        $wpdb->update(
            $table_name,
            array('status' => 'cancelled'),
            array(
                'tour_id' => $tour_id,
                'user_id' => $order->get_user_id(),
            ),
            array('%s'),
            array('%d', '%d')
        );
    }
    
    private function send_booking_confirmation($order, $item) {
        $tour_id = $item->get_meta('_tour_id');
        $tour = get_post($tour_id);
        $customer_email = $order->get_billing_email();
        
        $subject = 'Tur Rezervasyonu Onaylandı - ' . $tour->post_title;
        $message = "Merhaba,\n\n";
        $message .= "Tur rezervasyonunuz başarıyla onaylanmıştır.\n\n";
        $message .= "Tur: " . $tour->post_title . "\n";
        $message .= "Seyahat Tarihi: " . date('d.m.Y', strtotime($item->get_meta('_booking_date'))) . "\n";
        $message .= "Katılımcı Sayısı: " . $item->get_meta('_participants') . " kişi\n";
        $message .= "Toplam Tutar: ₺" . number_format($item->get_total(), 2, ',', '.') . "\n\n";
        $message .= "İyi tatiller dileriz!";
        
        wp_mail($customer_email, $subject, $message);
    }
    
    public function custom_add_to_cart_text($text) {
        global $product;
        
        if ($product && get_post_meta($product->get_id(), '_tour_id', true)) {
            return 'Rezervasyon Yap';
        }
        
        return $text;
    }
    
    public function hide_tour_products_from_shop($query) {
        if (!is_admin() && $query->is_main_query()) {
            if (is_shop() || is_product_category() || is_product_tag()) {
                $meta_query = $query->get('meta_query');
                $meta_query[] = array(
                    'key' => '_tour_id',
                    'compare' => 'NOT EXISTS',
                );
                $query->set('meta_query', $meta_query);
            }
        }
    }
    
    public function add_tour_product_type($types) {
        $types['tour'] = 'Tour Product';
        return $types;
    }
    
    public function tour_product_options() {
        global $post;
        
        $tour_id = get_post_meta($post->ID, '_tour_id', true);
        
        if ($tour_id) {
            echo '<div class="options_group">';
            echo '<p><strong>Bu ürün bir tur ürünüdür.</strong></p>';
            echo '<p>Tur ID: <a href="' . get_edit_post_link($tour_id) . '">' . $tour_id . '</a></p>';
            echo '</div>';
        }
    }
    
    public function save_tour_product_options($post_id) {
        // Tour products are managed automatically
    }
}

// Initialize WooCommerce integration
new SAM_WooCommerce_Integration();
?>