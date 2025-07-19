<div class="wrap">
    <h1>Partner Başvuruları</h1>
    
    <?php
    if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
        // View specific application
        global $wpdb;
        $application_id = intval($_GET['id']);
        $table_name = $wpdb->prefix . 'partner_applications';
        
        $application = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $application_id
        ));
        
        if ($application) {
            $user = get_userdata($application->user_id);
            $documents = json_decode($application->documents, true);
            ?>
            
            <div class="admin-panel">
                <div style="margin-bottom: 1rem;">
                    <a href="<?php echo admin_url('admin.php?page=partner-applications'); ?>" class="button">&larr; Geri Dön</a>
                </div>
                
                <div style="background: white; padding: 2rem; border-radius: 8px;">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                        <div class="application-details">
                            <h2>Başvuru Detayları</h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th>Başvuru ID</th>
                                    <td><?php echo $application->id; ?></td>
                                </tr>
                                <tr>
                                    <th>Başvuru Tarihi</th>
                                    <td><?php echo date('d.m.Y H:i', strtotime($application->applied_date)); ?></td>
                                </tr>
                                <tr>
                                    <th>Durum</th>
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
                                </tr>
                            </table>
                            
                            <h3>Kullanıcı Bilgileri</h3>
                            <table class="form-table">
                                <tr>
                                    <th>Kullanıcı Adı</th>
                                    <td><?php echo esc_html($user->display_name); ?></td>
                                </tr>
                                <tr>
                                    <th>E-posta</th>
                                    <td><?php echo esc_html($user->user_email); ?></td>
                                </tr>
                                <tr>
                                    <th>Kayıt Tarihi</th>
                                    <td><?php echo date('d.m.Y', strtotime($user->user_registered)); ?></td>
                                </tr>
                            </table>
                            
                            <h3>Şirket Bilgileri</h3>
                            <table class="form-table">
                                <tr>
                                    <th>Şirket Adı</th>
                                    <td><?php echo esc_html($application->company_name); ?></td>
                                </tr>
                                <tr>
                                    <th>Vergi Numarası</th>
                                    <td><?php echo esc_html($application->tax_number); ?></td>
                                </tr>
                                <tr>
                                    <th>Telefon</th>
                                    <td><?php echo esc_html($application->phone); ?></td>
                                </tr>
                                <tr>
                                    <th>Adres</th>
                                    <td><?php echo nl2br(esc_html($application->address)); ?></td>
                                </tr>
                                <tr>
                                    <th>Web Sitesi</th>
                                    <td>
                                        <?php if ($application->website) : ?>
                                            <a href="<?php echo esc_url($application->website); ?>" target="_blank"><?php echo esc_html($application->website); ?></a>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Açıklama</th>
                                    <td><?php echo nl2br(esc_html($application->description)); ?></td>
                                </tr>
                            </table>
                            
                            <?php if ($documents && count($documents) > 0) : ?>
                                <h3>Yüklenen Belgeler</h3>
                                <div class="documents-list">
                                    <?php foreach ($documents as $doc_url) : ?>
                                        <div class="document-item" style="margin: 0.5rem 0;">
                                            <a href="<?php echo esc_url($doc_url); ?>" target="_blank" class="button">📄 Belgeyi Görüntüle</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($application->notes) : ?>
                                <h3>Notlar</h3>
                                <div style="background: #f8f9fa; padding: 1rem; border-radius: 4px;">
                                    <?php echo nl2br(esc_html($application->notes)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="application-actions">
                            <h3>İşlemler</h3>
                            
                            <?php if ($application->status === 'pending') : ?>
                                <div class="action-buttons" style="display: flex; flex-direction: column; gap: 1rem;">
                                    <button type="button" class="button button-primary" onclick="approvePartner(<?php echo $application->id; ?>)">
                                        ✅ Başvuruyu Onayla
                                    </button>
                                    
                                    <button type="button" class="button button-secondary" onclick="showRejectForm(<?php echo $application->id; ?>)">
                                        ❌ Başvuruyu Reddet
                                    </button>
                                </div>
                                
                                <div id="reject-form" style="display: none; margin-top: 1rem;">
                                    <h4>Ret Nedeni</h4>
                                    <textarea id="reject-reason" rows="4" style="width: 100%;" placeholder="Ret nedenini açıklayın..."></textarea>
                                    <div style="margin-top: 1rem;">
                                        <button type="button" class="button button-primary" onclick="rejectPartner(<?php echo $application->id; ?>)">Reddet</button>
                                        <button type="button" class="button" onclick="hideRejectForm()">İptal</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($application->reviewed_date) : ?>
                                <div class="review-info" style="background: #f8f9fa; padding: 1rem; border-radius: 4px; margin-top: 1rem;">
                                    <h4>İnceleme Bilgileri</h4>
                                    <p><strong>İnceleme Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($application->reviewed_date)); ?></p>
                                    <?php if ($application->reviewed_by) : 
                                        $reviewer = get_userdata($application->reviewed_by);
                                    ?>
                                        <p><strong>İnceleyen:</strong> <?php echo esc_html($reviewer->display_name); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
            function approvePartner(applicationId) {
                if (confirm('Bu partner başvurusunu onaylamak istediğinize emin misiniz?')) {
                    jQuery.post(ajaxurl, {
                        action: 'approve_partner',
                        application_id: applicationId,
                        nonce: '<?php echo wp_create_nonce('sam_admin_nonce'); ?>'
                    }, function(response) {
                        if (response.success) {
                            alert(response.data);
                            location.reload();
                        } else {
                            alert('Hata: ' + response.data);
                        }
                    });
                }
            }
            
            function showRejectForm(applicationId) {
                document.getElementById('reject-form').style.display = 'block';
            }
            
            function hideRejectForm() {
                document.getElementById('reject-form').style.display = 'none';
                document.getElementById('reject-reason').value = '';
            }
            
            function rejectPartner(applicationId) {
                const reason = document.getElementById('reject-reason').value;
                if (!reason.trim()) {
                    alert('Lütfen ret nedenini açıklayın.');
                    return;
                }
                
                if (confirm('Bu partner başvurusunu reddetmek istediğinize emin misiniz?')) {
                    jQuery.post(ajaxurl, {
                        action: 'reject_partner',
                        application_id: applicationId,
                        reason: reason,
                        nonce: '<?php echo wp_create_nonce('sam_admin_nonce'); ?>'
                    }, function(response) {
                        if (response.success) {
                            alert(response.data);
                            location.reload();
                        } else {
                            alert('Hata: ' + response.data);
                        }
                    });
                }
            }
            </script>
            
            <?php
        } else {
            echo '<div class="notice notice-error"><p>Başvuru bulunamadı.</p></div>';
        }
    } else {
        // List all applications
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
        $applications = sam_get_partner_applications($status_filter);
        ?>
        
        <div class="admin-panel">
            <div class="filters" style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <form method="get" style="display: flex; gap: 1rem; align-items: center;">
                    <input type="hidden" name="page" value="partner-applications">
                    
                    <label for="status">Durum:</label>
                    <select name="status" id="status">
                        <option value="all" <?php selected($status_filter, 'all'); ?>>Tümü</option>
                        <option value="pending" <?php selected($status_filter, 'pending'); ?>>Beklemede</option>
                        <option value="approved" <?php selected($status_filter, 'approved'); ?>>Onaylandı</option>
                        <option value="rejected" <?php selected($status_filter, 'rejected'); ?>>Reddedildi</option>
                    </select>
                    
                    <button type="submit" class="button">Filtrele</button>
                </form>
            </div>
            
            <?php if (!empty($applications)) : ?>
                <div style="background: white; padding: 1rem; border-radius: 8px;">
                    <table class="data-table widefat">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Şirket Adı</th>
                                <th>Kullanıcı</th>
                                <th>Telefon</th>
                                <th>Başvuru Tarihi</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application) : 
                                $user = get_userdata($application->user_id);
                            ?>
                            <tr>
                                <td><?php echo $application->id; ?></td>
                                <td>
                                    <strong><?php echo esc_html($application->company_name); ?></strong><br>
                                    <small>Vergi No: <?php echo esc_html($application->tax_number); ?></small>
                                </td>
                                <td>
                                    <?php echo esc_html($user->display_name); ?><br>
                                    <small><?php echo esc_html($user->user_email); ?></small>
                                </td>
                                <td><?php echo esc_html($application->phone); ?></td>
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
                </div>
            <?php else : ?>
                <div style="background: white; padding: 2rem; border-radius: 8px; text-align: center;">
                    <p>Seçilen kriterlere uygun başvuru bulunamadı.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php
    }
    ?>
</div>