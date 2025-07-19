// Seyahat Acentesi Marketplace - Theme JavaScript

jQuery(document).ready(function($) {
    
    // Mobile menu toggle
    $('.menu-toggle').on('click', function() {
        $('.main-navigation').toggleClass('active');
    });
    
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });
    
    // Tour card animations
    $('.tour-card').hover(
        function() {
            $(this).find('.tour-image').css('transform', 'scale(1.05)');
        },
        function() {
            $(this).find('.tour-image').css('transform', 'scale(1)');
        }
    );
    
    // Booking form validation
    $('.booking-form').on('submit', function(e) {
        var bookingDate = $('#booking_date').val();
        if (bookingDate) {
            var today = new Date().toISOString().split('T')[0];
            if (bookingDate < today) {
                e.preventDefault();
                alert('Seyahat tarihi bugünden önce olamaz.');
                return false;
            }
        }
    });
    
    // Tour search functionality
    $('.tour-search-form').on('submit', function(e) {
        // Form will submit normally, no AJAX needed for archive page
    });
    
    // Price range slider (if implemented)
    if ($('.price-range').length) {
        $('.price-range').each(function() {
            var slider = $(this);
            var min = slider.data('min') || 0;
            var max = slider.data('max') || 10000;
            
            slider.slider({
                range: true,
                min: min,
                max: max,
                values: [min, max],
                slide: function(event, ui) {
                    $('.min-price-display').text('₺' + ui.values[0]);
                    $('.max-price-display').text('₺' + ui.values[1]);
                }
            });
        });
    }
    
    // Image gallery (if implemented)
    if ($('.tour-gallery').length) {
        $('.tour-gallery a').on('click', function(e) {
            e.preventDefault();
            var imageUrl = $(this).attr('href');
            var imageAlt = $(this).find('img').attr('alt');
            
            // Simple lightbox implementation
            var lightbox = $('<div class="lightbox-overlay"><div class="lightbox-content"><img src="' + imageUrl + '" alt="' + imageAlt + '"><span class="lightbox-close">&times;</span></div></div>');
            
            $('body').append(lightbox);
            
            lightbox.on('click', function(e) {
                if (e.target === this || $(e.target).hasClass('lightbox-close')) {
                    lightbox.remove();
                }
            });
        });
    }
    
    // Sticky header
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 100) {
            $('.site-header').addClass('scrolled');
        } else {
            $('.site-header').removeClass('scrolled');
        }
    });
    
    // Auto-hide notifications
    setTimeout(function() {
        $('.notice, .alert').fadeOut();
    }, 5000);
    
    // Tour favorites functionality (if implemented)
    $('.favorite-btn').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var tourId = btn.data('tour-id');
        
        $.post(ajax_object.ajax_url, {
            action: 'toggle_favorite',
            tour_id: tourId,
            nonce: ajax_object.nonce
        }, function(response) {
            if (response.success) {
                btn.toggleClass('favorited');
                btn.text(btn.hasClass('favorited') ? '❤️ Favorilerde' : '🤍 Favorilere Ekle');
            }
        });
    });
    
    // Load more tours functionality
    $('.load-more-tours').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var page = btn.data('page') || 1;
        
        btn.text('Yükleniyor...');
        
        $.get(window.location.href, {
            page: page + 1,
            ajax: 1
        }, function(data) {
            var newTours = $(data).find('.tour-card');
            $('.tours-grid').append(newTours);
            
            btn.data('page', page + 1);
            btn.text('Daha Fazla Tur Yükle');
            
            if (newTours.length < 6) {
                btn.hide();
            }
        });
    });
    
});

// Utility functions
function formatCurrency(amount) {
    return '₺' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function validateTurkishPhone(phone) {
    var phoneRegex = /^(\+90|0)?[1-9][0-9]{9}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

function showMessage(message, type) {
    var messageClass = type === 'error' ? 'notice-error' : 'notice-success';
    var messageHtml = '<div class="notice ' + messageClass + '" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 1rem; background: white; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">' + message + '</div>';
    
    $('body').append(messageHtml);
    
    setTimeout(function() {
        $('.notice').fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}