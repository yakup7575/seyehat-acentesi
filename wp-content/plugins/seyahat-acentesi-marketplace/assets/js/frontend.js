// Seyahat Acentesi Marketplace - Frontend JavaScript

jQuery(document).ready(function($) {
    
    // Tour search functionality
    $('.tour-search-form').on('submit', function(e) {
        e.preventDefault();
        performTourSearch();
    });
    
    // Filter change handlers
    $('.search-filter').on('change', function() {
        performTourSearch();
    });
    
    function performTourSearch() {
        var searchData = {
            action: 'search_tours',
            category: $('#tour_category').val(),
            location: $('#tour_location').val(),
            min_price: $('#min_price').val(),
            max_price: $('#max_price').val(),
            duration: $('#tour_duration').val(),
            nonce: sam_ajax.nonce
        };
        
        $('.search-results').html('<div class="loading">Turlar aranıyor...</div>');
        
        $.post(sam_ajax.ajax_url, searchData, function(response) {
            if (response.success) {
                $('.search-results').html(response.data);
            } else {
                $('.search-results').html('<div class="no-results">Arama kriterlerinize uygun tur bulunamadı.</div>');
            }
        });
    }
    
    // Partner dashboard functionality
    if ($('.partner-dashboard').length) {
        loadDashboardData();
    }
    
    function loadDashboardData() {
        $.post(sam_ajax.ajax_url, {
            action: 'get_partner_dashboard_data',
            nonce: sam_ajax.nonce
        }, function(response) {
            if (response.success) {
                updateDashboardStats(response.data);
            }
        });
    }
    
    function updateDashboardStats(data) {
        if (data.tours_count !== undefined) {
            $('.stat-tours .stat-number').text(data.tours_count);
        }
        if (data.bookings_count !== undefined) {
            $('.stat-bookings .stat-number').text(data.bookings_count);
        }
        if (data.revenue !== undefined) {
            $('.stat-revenue .stat-number').text('₺' + data.revenue.toLocaleString('tr-TR'));
        }
    }
    
    // Form validation
    $('form').on('submit', function(e) {
        var form = $(this);
        var isValid = true;
        
        // Check required fields
        form.find('[required]').each(function() {
            var field = $(this);
            if (!field.val().trim()) {
                field.addClass('error');
                isValid = false;
            } else {
                field.removeClass('error');
            }
        });
        
        // Email validation
        form.find('input[type="email"]').each(function() {
            var email = $(this);
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.val() && !emailRegex.test(email.val())) {
                email.addClass('error');
                isValid = false;
            } else {
                email.removeClass('error');
            }
        });
        
        // Phone validation (Turkish format)
        form.find('input[type="tel"]').each(function() {
            var phone = $(this);
            var phoneRegex = /^(\+90|0)?[1-9][0-9]{9}$/;
            if (phone.val() && !phoneRegex.test(phone.val().replace(/\s/g, ''))) {
                phone.addClass('error');
                isValid = false;
            } else {
                phone.removeClass('error');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Lütfen tüm gerekli alanları doğru şekilde doldurun.');
        }
    });
    
    // File upload preview
    $('input[type="file"]').on('change', function() {
        var files = this.files;
        var preview = $(this).siblings('.file-preview');
        
        if (!preview.length) {
            preview = $('<div class="file-preview"></div>');
            $(this).after(preview);
        }
        
        preview.empty();
        
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var fileName = file.name;
            var fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            
            preview.append('<div class="file-item">' + fileName + ' (' + fileSize + ')</div>');
        }
    });
    
    // Tour card hover effects
    $('.tour-card').hover(
        function() {
            $(this).addClass('hovered');
        },
        function() {
            $(this).removeClass('hovered');
        }
    );
    
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 600);
        }
    });
    
    // Loading states for buttons
    $('.btn').on('click', function() {
        var btn = $(this);
        if (btn.attr('type') === 'submit' && !btn.hasClass('no-loading')) {
            btn.addClass('loading');
            setTimeout(function() {
                btn.removeClass('loading');
            }, 3000);
        }
    });
    
    // Auto-hide success/error messages
    setTimeout(function() {
        $('.notice-success, .notice-error').fadeOut(500);
    }, 5000);
    
});

// Utility functions
function formatPrice(price) {
    return '₺' + price.toLocaleString('tr-TR');
}

function formatDate(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString('tr-TR');
}

function showLoading(element) {
    $(element).html('<div class="loading">Yükleniyor...</div>');
}

function hideLoading(element) {
    $(element).find('.loading').remove();
}