<div class="wrap">
    <h1>Tur Yönetimi</h1>
    
    <div class="admin-panel">
        <div class="tour-management-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <?php
            // Get tour statistics
            $all_tours = wp_count_posts('tour');
            $published_tours = $all_tours->publish;
            $draft_tours = $all_tours->draft;
            $pending_tours = $all_tours->pending;
            
            // Get tours by category
            $categories = get_terms(array(
                'taxonomy' => 'tour_category',
                'hide_empty' => false,
            ));
            ?>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #3b82f6;"><?php echo $published_tours; ?></div>
                <div class="stat-label" style="color: #666;">Yayında</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #f59e0b;"><?php echo $draft_tours; ?></div>
                <div class="stat-label" style="color: #666;">Taslak</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #8b5cf6;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #8b5cf6;"><?php echo $pending_tours; ?></div>
                <div class="stat-label" style="color: #666;">İncelemede</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #10b981;">
                <div class="stat-number" style="font-size: 2rem; font-weight: bold; color: #10b981;"><?php echo count($categories); ?></div>
                <div class="stat-label" style="color: #666;">Kategori</div>
            </div>
        </div>
        
        <div class="tour-filters" style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <form method="get" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
                <input type="hidden" name="page" value="tour-management">
                
                <div>
                    <label for="filter_status">Durum:</label>
                    <select name="filter_status" id="filter_status">
                        <option value="">Tümü</option>
                        <option value="publish" <?php selected($_GET['filter_status'] ?? '', 'publish'); ?>>Yayında</option>
                        <option value="draft" <?php selected($_GET['filter_status'] ?? '', 'draft'); ?>>Taslak</option>
                        <option value="pending" <?php selected($_GET['filter_status'] ?? '', 'pending'); ?>>İncelemede</option>
                    </select>
                </div>
                
                <div>
                    <label for="filter_category">Kategori:</label>
                    <select name="filter_category" id="filter_category">
                        <option value="">Tüm Kategoriler</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected($_GET['filter_category'] ?? '', $category->term_id); ?>>
                                <?php echo esc_html($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="filter_partner">Partner:</label>
                    <select name="filter_partner" id="filter_partner">
                        <option value="">Tüm Partnerler</option>
                        <?php
                        $partners = get_users(array('role' => 'partner'));
                        foreach ($partners as $partner) :
                        ?>
                            <option value="<?php echo esc_attr($partner->ID); ?>" <?php selected($_GET['filter_partner'] ?? '', $partner->ID); ?>>
                                <?php echo esc_html($partner->display_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="filter_search">Ara:</label>
                    <input type="text" name="filter_search" id="filter_search" value="<?php echo esc_attr($_GET['filter_search'] ?? ''); ?>" placeholder="Tur adı...">
                </div>
                
                <div>
                    <button type="submit" class="button button-primary">Filtrele</button>
                    <a href="<?php echo admin_url('admin.php?page=tour-management'); ?>" class="button">Temizle</a>
                </div>
            </form>
        </div>
        
        <div class="tour-list" style="background: white; padding: 1.5rem; border-radius: 8px;">
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                <h2>Turlar</h2>
                <a href="<?php echo admin_url('post-new.php?post_type=tour'); ?>" class="button button-primary">Yeni Tur Ekle</a>
            </div>
            
            <?php
            // Build query based on filters
            $args = array(
                'post_type' => 'tour',
                'posts_per_page' => 20,
                'post_status' => array('publish', 'draft', 'pending'),
            );
            
            if (!empty($_GET['filter_status'])) {
                $args['post_status'] = sanitize_text_field($_GET['filter_status']);
            }
            
            if (!empty($_GET['filter_category'])) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'tour_category',
                        'field' => 'term_id',
                        'terms' => intval($_GET['filter_category']),
                    ),
                );
            }
            
            if (!empty($_GET['filter_partner'])) {
                $args['author'] = intval($_GET['filter_partner']);
            }
            
            if (!empty($_GET['filter_search'])) {
                $args['s'] = sanitize_text_field($_GET['filter_search']);
            }
            
            $tours = new WP_Query($args);
            
            if ($tours->have_posts()) :
            ?>
                <table class="data-table widefat">
                    <thead>
                        <tr>
                            <th>Tur</th>
                            <th>Partner</th>
                            <th>Kategori</th>
                            <th>Fiyat</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($tours->have_posts()) : $tours->the_post(); 
                            $tour_price = get_tour_price(get_the_ID());
                            $tour_categories = get_the_terms(get_the_ID(), 'tour_category');
                            $agency_id = get_tour_agency_id(get_the_ID());
                            $agency_name = $agency_id ? get_the_author_meta('display_name', $agency_id) : get_the_author();
                        ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else : ?>
                                        <div style="width: 50px; height: 50px; background: #e5e7eb; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">📷</div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><a href="<?php echo get_edit_post_link(); ?>"><?php the_title(); ?></a></strong>
                                        <br><small><?php echo get_tour_location(get_the_ID()); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo esc_html($agency_name); ?></td>
                            <td>
                                <?php if ($tour_categories && !is_wp_error($tour_categories)) : ?>
                                    <?php echo esc_html($tour_categories[0]->name); ?>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo $tour_price ? '₺' . number_format($tour_price, 0, ',', '.') : '-'; ?></td>
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
                                <?php if (get_post_status() === 'pending') : ?>
                                    <button onclick="approveTour(<?php echo get_the_ID(); ?>)" class="button button-primary button-small">Onayla</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <div class="pagination-wrapper" style="margin-top: 1rem; text-align: center;">
                    <?php
                    echo paginate_links(array(
                        'total' => $tours->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'format' => '?paged=%#%',
                        'prev_text' => '« Önceki',
                        'next_text' => 'Sonraki »',
                    ));
                    ?>
                </div>
                
            <?php else : ?>
                <div style="text-align: center; padding: 3rem;">
                    <p>Filtrelere uygun tur bulunamadı.</p>
                </div>
            <?php endif; ?>
            
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</div>

<script>
function approveTour(tourId) {
    if (confirm('Bu turu onaylamak istediğinize emin misiniz?')) {
        jQuery.post(ajaxurl, {
            action: 'approve_tour',
            tour_id: tourId,
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