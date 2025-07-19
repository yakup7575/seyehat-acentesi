    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="footer-content">
                <!-- Company Info -->
                <div class="footer-section">
                    <h4><?php _e( 'Seyahat Acentesi', 'seyahat-theme' ); ?></h4>
                    <p><?php _e( 'Dünyada en iyi seyahat deneyimlerini keşfedin. Güvenilir, kaliteli ve unutulmaz anılar için bizi tercih edin.', 'seyahat-theme' ); ?></p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-section">
                    <h4><?php _e( 'Hızlı Bağlantılar', 'seyahat-theme' ); ?></h4>
                    <ul>
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Ana Sayfa', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'Hakkımızda', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'Destinasyonlar', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'Blog', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'İletişim', 'seyahat-theme' ); ?></a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="footer-section">
                    <h4><?php _e( 'Kategoriler', 'seyahat-theme' ); ?></h4>
                    <ul>
                        <li><a href="#"><?php _e( 'Şehir Turları', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'Müze & Sanat', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'Macera Sporları', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'Yemek Turları', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'Gece Hayatı', 'seyahat-theme' ); ?></a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div class="footer-section">
                    <h4><?php _e( 'Destek', 'seyahat-theme' ); ?></h4>
                    <ul>
                        <li><a href="#"><?php _e( 'Müşteri Hizmetleri', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'SSS', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'İptal Politikası', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'Gizlilik Politikası', 'seyahat-theme' ); ?></a></li>
                        <li><a href="#"><?php _e( 'Kullanım Şartları', 'seyahat-theme' ); ?></a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-section">
                    <h4><?php _e( 'İletişim', 'seyahat-theme' ); ?></h4>
                    <div class="contact-info">
                        <p><i class="fas fa-phone"></i> +90 212 XXX XX XX</p>
                        <p><i class="fas fa-envelope"></i> info@seyahatacentesi.com</p>
                        <p><i class="fas fa-map-marker-alt"></i> İstanbul, Türkiye</p>
                    </div>
                </div>

                <!-- Widget Area -->
                <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                    <div class="footer-section">
                        <?php dynamic_sidebar( 'footer-1' ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. <?php _e( 'Tüm hakları saklıdır.', 'seyahat-theme' ); ?></p>
                <p><?php _e( 'GetYourGuide benzeri tam kapsamlı seyahat pazaryeri platformu', 'seyahat-theme' ); ?></p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
