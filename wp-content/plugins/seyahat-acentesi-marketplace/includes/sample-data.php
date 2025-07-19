<?php
/**
 * Sample Data Installer for Seyahat Acentesi Marketplace
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SAM_Sample_Data_Installer {
    
    public function __construct() {
        add_action('admin_init', array($this, 'check_install_sample_data'));
    }
    
    public function check_install_sample_data() {
        if (isset($_GET['install_sample_data']) && current_user_can('manage_options')) {
            $this->install_sample_data();
            wp_redirect(admin_url('admin.php?page=seyahat-acentesi-marketplace&sample_data_installed=1'));
            exit;
        }
    }
    
    public function install_sample_data() {
        // Create sample tour categories
        $this->create_sample_categories();
        
        // Create sample pages
        $this->create_sample_pages();
        
        // Create sample tours
        $this->create_sample_tours();
        
        // Mark sample data as installed
        update_option('sam_sample_data_installed', true);
    }
    
    private function create_sample_categories() {
        $categories = array(
            array(
                'name' => 'Yurtiçi Turlar',
                'slug' => 'yurtici-turlar',
                'description' => 'Türkiye içindeki muhteşem destinasyonları keşfedin'
            ),
            array(
                'name' => 'Yurtdışı Turlar', 
                'slug' => 'yurtdisi-turlar',
                'description' => 'Dünyanın dört bir yanındaki eşsiz deneyimler'
            ),
            array(
                'name' => 'Yunan Adaları',
                'slug' => 'yunan-adalari', 
                'description' => 'Ege\'nin büyülü adalarında unutulmaz tatil'
            ),
            array(
                'name' => 'Kültür Turları',
                'slug' => 'kultur-turlari',
                'description' => 'Tarihi ve kültürel zenginlikleri keşfedin'
            ),
            array(
                'name' => 'Doğa Turları',
                'slug' => 'doga-turlari', 
                'description' => 'Doğanın güzelliklerinde huzur bulun'
            ),
            array(
                'name' => 'Balayı Turları',
                'slug' => 'balayi-turlari',
                'description' => 'Romantik anlar için özel turlar'
            )
        );
        
        foreach ($categories as $category) {
            if (!term_exists($category['slug'], 'tour_category')) {
                wp_insert_term(
                    $category['name'],
                    'tour_category',
                    array(
                        'slug' => $category['slug'],
                        'description' => $category['description']
                    )
                );
            }
        }
    }
    
    private function create_sample_pages() {
        $pages = array(
            array(
                'title' => 'Partner Kayıt',
                'slug' => 'partner-kayit',
                'content' => '[partner_registration_form]',
                'template' => 'page-partner-kayit.php'
            ),
            array(
                'title' => 'Partner Dashboard',
                'slug' => 'partner-dashboard', 
                'content' => '[partner_dashboard]',
                'template' => 'page-partner-dashboard.php'
            ),
            array(
                'title' => 'İletişim',
                'slug' => 'iletisim',
                'content' => '<h2>İletişim Bilgileri</h2>
                             <p><strong>Adres:</strong> İstanbul, Türkiye</p>
                             <p><strong>Telefon:</strong> 0850 123 45 67</p>
                             <p><strong>E-posta:</strong> info@seyahatacentesi.com</p>
                             <p><strong>Çalışma Saatleri:</strong> Pazartesi - Cuma: 09:00 - 18:00</p>'
            ),
            array(
                'title' => 'Hakkımızda',
                'slug' => 'hakkimizda',
                'content' => '<h2>Seyahat Acentesi Marketplace</h2>
                             <p>Türkiye\'nin en güvenilir seyahat acentesi pazaryeri olarak, müşterilerimize en kaliteli tatil deneyimlerini sunmak için çalışıyoruz.</p>
                             <p>Platformumuzda, TÜRSAB üyesi güvenilir acentelerle buluşarak, hayalinizdeki tatili planlayabilirsiniz.</p>'
            ),
            array(
                'title' => 'Kullanım Şartları',
                'slug' => 'kullanim-sartlari',
                'content' => '<h2>Kullanım Şartları</h2>
                             <p>Bu sayfa kullanım şartlarını içermektedir...</p>'
            ),
            array(
                'title' => 'Gizlilik Politikası',
                'slug' => 'gizlilik-politikasi',
                'content' => '<h2>Gizlilik Politikası</h2>
                             <p>Bu sayfa gizlilik politikasını içermektedir...</p>'
            ),
            array(
                'title' => 'Sıkça Sorulan Sorular',
                'slug' => 'sss',
                'content' => '<h2>Sıkça Sorulan Sorular</h2>
                             <h3>Rezervasyon nasıl yapılır?</h3>
                             <p>İstediğiniz turu seçtikten sonra "Rezervasyon Yap" butonuna tıklayarak işlemi tamamlayabilirsiniz.</p>'
            )
        );
        
        foreach ($pages as $page_data) {
            // Check if page already exists
            $existing_page = get_page_by_path($page_data['slug']);
            
            if (!$existing_page) {
                $page_id = wp_insert_post(array(
                    'post_title' => $page_data['title'],
                    'post_name' => $page_data['slug'],
                    'post_content' => $page_data['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page'
                ));
                
                if (isset($page_data['template'])) {
                    update_post_meta($page_id, '_wp_page_template', $page_data['template']);
                }
            }
        }
    }
    
    private function create_sample_tours() {
        // First, create a sample partner user if not exists
        $partner_user = get_user_by('email', 'partner@example.com');
        
        if (!$partner_user) {
            $partner_user_id = wp_create_user('sample_partner', 'partner123', 'partner@example.com');
            $partner_user = new WP_User($partner_user_id);
            $partner_user->set_role('partner');
            
            // Update user meta
            update_user_meta($partner_user_id, 'first_name', 'Örnek');
            update_user_meta($partner_user_id, 'last_name', 'Acente');
            update_user_meta($partner_user_id, 'display_name', 'Örnek Seyahat Acentesi');
            
            // Create partner application
            global $wpdb;
            $table_name = $wpdb->prefix . 'partner_applications';
            
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $partner_user_id,
                    'company_name' => 'Örnek Seyahat Acentesi',
                    'tax_number' => '1234567890',
                    'phone' => '0212 123 45 67',
                    'address' => 'İstanbul, Türkiye',
                    'website' => 'https://ornekacente.com',
                    'description' => 'Uzun yıllardır seyahat sektöründe hizmet veren deneyimli ekibimizle kaliteli hizmet sunuyoruz.',
                    'status' => 'approved',
                    'reviewed_date' => current_time('mysql'),
                    'reviewed_by' => 1
                )
            );
        } else {
            $partner_user_id = $partner_user->ID;
        }
        
        // Sample tours data
        $tours = array(
            array(
                'title' => 'Kapadokya Balon Turu',
                'content' => '<p>Kapadokya\'nın eşsiz manzarasını sıcak hava balonuyla keşfedin. Gün doğumunda gerçekleşen bu unutulmaz deneyim, size masalsı anlar yaşatacak.</p>
                             <h3>Tur Programı</h3>
                             <ul>
                                 <li>04:00 - Otellerden alış</li>
                                 <li>05:00 - Balon hazırlıkları</li>
                                 <li>05:30 - Uçuş başlangıcı</li>
                                 <li>07:00 - İniş ve şampanya</li>
                                 <li>08:00 - Otellere dönüş</li>
                             </ul>',
                'excerpt' => 'Kapadokya\'nın büyülü manzarasını sıcak hava balonuyla keşfedin',
                'category' => 'yurtici-turlar',
                'price' => 150,
                'duration' => 1,
                'location' => 'Kapadokya, Nevşehir',
                'max_participants' => 20,
                'start_date' => date('Y-m-d', strtotime('+7 days'))
            ),
            array(
                'title' => 'Santorini Adası 3 Gün',
                'content' => '<p>Yunanistan\'ın en güzel adası Santorini\'de 3 gün 2 gece unutulmaz bir tatil.</p>
                             <h3>Paket İçeriği</h3>
                             <ul>
                                 <li>Uçak bileti (gidiş-dönüş)</li>
                                 <li>2 gece konaklama</li>
                                 <li>Kahvaltı dahil</li>
                                 <li>Havaalanı transferi</li>
                                 <li>Oia sunset turu</li>
                             </ul>',
                'excerpt' => 'Santorini adasında büyülü bir tatil deneyimi',
                'category' => 'yunan-adalari',
                'price' => 850,
                'duration' => 3,
                'location' => 'Santorini, Yunanistan',
                'max_participants' => 25,
                'start_date' => date('Y-m-d', strtotime('+14 days'))
            ),
            array(
                'title' => 'İstanbul Boğaz Turu',
                'content' => '<p>İstanbul\'un tarihi güzelliklerini Boğaz turu ile keşfedin. Asya ve Avrupa\'yı birbirinden ayıran İstanbul Boğazı\'nın eşsiz manzarasını denizden izleyin.</p>
                             <h3>Görülecek Yerler</h3>
                             <ul>
                                 <li>Dolmabahçe Sarayı</li>
                                 <li>Ortaköy Camii</li>
                                 <li>Boğaz Köprüsü</li>
                                 <li>Rumeli Hisarı</li>
                                 <li>Bebek Koyu</li>
                             </ul>',
                'excerpt' => 'İstanbul Boğazı\'nın güzelliklerini denizden keşfedin',
                'category' => 'yurtici-turlar',
                'price' => 45,
                'duration' => 1,
                'location' => 'İstanbul',
                'max_participants' => 50,
                'start_date' => date('Y-m-d', strtotime('+3 days'))
            ),
            array(
                'title' => 'Pamukkale Travertenleri Günübirlik',
                'content' => '<p>Dünya mirası Pamukkale travertenlerinde unutulmaz bir gün geçirin. Hierapolis antik kenti ile birlikte bu eşsiz doğa harikasını keşfedin.</p>
                             <h3>Program</h3>
                             <ul>
                                 <li>06:00 - İstanbul\'dan hareket</li>
                                 <li>11:00 - Pamukkale\'ye varış</li>
                                 <li>11:30 - Travertenler gezisi</li>
                                 <li>13:00 - Öğle yemeği</li>
                                 <li>14:00 - Hierapolis antik kenti</li>
                                 <li>16:00 - İstanbul\'a dönüş</li>
                             </ul>',
                'excerpt' => 'Beyaz cennet Pamukkale\'yi bir günde keşfedin',
                'category' => 'yurtici-turlar',
                'price' => 120,
                'duration' => 1,
                'location' => 'Pamukkale, Denizli',
                'max_participants' => 40,
                'start_date' => date('Y-m-d', strtotime('+10 days'))
            ),
            array(
                'title' => 'Paris Romantik 5 Gün',
                'content' => '<p>Aşk şehri Paris\'te romantik bir tatil için özel olarak hazırlanmış 5 günlük balayı paketi.</p>
                             <h3>Özel Hizmetler</h3>
                             <ul>
                                 <li>4 yıldızlı otel konaklaması</li>
                                 <li>Seine Nehri cruise</li>
                                 <li>Eiffel Kulesi gece turu</li>
                                 <li>Romantik akşam yemeği</li>
                                 <li>Versay Sarayı gezisi</li>
                             </ul>',
                'excerpt' => 'Aşk şehri Paris\'te unutulmaz balayı tatili',
                'category' => 'balayi-turlari',
                'price' => 1250,
                'duration' => 5,
                'location' => 'Paris, Fransa',
                'max_participants' => 2,
                'start_date' => date('Y-m-d', strtotime('+21 days'))
            ),
            array(
                'title' => 'Efes Antik Kenti Kültür Turu',
                'content' => '<p>Dünyanın en iyi korunmuş antik kentlerinden biri olan Efes\'te tarih yolculuğuna çıkın.</p>
                             <h3>Gezilecek Yerler</h3>
                             <ul>
                                 <li>Efes Antik Kenti</li>
                                 <li>Artemis Tapınağı</li>
                                 <li>Meryem Ana Evi</li>
                                 <li>Şirince Köyü</li>
                                 <li>İzmir Arkeoloji Müzesi</li>
                             </ul>',
                'excerpt' => 'Efes antik kentinde tarihi bir yolculuk',
                'category' => 'kultur-turlari',
                'price' => 85,
                'duration' => 1,
                'location' => 'Efes, İzmir',
                'max_participants' => 35,
                'start_date' => date('Y-m-d', strtotime('+5 days'))
            )
        );
        
        foreach ($tours as $tour_data) {
            // Check if tour already exists
            $existing_tour = get_page_by_title($tour_data['title'], OBJECT, 'tour');
            
            if (!$existing_tour) {
                // Create tour post
                $tour_id = wp_insert_post(array(
                    'post_title' => $tour_data['title'],
                    'post_content' => $tour_data['content'],
                    'post_excerpt' => $tour_data['excerpt'],
                    'post_status' => 'publish',
                    'post_type' => 'tour',
                    'post_author' => $partner_user_id
                ));
                
                if ($tour_id && !is_wp_error($tour_id)) {
                    // Set tour meta
                    update_post_meta($tour_id, '_tour_price', $tour_data['price']);
                    update_post_meta($tour_id, '_tour_duration', $tour_data['duration']);
                    update_post_meta($tour_id, '_tour_location', $tour_data['location']);
                    update_post_meta($tour_id, '_tour_max_participants', $tour_data['max_participants']);
                    update_post_meta($tour_id, '_tour_start_date', $tour_data['start_date']);
                    update_post_meta($tour_id, '_tour_agency_id', $partner_user_id);
                    
                    // Set tour category
                    $category = get_term_by('slug', $tour_data['category'], 'tour_category');
                    if ($category) {
                        wp_set_object_terms($tour_id, array($category->term_id), 'tour_category');
                    }
                }
            }
        }
    }
    
    public function show_install_notice() {
        if (current_user_can('manage_options') && !get_option('sam_sample_data_installed')) {
            ?>
            <div class="notice notice-info">
                <p>
                    <strong>Seyahat Acentesi Marketplace:</strong> 
                    Örnek verilerle sistemi test etmek ister misiniz? 
                    <a href="<?php echo admin_url('admin.php?page=seyahat-acentesi-marketplace&install_sample_data=1'); ?>" class="button button-primary">Örnek Verileri Yükle</a>
                </p>
            </div>
            <?php
        }
        
        if (isset($_GET['sample_data_installed'])) {
            ?>
            <div class="notice notice-success">
                <p><strong>Başarılı!</strong> Örnek veriler başarıyla yüklendi. Şimdi sistemi test edebilirsiniz.</p>
            </div>
            <?php
        }
    }
}

// Initialize sample data installer
$sam_sample_data = new SAM_Sample_Data_Installer();
add_action('admin_notices', array($sam_sample_data, 'show_install_notice'));
?>