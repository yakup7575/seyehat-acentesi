<div class="partner-dashboard">
    <?php
    $current_user = wp_get_current_user();
    $user_application = sam_get_user_application($current_user->ID);
    
    // Check if user is approved partner
    if (!$user_application || $user_application->status !== 'approved') {
        ?>
        <div class="notice notice-warning">
            <h3>Partner Onayı Bekleniyor</h3>
            <p>Partner dashboard'a erişmek için önce partner başvurunuzun onaylanması gerekmektedir.</p>
            <a href="<?php echo home_url('/partner-kayit'); ?>" class="btn">Başvuru Durumunu Kontrol Et</a>
        </div>
        <?php
        return;
    }
    ?>
    
    <div class="dashboard-header">
        <div>
            <h1>Partner Dashboard</h1>
            <p>Hoş geldiniz, <strong><?php echo esc_html($current_user->display_name); ?></strong></p>
        </div>
        <div class="header-actions">
            <a href="<?php echo admin_url('post-new.php?post_type=tour'); ?>" class="btn">+ Yeni Tur Ekle</a>
        </div>
    </div>
    
    <div class="dashboard-stats">
        <?php
        // Get partner statistics
        $user_tours = new WP_Query(array(
            'post_type' => 'tour',
            'author' => $current_user->ID,
            'posts_per_page' => -1,
            'post_status' => array('publish', 'draft', 'pending')
        ));
        
        $published_tours = new WP_Query(array(
            'post_type' => 'tour',
            'author' => $current_user->ID,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        $draft_tours = new WP_Query(array(
            'post_type' => 'tour',
            'author' => $current_user->ID,
            'posts_per_page' => -1,
            'post_status' => 'draft'
        ));
        ?>
        
        <div class="stat-card">
            <div class="stat-number" style="color: #2c5aa0;"><?php echo $user_tours->found_posts; ?></div>
            <div class="stat-label">Toplam Tur</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number" style="color: #10b981;"><?php echo $published_tours->found_posts; ?></div>
            <div class="stat-label">Yayında</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number" style="color: #f59e0b;"><?php echo $draft_tours->found_posts; ?></div>
            <div class="stat-label">Taslak</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number" style="color: #8b5cf6;">0</div>
            <div class="stat-label">Bu Ay Rezervasyon</div>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="main-content">
            <div class="dashboard-section">
                <h2>Turlarım</h2>
                
                <?php if ($user_tours->have_posts()) : ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tur Adı</th>
                                <th>Fiyat</th>
                                <th>Durum</th>
                                <th>Oluşturma Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user_tours->have_posts()) : $user_tours->the_post(); 
                                $tour_price = get_tour_price(get_the_ID());
                            ?>
                            <tr>
                                <td>
                                    <strong><?php the_title(); ?></strong>
                                    <?php if (has_post_thumbnail()) : ?>
                                        <br><small>Resim: ✅</small>
                                    <?php else : ?>
                                        <br><small style="color: #f59e0b;">Resim: ❌</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $tour_price ? '₺' . number_format($tour_price, 0, ',', '.') : '-'; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo get_post_status() === 'publish' ? 'status-approved' : 'status-pending'; ?>">
                                        <?php
                                        switch (get_post_status()) {
                                            case 'publish': echo 'Yayında'; break;
                                            case 'draft': echo 'Taslak'; break;
                                            case 'pending': echo 'İncelemede'; break;
                                            default: echo get_post_status(); break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo get_the_date('d.m.Y'); ?></td>
                                <td>
                                    <a href="<?php echo get_edit_post_link(); ?>" class="button button-small">Düzenle</a>
                                    <a href="<?php the_permalink(); ?>" class="button button-small" target="_blank">Görüntüle</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div style="text-align: center; padding: 2rem;">
                        <p>Henüz tur eklememişsiniz.</p>
                        <a href="<?php echo admin_url('post-new.php?post_type=tour'); ?>" class="btn">İlk Turunuzu Ekleyin</a>
                    </div>
                <?php endif; ?>
                
                <?php wp_reset_postdata(); ?>
            </div>
            
            <div class="dashboard-section">
                <h2>Son Rezervasyonlar</h2>
                <div style="text-align: center; padding: 2rem; color: #666;">
                    <p>Rezervasyon sistemi WooCommerce entegrasyonu ile aktif hale gelecektir.</p>
                </div>
            </div>
        </div>
        
        <div class="sidebar">
            <div class="dashboard-section">
                <h3>Hızlı İşlemler</h3>
                <div class="quick-actions">
                    <a href="<?php echo admin_url('post-new.php?post_type=tour'); ?>" class="btn">Yeni Tur Ekle</a>
                    <a href="<?php echo admin_url('edit.php?post_type=tour'); ?>" class="btn btn-secondary">Tüm Turlarım</a>
                    <a href="<?php echo admin_url('upload.php'); ?>" class="btn btn-secondary">Medya Kütüphanesi</a>
                    <a href="<?php echo admin_url('profile.php'); ?>" class="btn btn-secondary">Profil Ayarları</a>
                </div>
            </div>
            
            <div class="dashboard-section">
                <h3>Partner Bilgileri</h3>
                <div class="partner-info">
                    <p><strong>Şirket:</strong> <?php echo esc_html($user_application->company_name); ?></p>
                    <p><strong>Telefon:</strong> <?php echo esc_html($user_application->phone); ?></p>
                    <p><strong>Onay Tarihi:</strong> <?php echo date('d.m.Y', strtotime($user_application->reviewed_date)); ?></p>
                </div>
            </div>
            
            <div class="dashboard-section">
                <h3>Destek</h3>
                <div class="support-info">
                    <p><strong>📞 Teknik Destek:</strong> 0850 123 45 67</p>
                    <p><strong>✉️ E-posta:</strong> destek@seyahatacentesi.com</p>
                    <p><strong>🕒 Çalışma Saatleri:</strong> 09:00 - 18:00</p>
                    
                    <div style="margin-top: 1rem;">
                        <a href="<?php echo home_url('/sss'); ?>" class="btn btn-secondary" style="width: 100%;">Sıkça Sorulan Sorular</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>