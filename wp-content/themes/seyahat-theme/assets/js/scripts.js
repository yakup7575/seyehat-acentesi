/**
 * Seyahat Acentesi Theme Scripts
 * Main JavaScript file for theme functionality
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Mobile menu toggle
        $('.mobile-menu-toggle').on('click', function() {
            $('.mobile-nav-overlay').addClass('active');
            $('body').addClass('mobile-nav-open');
        });

        $('.mobile-nav-close, .mobile-nav-overlay').on('click', function(e) {
            if (e.target === this) {
                $('.mobile-nav-overlay').removeClass('active');
                $('body').removeClass('mobile-nav-open');
            }
        });

        // Search form enhancement
        $('.search-form input[type="text"]').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            if (!$(this).val()) {
                $(this).parent().removeClass('focused');
            }
        });

        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });

        // Product card hover effects
        $('.product-card').hover(
            function() {
                $(this).find('.product-image').addClass('hovered');
            },
            function() {
                $(this).find('.product-image').removeClass('hovered');
            }
        );

        // AJAX search functionality
        if (typeof seyahat_ajax !== 'undefined') {
            let searchTimeout;
            
            $('.search-form input[name="destination"]').on('input', function() {
                const query = $(this).val();
                
                clearTimeout(searchTimeout);
                
                if (query.length >= 3) {
                    searchTimeout = setTimeout(function() {
                        performSearch(query);
                    }, 500);
                }
            });
            
            function performSearch(query) {
                $.ajax({
                    url: seyahat_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'seyahat_search',
                        query: query,
                        nonce: seyahat_ajax.nonce
                    },
                    beforeSend: function() {
                        $('.search-results').addClass('loading');
                    },
                    success: function(response) {
                        if (response.success) {
                            displaySearchResults(response.data);
                        }
                    },
                    complete: function() {
                        $('.search-results').removeClass('loading');
                    }
                });
            }
            
            function displaySearchResults(results) {
                // Implementation for displaying search results
                console.log('Search results:', results);
            }
        }

        // Price formatter
        $('.product-price').each(function() {
            var price = $(this).text();
            // Format price display if needed
        });

        // Rating stars
        $('.product-rating .stars').each(function() {
            var rating = $(this).data('rating') || 5;
            var starsHtml = '';
            
            for (var i = 1; i <= 5; i++) {
                if (i <= rating) {
                    starsHtml += '<i class="fas fa-star"></i>';
                } else if (i - 0.5 <= rating) {
                    starsHtml += '<i class="fas fa-star-half-alt"></i>';
                } else {
                    starsHtml += '<i class="far fa-star"></i>';
                }
            }
            
            if (starsHtml) {
                $(this).html(starsHtml);
            }
        });

        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // Scroll to top button
        const scrollToTopBtn = $('<button id="scroll-to-top" class="scroll-to-top" aria-label="Yukarı çık"><i class="fas fa-chevron-up"></i></button>');
        $('body').append(scrollToTopBtn);

        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                scrollToTopBtn.addClass('visible');
            } else {
                scrollToTopBtn.removeClass('visible');
            }
        });

        scrollToTopBtn.on('click', function() {
            $('html, body').animate({scrollTop: 0}, 600);
        });

        // Form validation
        $('form').on('submit', function(e) {
            var form = $(this);
            var isValid = true;

            form.find('input[required], select[required], textarea[required]').each(function() {
                var field = $(this);
                var value = field.val().trim();

                if (!value) {
                    field.addClass('error');
                    isValid = false;
                } else {
                    field.removeClass('error');
                }

                // Email validation
                if (field.attr('type') === 'email' && value) {
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        field.addClass('error');
                        isValid = false;
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Show error message
                showNotification('Lütfen tüm gerekli alanları doldurun.', 'error');
            }
        });

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = $(`
                <div class="notification notification-${type}">
                    <span>${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `);

            $('body').append(notification);

            setTimeout(() => {
                notification.addClass('show');
            }, 100);

            notification.find('.notification-close').on('click', function() {
                notification.removeClass('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            });

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }

        // Initialize tooltips if Bootstrap is available
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Currency formatter
        window.formatCurrency = function(amount, currency = 'TRY') {
            const formatter = new Intl.NumberFormat('tr-TR', {
                style: 'currency',
                currency: currency,
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            return formatter.format(amount);
        };

        // Date formatter
        window.formatDate = function(date, locale = 'tr-TR') {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            return new Date(date).toLocaleDateString(locale, options);
        };
    });

    // Page performance monitoring
    $(window).on('load', function() {
        if ('performance' in window) {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log('Page load time:', loadTime + 'ms');
        }
    });

})(jQuery);
