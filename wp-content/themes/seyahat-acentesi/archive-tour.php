<?php get_header(); ?>

<div class="container">
    <div class="tours-archive" style="padding: 2rem 0;">
        <div class="archive-header" style="text-align: center; margin-bottom: 3rem;">
            <h1 class="page-title">
                <?php
                if (is_tax()) {
                    single_term_title();
                } else {
                    echo 'Tüm Turlar';
                }
                ?>
            </h1>
            
            <?php if (is_tax() && term_description()) : ?>
                <div class="taxonomy-description" style="color: #666; margin-top: 1rem;">
                    <?php echo term_description(); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Search and Filter Section -->
        <div class="tour-filters" style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
            <form class="tour-search-form" method="get">
                <div class="search-filters" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="filter-group">
                        <label for="search">Tur Ara</label>
                        <input type="text" id="search" name="search" value="<?php echo get_search_query(); ?>" placeholder="Tur adı veya lokasyon...">
                    </div>
                    
                    <div class="filter-group">
                        <label for="tour_category">Kategori</label>
                        <select id="tour_category" name="tour_category">
                            <option value="">Tüm Kategoriler</option>
                            <?php
                            $categories = get_terms(array(
                                'taxonomy' => 'tour_category',
                                'hide_empty' => false,
                            ));
                            
                            $selected_category = get_query_var('tour_category');
                            
                            foreach ($categories as $category) :
                                $selected = ($selected_category === $category->slug) ? 'selected' : '';
                                echo '<option value="' . esc_attr($category->slug) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                            endforeach;
                            ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="min_price">Min Fiyat</label>
                        <input type="number" id="min_price" name="min_price" value="<?php echo esc_attr($_GET['min_price'] ?? ''); ?>" placeholder="₺">
                    </div>
                    
                    <div class="filter-group">
                        <label for="max_price">Max Fiyat</label>
                        <input type="number" id="max_price" name="max_price" value="<?php echo esc_attr($_GET['max_price'] ?? ''); ?>" placeholder="₺">
                    </div>
                    
                    <div class="filter-group">
                        <label for="sort_by">Sıralama</label>
                        <select id="sort_by" name="sort_by">
                            <option value="date" <?php selected($_GET['sort_by'] ?? '', 'date'); ?>>En Yeni</option>
                            <option value="price_low" <?php selected($_GET['sort_by'] ?? '', 'price_low'); ?>>Fiyat (Düşük-Yüksek)</option>
                            <option value="price_high" <?php selected($_GET['sort_by'] ?? '', 'price_high'); ?>>Fiyat (Yüksek-Düşük)</option>
                            <option value="title" <?php selected($_GET['sort_by'] ?? '', 'title'); ?>>Alfabetik</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn">Filtrele</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Tours Grid -->
        <div class="tours-grid">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
                    $tour_price = get_tour_price(get_the_ID());
                    $tour_location = get_tour_location(get_the_ID());
                    $tour_duration = get_tour_duration(get_the_ID());
                    $tour_start_date = get_tour_start_date(get_the_ID());
                    $agency_id = get_tour_agency_id(get_the_ID());
                    $agency_name = $agency_id ? get_the_author_meta('display_name', $agency_id) : get_the_author();
                    ?>
                    <div class="tour-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" alt="<?php the_title(); ?>" class="tour-image">
                            </a>
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
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </div>
                            
                            <div class="tour-meta" style="display: flex; justify-content: space-between; align-items: center; margin: 1rem 0; font-size: 0.9rem; color: #666;">
                                <?php if ($tour_duration) : ?>
                                    <span class="tour-duration">🕒 <?php echo esc_html($tour_duration); ?> gün</span>
                                <?php endif; ?>
                                
                                <?php if ($tour_start_date) : ?>
                                    <span class="tour-date">📅 <?php echo date('d.m.Y', strtotime($tour_start_date)); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="tour-footer" style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="tour-agency" style="font-size: 0.9rem; color: #888;">🏢 <?php echo esc_html($agency_name); ?></span>
                                
                                <?php if ($tour_price) : ?>
                                    <div class="tour-price">₺<?php echo number_format($tour_price, 0, ',', '.'); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="btn" style="width: 100%; margin-top: 1rem; text-align: center;">Detayları İncele</a>
                        </div>
                    </div>
                    <?php
                endwhile;
                
                // Pagination
                ?>
                <div class="pagination-wrapper" style="grid-column: 1 / -1; text-align: center; margin-top: 2rem;">
                    <?php
                    echo paginate_links(array(
                        'prev_text' => '« Önceki',
                        'next_text' => 'Sonraki »',
                        'type' => 'plain',
                    ));
                    ?>
                </div>
                <?php
                
            else :
                ?>
                <div class="no-tours" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <h3>Tur Bulunamadı</h3>
                    <p>Aradığınız kriterlere uygun tur bulunmamaktadır.</p>
                    <a href="<?php echo get_post_type_archive_link('tour'); ?>" class="btn">Tüm Turları Görüntüle</a>
                </div>
                <?php
            endif;
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>