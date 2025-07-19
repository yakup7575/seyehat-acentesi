<div class="wrap">
    <h1>Marketplace Ayarları</h1>
    
    <?php
    // Handle form submission
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['sam_settings_nonce'], 'sam_settings')) {
        $settings = array(
            'commission_rate' => floatval($_POST['commission_rate']),
            'auto_approve_tours' => isset($_POST['auto_approve_tours']) ? '1' : '0',
            'require_documents' => isset($_POST['require_documents']) ? '1' : '0',
            'email_notifications' => isset($_POST['email_notifications']) ? '1' : '0',
            'site_title' => sanitize_text_field($_POST['site_title']),
            'site_description' => sanitize_textarea_field($_POST['site_description']),
            'contact_email' => sanitize_email($_POST['contact_email']),
            'contact_phone' => sanitize_text_field($_POST['contact_phone']),
            'terms_page' => intval($_POST['terms_page']),
            'privacy_page' => intval($_POST['privacy_page']),
        );
        
        update_option('sam_settings', $settings);
        echo '<div class="notice notice-success"><p>Ayarlar başarıyla kaydedildi.</p></div>';
    }
    
    // Get current settings
    $settings = get_option('sam_settings', array(
        'commission_rate' => 10,
        'auto_approve_tours' => '0',
        'require_documents' => '1',
        'email_notifications' => '1',
        'site_title' => 'Seyahat Acentesi Marketplace',
        'site_description' => 'Türkiye\'nin en büyük seyahat acentesi pazaryeri',
        'contact_email' => get_option('admin_email'),
        'contact_phone' => '0850 123 45 67',
        'terms_page' => 0,
        'privacy_page' => 0,
    ));
    ?>
    
    <div class="admin-panel">
        <form method="post" action="">
            <?php wp_nonce_field('sam_settings', 'sam_settings_nonce'); ?>
            
            <div class="admin-tabs">
                <button type="button" class="admin-tab active" data-tab="general">Genel Ayarlar</button>
                <button type="button" class="admin-tab" data-tab="partners">Partner Ayarları</button>
                <button type="button" class="admin-tab" data-tab="tours">Tur Ayarları</button>
                <button type="button" class="admin-tab" data-tab="notifications">Bildirimler</button>
                <button type="button" class="admin-tab" data-tab="pages">Sayfalar</button>
            </div>
            
            <!-- General Settings -->
            <div id="general-tab" class="tab-content active">
                <h2>Genel Ayarlar</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Site Başlığı</th>
                        <td>
                            <input type="text" name="site_title" value="<?php echo esc_attr($settings['site_title']); ?>" class="regular-text">
                            <p class="description">Marketplace sitenizin başlığı</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Site Açıklaması</th>
                        <td>
                            <textarea name="site_description" rows="3" class="large-text"><?php echo esc_textarea($settings['site_description']); ?></textarea>
                            <p class="description">Sitenizin kısa açıklaması</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">İletişim E-postası</th>
                        <td>
                            <input type="email" name="contact_email" value="<?php echo esc_attr($settings['contact_email']); ?>" class="regular-text">
                            <p class="description">Müşteri hizmetleri e-posta adresi</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">İletişim Telefonu</th>
                        <td>
                            <input type="text" name="contact_phone" value="<?php echo esc_attr($settings['contact_phone']); ?>" class="regular-text">
                            <p class="description">Müşteri hizmetleri telefon numarası</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Partner Settings -->
            <div id="partners-tab" class="tab-content">
                <h2>Partner Ayarları</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Komisyon Oranı (%)</th>
                        <td>
                            <input type="number" name="commission_rate" value="<?php echo esc_attr($settings['commission_rate']); ?>" min="0" max="100" step="0.1" class="small-text">
                            <p class="description">Partnerlerden alınacak komisyon oranı</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Belge Yükleme Zorunluluğu</th>
                        <td>
                            <label>
                                <input type="checkbox" name="require_documents" value="1" <?php checked($settings['require_documents'], '1'); ?>>
                                Partner başvurusu için belge yükleme zorunlu olsun
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Tour Settings -->
            <div id="tours-tab" class="tab-content">
                <h2>Tur Ayarları</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Otomatik Tur Onayı</th>
                        <td>
                            <label>
                                <input type="checkbox" name="auto_approve_tours" value="1" <?php checked($settings['auto_approve_tours'], '1'); ?>>
                                Yeni turlar otomatik olarak onaylansın
                            </label>
                            <p class="description">Bu seçenek aktifse, yeni eklenen turlar admin onayı beklemeden yayınlanır</p>
                        </td>
                    </tr>
                </table>
                
                <h3>Tur Kategorileri</h3>
                <p>Tur kategorilerini yönetmek için <a href="<?php echo admin_url('edit-tags.php?taxonomy=tour_category&post_type=tour'); ?>">buraya tıklayın</a>.</p>
                
                <h3>Varsayılan Kategoriler</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                    <?php
                    $default_categories = array(
                        'Yurtiçi Turlar' => 'Türkiye içi seyahat paketleri',
                        'Yurtdışı Turlar' => 'Uluslararası seyahat paketleri',
                        'Yunan Adaları' => 'Ege denizi ada turları',
                        'Kültür Turları' => 'Tarihi ve kültürel geziler',
                        'Doğa Turları' => 'Doğa ve macera turları',
                        'Balayı Turları' => 'Romantik çift turları'
                    );
                    
                    foreach ($default_categories as $cat_name => $cat_desc) :
                        $existing_cat = get_term_by('name', $cat_name, 'tour_category');
                    ?>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px;">
                            <strong><?php echo esc_html($cat_name); ?></strong>
                            <p style="font-size: 0.9rem; color: #666; margin: 0.5rem 0;"><?php echo esc_html($cat_desc); ?></p>
                            <?php if (!$existing_cat) : ?>
                                <button type="button" onclick="createCategory('<?php echo esc_js($cat_name); ?>', '<?php echo esc_js($cat_desc); ?>')" class="button button-small">Oluştur</button>
                            <?php else : ?>
                                <span style="color: #10b981; font-size: 0.9rem;">✅ Mevcut</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Notification Settings -->
            <div id="notifications-tab" class="tab-content">
                <h2>Bildirim Ayarları</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">E-posta Bildirimleri</th>
                        <td>
                            <label>
                                <input type="checkbox" name="email_notifications" value="1" <?php checked($settings['email_notifications'], '1'); ?>>
                                E-posta bildirimlerini etkinleştir
                            </label>
                        </td>
                    </tr>
                </table>
                
                <h3>Bildirim Türleri</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin: 0.5rem 0;">✉️ Yeni partner başvurusu</li>
                    <li style="margin: 0.5rem 0;">✉️ Partner onay/red durumu</li>
                    <li style="margin: 0.5rem 0;">✉️ Yeni tur eklendi</li>
                    <li style="margin: 0.5rem 0;">✉️ Yeni rezervasyon</li>
                    <li style="margin: 0.5rem 0;">✉️ Ödeme tamamlandı</li>
                </ul>
            </div>
            
            <!-- Pages Settings -->
            <div id="pages-tab" class="tab-content">
                <h2>Sayfa Ayarları</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Kullanım Şartları Sayfası</th>
                        <td>
                            <select name="terms_page">
                                <option value="0">Sayfa Seçin</option>
                                <?php
                                $pages = get_pages();
                                foreach ($pages as $page) {
                                    $selected = selected($settings['terms_page'], $page->ID, false);
                                    echo '<option value="' . $page->ID . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                                }
                                ?>
                            </select>
                            <p class="description">Partner kayıt formunda gösterilecek kullanım şartları sayfası</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Gizlilik Politikası Sayfası</th>
                        <td>
                            <select name="privacy_page">
                                <option value="0">Sayfa Seçin</option>
                                <?php
                                foreach ($pages as $page) {
                                    $selected = selected($settings['privacy_page'], $page->ID, false);
                                    echo '<option value="' . $page->ID . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                                }
                                ?>
                            </select>
                            <p class="description">Partner kayıt formunda gösterilecek gizlilik politikası sayfası</p>
                        </td>
                    </tr>
                </table>
                
                <h3>Gerekli Sayfalar</h3>
                <p>Marketplace'in düzgün çalışması için aşağıdaki sayfaların oluşturulması önerilir:</p>
                <ul>
                    <li><strong>Partner Kayıt:</strong> Shortcode [partner_registration_form]</li>
                    <li><strong>Partner Dashboard:</strong> Shortcode [partner_dashboard]</li>
                    <li><strong>İletişim:</strong> İletişim bilgileri ve form</li>
                    <li><strong>Hakkımızda:</strong> Şirket bilgileri</li>
                    <li><strong>SSS:</strong> Sıkça sorulan sorular</li>
                </ul>
            </div>
            
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button-primary" value="Ayarları Kaydet">
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Tab functionality
    $('.admin-tab').click(function() {
        var tabId = $(this).data('tab');
        
        $('.admin-tab').removeClass('active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('active');
        $('#' + tabId + '-tab').addClass('active');
    });
});

function createCategory(name, description) {
    jQuery.post(ajaxurl, {
        action: 'create_tour_category',
        name: name,
        description: description,
        nonce: '<?php echo wp_create_nonce('sam_admin_nonce'); ?>'
    }, function(response) {
        if (response.success) {
            alert('Kategori başarıyla oluşturuldu: ' + name);
            location.reload();
        } else {
            alert('Kategori oluşturulurken hata: ' + response.data);
        }
    });
}
</script>

<style>
.tab-content {
    display: none;
    padding: 2rem 0;
}

.tab-content.active {
    display: block;
}

.admin-tabs {
    border-bottom: 1px solid #ccd0d4;
    margin-bottom: 0;
}

.admin-tab {
    background: none;
    border: none;
    padding: 1rem 1.5rem;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
}

.admin-tab.active {
    color: #0073aa;
    border-bottom-color: #0073aa;
}
</style>