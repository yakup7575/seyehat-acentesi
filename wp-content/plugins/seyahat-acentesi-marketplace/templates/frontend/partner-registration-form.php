<div class="partner-registration-form">
    <?php
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $existing_application = sam_get_user_application($user_id);
        
        if ($existing_application) {
            switch ($existing_application->status) {
                case 'pending':
                    ?>
                    <div class="notice notice-info">
                        <h3>Başvurunuz İnceleniyor</h3>
                        <p>Partner başvurunuz <?php echo date('d.m.Y', strtotime($existing_application->applied_date)); ?> tarihinde alınmıştır ve şu anda incelenmektedir.</p>
                        <p>En kısa sürede başvurunuz değerlendirilerek size e-posta ile bilgi verilecektir.</p>
                    </div>
                    <?php
                    return;
                    
                case 'approved':
                    ?>
                    <div class="notice notice-success">
                        <h3>Partner Başvurunuz Onaylandı!</h3>
                        <p>Tebrikler! Partner başvurunuz onaylanmıştır. Artık platformumuzda tur yayınlayabilirsiniz.</p>
                        <a href="<?php echo admin_url('edit.php?post_type=tour'); ?>" class="btn">Tur Yönetimine Git</a>
                    </div>
                    <?php
                    return;
                    
                case 'rejected':
                    ?>
                    <div class="notice notice-error">
                        <h3>Başvurunuz Hakkında</h3>
                        <p>Partner başvurunuz değerlendirildi ancak şu anda onaylanamadı.</p>
                        <?php if ($existing_application->notes) : ?>
                            <p><strong>Sebep:</strong> <?php echo esc_html($existing_application->notes); ?></p>
                        <?php endif; ?>
                        <p>Eksiklikleri tamamladıktan sonra aşağıdaki formu kullanarak tekrar başvurabilirsiniz.</p>
                    </div>
                    <?php
                    break;
            }
        }
        ?>
        
        <div class="partner-form-container" style="background: white; padding: 2rem; border-radius: 10px; margin: 2rem 0;">
            <h2>Seyahat Acentesi Partner Başvuru Formu</h2>
            <p>Platformumuza partner olmak için aşağıdaki formu doldurun. Başvurunuz en kısa sürede değerlendirilerek size dönüş yapılacaktır.</p>
            
            <form id="partner-application-form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="company_name">Şirket Adı *</label>
                    <input type="text" id="company_name" name="company_name" required>
                </div>
                
                <div class="form-group">
                    <label for="tax_number">Vergi Numarası *</label>
                    <input type="text" id="tax_number" name="tax_number" required>
                    <small>Şirketinizin vergi numarasını girin</small>
                </div>
                
                <div class="form-group">
                    <label for="phone">Telefon Numarası *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Şirket Adresi *</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="website">Web Sitesi</label>
                    <input type="url" id="website" name="website">
                    <small>Varsa şirketinizin web sitesi adresini girin</small>
                </div>
                
                <div class="form-group">
                    <label for="description">Şirket Hakkında *</label>
                    <textarea id="description" name="description" rows="5" required placeholder="Şirketinizin faaliyet alanı, hizmetleri ve deneyimi hakkında bilgi verin..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="documents">Belgeler</label>
                    <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                    <small>Şirket belgeleri, TÜRSAB belgesi, faaliyet belgesi vb. yükleyebilirsiniz (PDF, JPG, PNG formatlarında)</small>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="terms_accepted" required>
                        <a href="<?php echo home_url('/kullanim-sartlari'); ?>" target="_blank">Kullanım şartlarını</a> ve <a href="<?php echo home_url('/gizlilik-politikasi'); ?>" target="_blank">gizlilik politikasını</a> okudum ve kabul ediyorum. *
                    </label>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Başvuru Gönder</button>
                </div>
            </form>
        </div>
        
        <div class="partner-benefits" style="margin-top: 3rem;">
            <h3>Partner Olmanın Avantajları</h3>
            <div class="benefits-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div class="benefit-item">
                    <div class="benefit-icon">🌐</div>
                    <h4>Geniş Müşteri Kitlesi</h4>
                    <p>Binlerce potansiyel müşteriye ulaşın ve satışlarınızı artırın.</p>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">💰</div>
                    <h4>Düşük Komisyon</h4>
                    <p>Sektörün en uygun komisyon oranları ile daha fazla kazanın.</p>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">📱</div>
                    <h4>Kolay Yönetim</h4>
                    <p>Kullanıcı dostu panel ile turlarınızı kolayca yönetin.</p>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">🛡️</div>
                    <h4>Güvenli Ödeme</h4>
                    <p>Güvenli ödeme sistemi ile risksiz para transferi.</p>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#partner-application-form').on('submit', function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                formData.append('action', 'submit_partner_application');
                formData.append('nonce', '<?php echo wp_create_nonce('sam_nonce'); ?>');
                
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.text();
                submitBtn.text('Gönderiliyor...').prop('disabled', true);
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert(response.data);
                            location.reload();
                        } else {
                            alert('Hata: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Beklenmedik bir hata oluştu. Lütfen tekrar deneyin.');
                    },
                    complete: function() {
                        submitBtn.text(originalText).prop('disabled', false);
                    }
                });
            });
        });
        </script>
        
        <?php
    } else {
        ?>
        <div class="login-required" style="background: white; padding: 2rem; border-radius: 10px; text-align: center; margin: 2rem 0;">
            <h2>Partner Başvurusu</h2>
            <p>Partner başvurusu yapabilmek için önce giriş yapmanız gerekmektedir.</p>
            <div style="margin-top: 2rem;">
                <a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn">Giriş Yap</a>
                <a href="<?php echo wp_registration_url(); ?>" class="btn btn-secondary">Kayıt Ol</a>
            </div>
        </div>
        
        <div class="why-partner" style="margin-top: 3rem;">
            <h3>Neden Partner Olmalısınız?</h3>
            <div class="partner-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div class="info-card" style="background: white; padding: 1.5rem; border-radius: 8px;">
                    <h4>📈 Satışlarınızı Artırın</h4>
                    <p>Geniş müşteri kitlemize ulaşarak satışlarınızı katlamanya fırsat yakalayın. Profesyonel pazarlama desteği ile turlarınız daha fazla kişiye ulaşsın.</p>
                </div>
                
                <div class="info-card" style="background: white; padding: 1.5rem; border-radius: 8px;">
                    <h4>🛠️ Profesyonel Araçlar</h4>
                    <p>Tur yönetimi, rezervasyon takibi, müşteri iletişimi ve raporlama için ihtiyacınız olan tüm araçlar tek platformda.</p>
                </div>
                
                <div class="info-card" style="background: white; padding: 1.5rem; border-radius: 8px;">
                    <h4>📞 7/24 Destek</h4>
                    <p>Teknik sorunlardan operasyonel desteke kadar 7/24 ulaşabileceğiniz profesyonel ekibimizle yanınızdayız.</p>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>