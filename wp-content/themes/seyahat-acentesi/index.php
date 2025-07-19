<?php get_header(); ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1>Türkiye'nin En Büyük Seyahat Acentesi Pazaryeri</h1>
            <p>Güvenilir seyahat acentelerinden en uygun fiyatlarla tatil paketleri keşfedin</p>
            <a href="#tours" class="cta-button">Turları İncele</a>
        </div>
    </div>
</section>

<!-- Featured Tours Section -->
<section id="tours" class="tours-section">
    <div class="container">
        <h2 class="section-title">Öne Çıkan Turlar</h2>
        
        <div class="tours-grid">
            <?php
            // Get featured tours
            $featured_tours = new WP_Query(array(
                'post_type' => 'tour',
                'posts_per_page' => 6,
                'meta_key' => '_tour_featured',
                'meta_value' => '1',
                'post_status' => 'publish'
            ));
            
            if (!$featured_tours->have_posts()) {
                // If no featured tours, get latest tours
                $featured_tours = new WP_Query(array(
                    'post_type' => 'tour',
                    'posts_per_page' => 6,
                    'post_status' => 'publish'
                ));
            }
            
            if ($featured_tours->have_posts()) :
                while ($featured_tours->have_posts()) : $featured_tours->the_post();
                    $tour_price = get_tour_price(get_the_ID());
                    $tour_location = get_tour_location(get_the_ID());
                    $tour_duration = get_tour_duration(get_the_ID());
                    $agency_id = get_tour_agency_id(get_the_ID());
                    $agency_name = $agency_id ? get_the_author_meta('display_name', $agency_id) : get_the_author();
                    ?>
                    <div class="tour-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" alt="<?php the_title(); ?>" class="tour-image">
                        <?php else : ?>
                            <div class="tour-image-placeholder" style="height: 200px; background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                                📷 Fotoğraf Yok
                            </div>
                        <?php endif; ?>
                        
                        <div class="tour-content">
                            <h3 class="tour-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <?php if ($tour_location) : ?>
                                <p class="tour-location">📍 <?php echo esc_html($tour_location); ?></p>
                            <?php endif; ?>
                            
                            <div class="tour-description">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </div>
                            
                            <div class="tour-meta">
                                <?php if ($tour_duration) : ?>
                                    <span class="tour-duration">🕒 <?php echo esc_html($tour_duration); ?> gün</span>
                                <?php endif; ?>
                                
                                <span class="tour-agency">🏢 <?php echo esc_html($agency_name); ?></span>
                            </div>
                            
                            <?php if ($tour_price) : ?>
                                <div class="tour-price">₺<?php echo number_format($tour_price, 0, ',', '.'); ?></div>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" class="btn">Detayları İncele</a>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <div class="no-tours">
                    <p>Henüz yayınlanmış tur bulunmuyor. Yakında harika turlarla buradayız!</p>
                </div>
                <?php
            endif;
            ?>
        </div>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo get_post_type_archive_link('tour'); ?>" class="btn btn-secondary">Tüm Turları Görüntüle</a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section" style="background: white; padding: 4rem 0;">
    <div class="container">
        <h2 class="section-title">Tur Kategorileri</h2>
        
        <div class="categories-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-top: 2rem;">
            <?php
            $tour_categories = get_terms(array(
                'taxonomy' => 'tour_category',
                'hide_empty' => false,
            ));
            
            if (!empty($tour_categories) && !is_wp_error($tour_categories)) :
                foreach ($tour_categories as $category) :
                    $category_link = get_term_link($category);
                    ?>
                    <div class="category-card" style="background: #f8fafc; padding: 2rem; text-align: center; border-radius: 10px; transition: transform 0.3s;">
                        <div class="category-icon" style="font-size: 3rem; margin-bottom: 1rem;">🌴</div>
                        <h3 style="color: #2c5aa0; margin-bottom: 0.5rem;"><?php echo esc_html($category->name); ?></h3>
                        <p style="color: #666; margin-bottom: 1rem;"><?php echo esc_html($category->description); ?></p>
                        <span style="color: #888; font-size: 0.9rem;"><?php echo $category->count; ?> tur</span>
                        <br>
                        <a href="<?php echo esc_url($category_link); ?>" class="btn" style="margin-top: 1rem;">İncele</a>
                    </div>
                    <?php
                endforeach;
            else :
                // Default categories if none exist
                $default_categories = array(
                    array('name' => 'Yurtiçi Turlar', 'icon' => '🏔️', 'desc' => 'Türkiye\'nin en güzel yerlerini keşfedin'),
                    array('name' => 'Yurtdışı Turlar', 'icon' => '🌍', 'desc' => 'Dünyanın dört bir yanına seyahat edin'),
                    array('name' => 'Yunan Adaları', 'icon' => '🏖️', 'desc' => 'Ege\'nin incilerini ziyaret edin'),
                    array('name' => 'Kültür Turları', 'icon' => '🏛️', 'desc' => 'Tarihi ve kültürel deneyimler'),
                );
                
                foreach ($default_categories as $category) :
                    ?>
                    <div class="category-card" style="background: #f8fafc; padding: 2rem; text-align: center; border-radius: 10px;">
                        <div class="category-icon" style="font-size: 3rem; margin-bottom: 1rem;"><?php echo $category['icon']; ?></div>
                        <h3 style="color: #2c5aa0; margin-bottom: 0.5rem;"><?php echo esc_html($category['name']); ?></h3>
                        <p style="color: #666; margin-bottom: 1rem;"><?php echo esc_html($category['desc']); ?></p>
                        <span style="color: #888; font-size: 0.9rem;">0 tur</span>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Partner Section -->
<section class="partner-section">
    <div class="container">
        <h2 class="section-title">Seyahat Acentesi Partner Olun</h2>
        <p style="text-align: center; font-size: 1.2rem; color: #666; margin-bottom: 3rem;">
            Acentenizi platformumuza kaydettirin ve binlerce müşteriye ulaşın
        </p>
        
        <div class="partner-benefits">
            <div class="benefit-item">
                <div class="benefit-icon">💼</div>
                <h3>Kolay Yönetim</h3>
                <p>Turlarınızı kolayca ekleyin, düzenleyin ve yönetin. Kullanıcı dostu arayüz ile hızlı işlemler.</p>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">📈</div>
                <h3>Artan Satışlar</h3>
                <p>Geniş müşteri kitlesine ulaşın ve satışlarınızı artırın. Profesyonel pazarlama desteği.</p>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">🛡️</div>
                <h3>Güvenli Ödeme</h3>
                <p>Güvenli ödeme sistemi ile risksiz işlemler. Hızlı para transferi garantisi.</p>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">📞</div>
                <h3>7/24 Destek</h3>
                <p>Teknik ve operasyon desteği için 7/24 ulaşabileceğiniz profesyonel ekibimiz.</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo home_url('/partner-kayit'); ?>" class="cta-button">Hemen Partner Ol</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>