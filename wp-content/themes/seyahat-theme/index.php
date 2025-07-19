<?php
/**
 * The main template file for Seyahat Acentesi theme
 * This is the most generic template file in a WordPress theme
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title"><?php _e( 'Dünyayı Keşfet', 'seyahat-theme' ); ?></h1>
            <p class="hero-subtitle"><?php _e( 'Benzersiz seyahat deneyimleri ve aktiviteler', 'seyahat-theme' ); ?></p>
            
            <!-- Search Form -->
            <div class="search-form">
                <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <div class="search-row">
                        <div class="search-field">
                            <label for="destination"><?php _e( 'Nereye?', 'seyahat-theme' ); ?></label>
                            <input type="text" id="destination" name="destination" placeholder="<?php _e( 'Şehir, ülke veya aktivite ara...', 'seyahat-theme' ); ?>">
                        </div>
                        <div class="search-field">
                            <label for="date"><?php _e( 'Tarih', 'seyahat-theme' ); ?></label>
                            <input type="date" id="date" name="date">
                        </div>
                        <div class="search-field">
                            <label for="guests"><?php _e( 'Misafir', 'seyahat-theme' ); ?></label>
                            <select id="guests" name="guests">
                                <option value="1">1 <?php _e( 'kişi', 'seyahat-theme' ); ?></option>
                                <option value="2">2 <?php _e( 'kişi', 'seyahat-theme' ); ?></option>
                                <option value="3">3 <?php _e( 'kişi', 'seyahat-theme' ); ?></option>
                                <option value="4">4+ <?php _e( 'kişi', 'seyahat-theme' ); ?></option>
                            </select>
                        </div>
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i> <?php _e( 'Ara', 'seyahat-theme' ); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Popular Categories -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title"><?php _e( 'Popüler Kategoriler', 'seyahat-theme' ); ?></h2>
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-city"></i>
                    </div>
                    <div class="category-info">
                        <h3 class="category-title"><?php _e( 'Şehir Turları', 'seyahat-theme' ); ?></h3>
                        <p class="category-count">150+ <?php _e( 'aktivite', 'seyahat-theme' ); ?></p>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="category-info">
                        <h3 class="category-title"><?php _e( 'Müze & Sanat', 'seyahat-theme' ); ?></h3>
                        <p class="category-count">80+ <?php _e( 'aktivite', 'seyahat-theme' ); ?></p>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <div class="category-info">
                        <h3 class="category-title"><?php _e( 'Macera Sporları', 'seyahat-theme' ); ?></h3>
                        <p class="category-count">120+ <?php _e( 'aktivite', 'seyahat-theme' ); ?></p>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="category-info">
                        <h3 class="category-title"><?php _e( 'Yemek Turları', 'seyahat-theme' ); ?></h3>
                        <p class="category-count">65+ <?php _e( 'aktivite', 'seyahat-theme' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="products-section">
        <div class="container">
            <h2 class="section-title"><?php _e( 'Öne Çıkan Deneyimler', 'seyahat-theme' ); ?></h2>
            
            <?php if ( have_posts() ) : ?>
                <div class="product-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('product-card'); ?>>
                            <div class="product-image">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php else : ?>
                                    <i class="fas fa-image fa-3x" style="color: #ccc;"></i>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="product-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php _e( 'İstanbul, Türkiye', 'seyahat-theme' ); ?></span>
                                </div>
                                
                                <div class="product-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <span class="rating-count">(124 <?php _e( 'değerlendirme', 'seyahat-theme' ); ?>)</span>
                                </div>
                                
                                <div class="product-price">
                                    ₺299<span style="font-size: 0.8em; font-weight: normal;"> / <?php _e( 'kişi', 'seyahat-theme' ); ?></span>
                                </div>
                                
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                    <?php _e( 'Detayları Gör', 'seyahat-theme' ); ?>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="no-content text-center">
                    <h3><?php _e( 'Henüz içerik bulunmuyor', 'seyahat-theme' ); ?></h3>
                    <p><?php _e( 'Seyahat deneyimlerinizi keşfetmeye başlayın.', 'seyahat-theme' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
