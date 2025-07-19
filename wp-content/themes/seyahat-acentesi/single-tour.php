<?php get_header(); ?>

<div class="container">
    <article class="tour-single" style="padding: 2rem 0;">
        <?php
        while (have_posts()) : the_post();
            $tour_price = get_tour_price(get_the_ID());
            $tour_location = get_tour_location(get_the_ID());
            $tour_duration = get_tour_duration(get_the_ID());
            $tour_start_date = get_tour_start_date(get_the_ID());
            $tour_max_participants = get_tour_max_participants(get_the_ID());
            $agency_id = get_tour_agency_id(get_the_ID());
            $agency_name = $agency_id ? get_the_author_meta('display_name', $agency_id) : get_the_author();
            ?>
            
            <div class="tour-header" style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
                <div class="breadcrumb" style="margin-bottom: 1rem; font-size: 0.9rem; color: #666;">
                    <a href="<?php echo home_url(); ?>">Ana Sayfa</a> > 
                    <a href="<?php echo get_post_type_archive_link('tour'); ?>">Turlar</a> > 
                    <?php
                    $categories = get_the_terms(get_the_ID(), 'tour_category');
                    if ($categories && !is_wp_error($categories)) {
                        foreach ($categories as $category) {
                            echo '<a href="' . get_term_link($category) . '">' . esc_html($category->name) . '</a> > ';
                            break;
                        }
                    }
                    ?>
                    <span><?php the_title(); ?></span>
                </div>
                
                <h1 class="tour-title" style="color: #2c5aa0; margin-bottom: 1rem;"><?php the_title(); ?></h1>
                
                <div class="tour-quick-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <?php if ($tour_location) : ?>
                        <div class="info-item">
                            <strong>📍 Konum:</strong> <?php echo esc_html($tour_location); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($tour_duration) : ?>
                        <div class="info-item">
                            <strong>🕒 Süre:</strong> <?php echo esc_html($tour_duration); ?> gün
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($tour_start_date) : ?>
                        <div class="info-item">
                            <strong>📅 Başlangıç:</strong> <?php echo date('d.m.Y', strtotime($tour_start_date)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($tour_max_participants) : ?>
                        <div class="info-item">
                            <strong>👥 Kapasite:</strong> <?php echo esc_html($tour_max_participants); ?> kişi
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <strong>🏢 Acente:</strong> <?php echo esc_html($agency_name); ?>
                    </div>
                </div>
                
                <?php if ($categories && !is_wp_error($categories)) : ?>
                    <div class="tour-categories" style="margin-bottom: 1rem;">
                        <?php foreach ($categories as $category) : ?>
                            <a href="<?php echo get_term_link($category); ?>" class="category-tag" style="background: #e3f2fd; color: #1565c0; padding: 0.25rem 0.75rem; border-radius: 15px; text-decoration: none; font-size: 0.875rem; margin-right: 0.5rem;">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tour-content-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <div class="tour-main-content">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="tour-featured-image" style="margin-bottom: 2rem;">
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" alt="<?php the_title(); ?>" style="width: 100%; height: 400px; object-fit: cover; border-radius: 10px;">
                        </div>
                    <?php endif; ?>
                    
                    <div class="tour-description" style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
                        <h2 style="color: #2c5aa0; margin-bottom: 1rem;">Tur Açıklaması</h2>
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Tour Program (if available) -->
                    <?php
                    $tour_program = get_post_meta(get_the_ID(), '_tour_program', true);
                    if ($tour_program) :
                    ?>
                        <div class="tour-program" style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
                            <h2 style="color: #2c5aa0; margin-bottom: 1rem;">Tur Programı</h2>
                            <?php echo wp_kses_post($tour_program); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Included/Excluded Services -->
                    <div class="tour-services" style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                            <?php
                            $included_services = get_post_meta(get_the_ID(), '_tour_included', true);
                            $excluded_services = get_post_meta(get_the_ID(), '_tour_excluded', true);
                            ?>
                            
                            <div class="included-services">
                                <h3 style="color: #10b981; margin-bottom: 1rem;">✅ Dahil Olan Hizmetler</h3>
                                <?php if ($included_services) : ?>
                                    <?php echo wp_kses_post($included_services); ?>
                                <?php else : ?>
                                    <ul>
                                        <li>Ulaşım</li>
                                        <li>Konaklama</li>
                                        <li>Rehber hizmeti</li>
                                        <li>Müze girişleri</li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            
                            <div class="excluded-services">
                                <h3 style="color: #ef4444; margin-bottom: 1rem;">❌ Dahil Olmayan Hizmetler</h3>
                                <?php if ($excluded_services) : ?>
                                    <?php echo wp_kses_post($excluded_services); ?>
                                <?php else : ?>
                                    <ul>
                                        <li>Öğle yemekleri</li>
                                        <li>Kişisel harcamalar</li>
                                        <li>İçecekler</li>
                                        <li>Ekstra aktiviteler</li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tour-sidebar">
                    <!-- Booking Widget -->
                    <div class="booking-widget" style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <?php if ($tour_price) : ?>
                            <div class="price-display" style="text-align: center; margin-bottom: 2rem;">
                                <div class="price-label" style="color: #666; margin-bottom: 0.5rem;">Kişi başı fiyat</div>
                                <div class="price-amount" style="font-size: 2.5rem; font-weight: bold; color: #f59e0b;">
                                    ₺<?php echo number_format($tour_price, 0, ',', '.'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form class="booking-form" method="post" action="<?php echo esc_url(add_query_arg('add-to-cart', get_the_ID(), wc_get_cart_url())); ?>">
                            <div class="form-group">
                                <label for="booking_date">Seyahat Tarihi</label>
                                <input type="date" id="booking_date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="participants">Katılımcı Sayısı</label>
                                <select id="participants" name="participants" required>
                                    <?php for ($i = 1; $i <= ($tour_max_participants ?: 10); $i++) : ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> kişi</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="special_requests">Özel İstekler</label>
                                <textarea id="special_requests" name="special_requests" rows="3" placeholder="Diyet kısıtlamaları, engelli erişimi vb."></textarea>
                            </div>
                            
                            <button type="submit" class="btn" style="width: 100%; background: #10b981;">Rezervasyon Yap</button>
                        </form>
                        
                        <div class="contact-info" style="margin-top: 2rem; text-align: center; font-size: 0.9rem; color: #666;">
                            <p>📞 Telefon ile rezervasyon: <strong>0850 123 45 67</strong></p>
                            <p>✉️ E-posta: <strong>info@seyahatacentesi.com</strong></p>
                        </div>
                    </div>
                    
                    <!-- Agency Info -->
                    <div class="agency-info" style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
                        <h3 style="color: #2c5aa0; margin-bottom: 1rem;">Acente Bilgileri</h3>
                        
                        <div class="agency-details">
                            <p><strong>Acente Adı:</strong> <?php echo esc_html($agency_name); ?></p>
                            
                            <?php if ($agency_id) : 
                                $agency_user = get_userdata($agency_id);
                                $agency_phone = get_user_meta($agency_id, 'phone', true);
                                $agency_website = get_user_meta($agency_id, 'website', true);
                            ?>
                                <p><strong>E-posta:</strong> <?php echo esc_html($agency_user->user_email); ?></p>
                                
                                <?php if ($agency_phone) : ?>
                                    <p><strong>Telefon:</strong> <?php echo esc_html($agency_phone); ?></p>
                                <?php endif; ?>
                                
                                <?php if ($agency_website) : ?>
                                    <p><strong>Web Sitesi:</strong> <a href="<?php echo esc_url($agency_website); ?>" target="_blank"><?php echo esc_html($agency_website); ?></a></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div style="margin-top: 1rem;">
                            <a href="<?php echo home_url('/iletisim'); ?>" class="btn btn-secondary" style="width: 100%;">Acente ile İletişim</a>
                        </div>
                    </div>
                    
                    <!-- Safety Info -->
                    <div class="safety-info" style="background: #f0f9ff; padding: 1.5rem; border-radius: 10px; border-left: 4px solid #0ea5e9;">
                        <h4 style="color: #0c4a6e; margin-bottom: 1rem;">🛡️ Güvenli Rezervasyon</h4>
                        <ul style="list-style: none; padding: 0; font-size: 0.9rem; color: #0c4a6e;">
                            <li>✅ TÜRSAB güvencesi</li>
                            <li>✅ Güvenli ödeme sistemi</li>
                            <li>✅ 7/24 müşteri desteği</li>
                            <li>✅ İptal garantisi</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Related Tours -->
            <div class="related-tours" style="margin-top: 3rem;">
                <h2 style="text-align: center; color: #2c5aa0; margin-bottom: 2rem;">Benzer Turlar</h2>
                
                <div class="tours-grid">
                    <?php
                    $related_tours = new WP_Query(array(
                        'post_type' => 'tour',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'tour_category',
                                'field' => 'term_id',
                                'terms' => wp_get_post_terms(get_the_ID(), 'tour_category', array('fields' => 'ids')),
                            ),
                        ),
                    ));
                    
                    if ($related_tours->have_posts()) :
                        while ($related_tours->have_posts()) : $related_tours->the_post();
                            $related_price = get_tour_price(get_the_ID());
                            $related_location = get_tour_location(get_the_ID());
                            ?>
                            <div class="tour-card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" alt="<?php the_title(); ?>" class="tour-image">
                                    </a>
                                <?php endif; ?>
                                
                                <div class="tour-content">
                                    <h3 class="tour-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <?php if ($related_location) : ?>
                                        <p class="tour-location">📍 <?php echo esc_html($related_location); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($related_price) : ?>
                                        <div class="tour-price">₺<?php echo number_format($related_price, 0, ',', '.'); ?></div>
                                    <?php endif; ?>
                                    
                                    <a href="<?php the_permalink(); ?>" class="btn" style="width: 100%; margin-top: 1rem;">İncele</a>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
            
        <?php endwhile; ?>
    </article>
</div>

<?php get_footer(); ?>