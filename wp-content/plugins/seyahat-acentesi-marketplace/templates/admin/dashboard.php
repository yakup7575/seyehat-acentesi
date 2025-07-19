<div class="wrap">
    <h1>Seyahat Acentesi Marketplace - Yönetim Paneli</h1>
    
    <div class="admin-panel">
        <div class="dashboard-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            
            <?php
            // Get statistics
            global $wpdb;
            
            // Partner applications count
            $applications_table = $wpdb->prefix . 'partner_applications';
            $pending_applications = $wpdb->get_var("SELECT COUNT(*) FROM $applications_table WHERE status = 'pending'");
            $total_partners = $wpdb->get_var("SELECT COUNT(*) FROM $applications_table WHERE status = 'approved'");
            
            // Tours count
            $tours_count = wp_count_posts('tour');
            $total_tours = $tours_count->publish;
            $draft_tours = $tours_count->draft;
            
            // Users count
            $user_count = count_users();
            $total_users = $user_count['total_users'];
            ?>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #f59e0b;"><?php echo $pending_applications; ?></div>
                <div class="stat-label" style="color: #666;">Bekleyen Başvuru</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #10b981;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #10b981;"><?php echo $total_partners; ?></div>
                <div class="stat-label" style="color: #666;">Onaylı Partner</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #3b82f6;"><?php echo $total_tours; ?></div>
                <div class="stat-label" style="color: #666;">Yayınlanan Tur</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #8b5cf6;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #8b5cf6;"><?php echo $total_users; ?></div>
                <div class="stat-label" style="color: #666;">Toplam Kullanıcı</div>
            </div>
        </div>
        
        <div class="dashboard-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <div class="main-content">
                <div class="panel-section" style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h2>Son Partner Başvuruları</h2>
                    
                    <?php
                    $recent_applications = sam_get_partner_applications('pending');
                    $recent_applications = array_slice($recent_applications, 0, 5);
                    
                    if (!empty($recent_applications)) :
                    ?>
                        <table class="data-table" style="width: 100%; margin-top: 1rem;">
                            <thead>
                                <tr>
                                    <th>Şirket Adı</th>
                                    <th>Başvuru Tarihi</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_applications as $application) : 
                                    $user = get_userdata($application->user_id);
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($application->company_name); ?></strong><br>
                                        <small><?php echo esc_html($user->display_name); ?></small>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($application->applied_date)); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo esc_attr($application->status); ?>">
                                            <?php
                                            switch ($application->status) {
                                                case 'pending': echo 'Beklemede'; break;
                                                case 'approved': echo 'Onaylandı'; break;
                                                case 'rejected': echo 'Reddedildi'; break;
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo admin_url('admin.php?page=partner-applications&action=view&id=' . $application->id); ?>" class="button button-small">İncele</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div style="margin-top: 1rem;">
                            <a href="<?php echo admin_url('admin.php?page=partner-applications'); ?>" class="button">Tüm Başvuruları Görüntüle</a>
                        </div>
                    <?php else : ?>
                        <p>Henüz bekleyen partner başvurusu bulunmuyor.</p>
                    <?php endif; ?>
                </div>
                
                <div class="panel-section" style="background: white; padding: 1.5rem; border-radius: 8px;">
                    <h2>Son Eklenen Turlar</h2>
                    
                    <?php
                    $recent_tours = new WP_Query(array(
                        'post_type' => 'tour',
                        'posts_per_page' => 5,
                        'post_status' => array('publish', 'draft'),
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    if ($recent_tours->have_posts()) :
                    ?>
                        <table class="data-table" style="width: 100%; margin-top: 1rem;">
                            <thead>
                                <tr>
                                    <th>Tur Adı</th>
                                    <th>Partner</th>
                                    <th>Fiyat</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($recent_tours->have_posts()) : $recent_tours->the_post();
                                    $tour_price = get_tour_price(get_the_ID());
                                    $agency_id = get_tour_agency_id(get_the_ID());
                                    $agency_name = $agency_id ? get_the_author_meta('display_name', $agency_id) : get_the_author();
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php the_title(); ?></strong><br>
                                        <small><?php echo get_the_date('d.m.Y H:i'); ?></small>
                                    </td>
                                    <td><?php echo esc_html($agency_name); ?></td>
                                    <td><?php echo $tour_price ? '₺' . number_format($tour_price, 0, ',', '.') : '-'; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo get_post_status() === 'publish' ? 'status-approved' : 'status-pending'; ?>">
                                            <?php echo get_post_status() === 'publish' ? 'Yayında' : 'Taslak'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo get_edit_post_link(); ?>" class="button button-small">Düzenle</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        
                        <div style="margin-top: 1rem;">
                            <a href="<?php echo admin_url('edit.php?post_type=tour'); ?>" class="button">Tüm Turları Görüntüle</a>
                        </div>
                    <?php 
                        wp_reset_postdata();
                    else : 
                    ?>
                        <p>Henüz tur eklenmemiş.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="sidebar">
                <div class="panel-section" style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3>Hızlı İşlemler</h3>
                    <div class="quick-actions" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="<?php echo admin_url('admin.php?page=partner-applications'); ?>" class="button button-primary">Partner Başvurularını İncele</a>
                        <a href="<?php echo admin_url('edit.php?post_type=tour'); ?>" class="button">Turları Yönet</a>
                        <a href="<?php echo admin_url('edit-tags.php?taxonomy=tour_category&post_type=tour'); ?>" class="button">Tur Kategorilerini Yönet</a>
                        <a href="<?php echo admin_url('users.php'); ?>" class="button">Kullanıcıları Yönet</a>
                    </div>
                </div>
                
                <div class="panel-section" style="background: white; padding: 1.5rem; border-radius: 8px;">
                    <h3>Sistem Bilgileri</h3>
                    <div class="system-info">
                        <p><strong>WordPress:</strong> <?php echo get_bloginfo('version'); ?></p>
                        <p><strong>Tema:</strong> <?php echo wp_get_theme()->get('Name'); ?></p>
                        <p><strong>Plugin Sürümü:</strong> <?php echo SAM_PLUGIN_VERSION; ?></p>
                        <p><strong>PHP:</strong> <?php echo phpversion(); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>