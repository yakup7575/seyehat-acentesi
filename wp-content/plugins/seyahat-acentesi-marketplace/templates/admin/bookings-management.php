<div class="wrap">
    <h1>Rezervasyon Yönetimi</h1>
    
    <div class="admin-panel">
        <div class="booking-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <?php
            // Get booking statistics
            global $wpdb;
            $bookings_table = $wpdb->prefix . 'tour_bookings';
            
            $total_bookings = $wpdb->get_var("SELECT COUNT(*) FROM $bookings_table");
            $pending_bookings = $wpdb->get_var("SELECT COUNT(*) FROM $bookings_table WHERE status = 'pending'");
            $confirmed_bookings = $wpdb->get_var("SELECT COUNT(*) FROM $bookings_table WHERE status = 'confirmed'");
            $total_revenue = $wpdb->get_var("SELECT SUM(total_price) FROM $bookings_table WHERE payment_status = 'completed'") ?: 0;
            ?>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #3b82f6;"><?php echo $total_bookings; ?></div>
                <div class="stat-label" style="color: #666;">Toplam Rezervasyon</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #f59e0b;"><?php echo $pending_bookings; ?></div>
                <div class="stat-label" style="color: #666;">Bekleyen</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #10b981;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #10b981;"><?php echo $confirmed_bookings; ?></div>
                <div class="stat-label" style="color: #666;">Onaylanan</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #8b5cf6;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #8b5cf6;">₺<?php echo number_format($total_revenue, 0, ',', '.'); ?></div>
                <div class="stat-label" style="color: #666;">Toplam Gelir</div>
            </div>
        </div>
        
        <div class="booking-filters" style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <form method="get" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
                <input type="hidden" name="page" value="bookings-management">
                
                <div>
                    <label for="filter_status">Durum:</label>
                    <select name="filter_status" id="filter_status">
                        <option value="">Tümü</option>
                        <option value="pending" <?php selected($_GET['filter_status'] ?? '', 'pending'); ?>>Bekleyen</option>
                        <option value="confirmed" <?php selected($_GET['filter_status'] ?? '', 'confirmed'); ?>>Onaylanan</option>
                        <option value="cancelled" <?php selected($_GET['filter_status'] ?? '', 'cancelled'); ?>>İptal</option>
                    </select>
                </div>
                
                <div>
                    <label for="filter_payment">Ödeme Durumu:</label>
                    <select name="filter_payment" id="filter_payment">
                        <option value="">Tümü</option>
                        <option value="pending" <?php selected($_GET['filter_payment'] ?? '', 'pending'); ?>>Bekleyen</option>
                        <option value="completed" <?php selected($_GET['filter_payment'] ?? '', 'completed'); ?>>Tamamlandı</option>
                        <option value="failed" <?php selected($_GET['filter_payment'] ?? '', 'failed'); ?>>Başarısız</option>
                    </select>
                </div>
                
                <div>
                    <label for="filter_date_from">Başlangıç Tarihi:</label>
                    <input type="date" name="filter_date_from" id="filter_date_from" value="<?php echo esc_attr($_GET['filter_date_from'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="filter_date_to">Bitiş Tarihi:</label>
                    <input type="date" name="filter_date_to" id="filter_date_to" value="<?php echo esc_attr($_GET['filter_date_to'] ?? ''); ?>">
                </div>
                
                <div>
                    <button type="submit" class="button button-primary">Filtrele</button>
                    <a href="<?php echo admin_url('admin.php?page=bookings-management'); ?>" class="button">Temizle</a>
                </div>
            </form>
        </div>
        
        <div class="booking-list" style="background: white; padding: 1.5rem; border-radius: 8px;">
            <h2>Rezervasyonlar</h2>
            
            <?php
            // Build query for bookings
            $where_conditions = array('1=1');
            
            if (!empty($_GET['filter_status'])) {
                $where_conditions[] = $wpdb->prepare("status = %s", sanitize_text_field($_GET['filter_status']));
            }
            
            if (!empty($_GET['filter_payment'])) {
                $where_conditions[] = $wpdb->prepare("payment_status = %s", sanitize_text_field($_GET['filter_payment']));
            }
            
            if (!empty($_GET['filter_date_from'])) {
                $where_conditions[] = $wpdb->prepare("booking_date >= %s", sanitize_text_field($_GET['filter_date_from']));
            }
            
            if (!empty($_GET['filter_date_to'])) {
                $where_conditions[] = $wpdb->prepare("booking_date <= %s", sanitize_text_field($_GET['filter_date_to']) . ' 23:59:59');
            }
            
            $where_clause = implode(' AND ', $where_conditions);
            $bookings = $wpdb->get_results("SELECT * FROM $bookings_table WHERE $where_clause ORDER BY booking_date DESC LIMIT 50");
            
            if (!empty($bookings)) :
            ?>
                <table class="data-table widefat">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tur</th>
                            <th>Müşteri</th>
                            <th>Katılımcı</th>
                            <th>Seyahat Tarihi</th>
                            <th>Toplam Fiyat</th>
                            <th>Durum</th>
                            <th>Ödeme</th>
                            <th>Rezervasyon Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking) : 
                            $tour = get_post($booking->tour_id);
                            $customer = get_userdata($booking->user_id);
                        ?>
                        <tr>
                            <td><?php echo $booking->id; ?></td>
                            <td>
                                <?php if ($tour) : ?>
                                    <strong><a href="<?php echo get_permalink($tour->ID); ?>" target="_blank"><?php echo esc_html($tour->post_title); ?></a></strong>
                                    <br><small><?php echo get_tour_location($tour->ID); ?></small>
                                <?php else : ?>
                                    <em>Tur bulunamadı</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($customer) : ?>
                                    <?php echo esc_html($customer->display_name); ?>
                                    <br><small><?php echo esc_html($customer->user_email); ?></small>
                                <?php else : ?>
                                    <em>Kullanıcı bulunamadı</em>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $booking->participants; ?> kişi</td>
                            <td><?php echo $booking->travel_date ? date('d.m.Y', strtotime($booking->travel_date)) : '-'; ?></td>
                            <td>₺<?php echo number_format($booking->total_price, 0, ',', '.'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo esc_attr($booking->status); ?>">
                                    <?php
                                    switch ($booking->status) {
                                        case 'pending': echo 'Bekleyen'; break;
                                        case 'confirmed': echo 'Onaylanan'; break;
                                        case 'cancelled': echo 'İptal'; break;
                                        default: echo ucfirst($booking->status); break;
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $booking->payment_status === 'completed' ? 'status-approved' : 'status-pending'; ?>">
                                    <?php
                                    switch ($booking->payment_status) {
                                        case 'pending': echo 'Bekleyen'; break;
                                        case 'completed': echo 'Tamamlandı'; break;
                                        case 'failed': echo 'Başarısız'; break;
                                        default: echo ucfirst($booking->payment_status); break;
                                    }
                                    ?>
                                </span>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($booking->booking_date)); ?></td>
                            <td>
                                <?php if ($booking->status === 'pending') : ?>
                                    <button onclick="confirmBooking(<?php echo $booking->id; ?>)" class="button button-primary button-small">Onayla</button>
                                    <button onclick="cancelBooking(<?php echo $booking->id; ?>)" class="button button-secondary button-small">İptal</button>
                                <?php endif; ?>
                                <button onclick="viewBookingDetails(<?php echo $booking->id; ?>)" class="button button-small">Detay</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div style="text-align: center; padding: 3rem;">
                    <p>Henüz rezervasyon bulunmuyor.</p>
                    <p><small>Müşterilerin rezervasyon yapması için WooCommerce entegrasyonunun tamamlanması gerekmektedir.</small></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Monthly Revenue Chart Placeholder -->
        <div class="revenue-chart" style="background: white; padding: 1.5rem; border-radius: 8px; margin-top: 2rem;">
            <h2>Aylık Gelir Raporu</h2>
            <div style="text-align: center; padding: 3rem; color: #666;">
                <p>Gelir raporu grafikleri WooCommerce entegrasyonu ile aktif hale gelecektir.</p>
            </div>
        </div>
    </div>
</div>

<script>
function confirmBooking(bookingId) {
    if (confirm('Bu rezervasyonu onaylamak istediğinize emin misiniz?')) {
        jQuery.post(ajaxurl, {
            action: 'confirm_booking',
            booking_id: bookingId,
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

function cancelBooking(bookingId) {
    var reason = prompt('İptal nedeni (opsiyonel):');
    if (reason !== null) {
        jQuery.post(ajaxurl, {
            action: 'cancel_booking',
            booking_id: bookingId,
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

function viewBookingDetails(bookingId) {
    // Open booking details in a modal or new window
    var detailsUrl = '<?php echo admin_url('admin.php?page=bookings-management&action=view&id='); ?>' + bookingId;
    window.open(detailsUrl, '_blank', 'width=800,height=600');
}
</script>