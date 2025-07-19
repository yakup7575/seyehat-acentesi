/**
 * Seyahat Marketplace Frontend JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Initialize marketplace functionality
        SeyahatMarketplace.init();
        
    });

    // Main marketplace object
    var SeyahatMarketplace = {
        
        init: function() {
            this.initBooking();
            this.initSearch();
            this.initVendorRegistration();
            this.initTourFilters();
        },
        
        // Booking functionality
        initBooking: function() {
            $('.btn-book-tour').on('click', function(e) {
                e.preventDefault();
                
                var tourId = $(this).data('tour-id');
                
                if (!seyahat_marketplace.user_logged_in) {
                    alert(seyahat_marketplace.strings.login_required);
                    window.location.href = seyahat_marketplace.login_url;
                    return;
                }
                
                SeyahatMarketplace.showBookingModal(tourId);
            });
        },
        
        // Search functionality
        initSearch: function() {
            var searchTimeout;
            
            $('.seyahat-search-form input[name="destination"]').on('input', function() {
                var query = $(this).val();
                
                clearTimeout(searchTimeout);
                
                if (query.length >= 3) {
                    searchTimeout = setTimeout(function() {
                        SeyahatMarketplace.performSearch(query);
                    }, 500);
                }
            });
        },
        
        // Vendor registration
        initVendorRegistration: function() {
            $('#vendor-registration-form').on('submit', function(e) {
                e.preventDefault();
                
                var formData = {
                    action: 'register_vendor',
                    nonce: seyahat_marketplace.nonce,
                    company_name: $('#company_name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    address: $('#address').val(),
                    tax_number: $('#tax_number').val(),
                };
                
                $.ajax({
                    url: seyahat_marketplace.ajax_url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#vendor-registration-form').addClass('loading');
                    },
                    success: function(response) {
                        if (response.success) {
                            SeyahatMarketplace.showNotification(response.data.message, 'success');
                            $('#vendor-registration-form')[0].reset();
                        } else {
                            SeyahatMarketplace.showNotification(response.data.message, 'error');
                        }
                    },
                    error: function() {
                        SeyahatMarketplace.showNotification(seyahat_marketplace.strings.error, 'error');
                    },
                    complete: function() {
                        $('#vendor-registration-form').removeClass('loading');
                    }
                });
            });
        },
        
        // Tour filters
        initTourFilters: function() {
            $('.tour-filters').on('change', 'select, input', function() {
                SeyahatMarketplace.filterTours();
            });
        },
        
        // Show booking modal
        showBookingModal: function(tourId) {
            // Create booking modal
            var modal = $('<div class="booking-modal">').html(`
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>${seyahat_marketplace.strings.book_tour}</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="booking-form">
                            <div class="form-group">
                                <label for="tour_date">${seyahat_marketplace.strings.tour_date}</label>
                                <input type="date" id="tour_date" name="tour_date" required />
                            </div>
                            <div class="form-group">
                                <label for="guests">${seyahat_marketplace.strings.guests}</label>
                                <select id="guests" name="guests" required>
                                    <option value="1">1 ${seyahat_marketplace.strings.person}</option>
                                    <option value="2">2 ${seyahat_marketplace.strings.person}</option>
                                    <option value="3">3 ${seyahat_marketplace.strings.person}</option>
                                    <option value="4">4+ ${seyahat_marketplace.strings.person}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">${seyahat_marketplace.strings.confirm_booking}</button>
                        </form>
                    </div>
                </div>
            `);
            
            $('body').append(modal);
            modal.fadeIn();
            
            // Handle modal close
            modal.on('click', '.modal-close, .booking-modal', function(e) {
                if (e.target === this) {
                    modal.fadeOut(function() {
                        modal.remove();
                    });
                }
            });
            
            // Handle booking form submission
            modal.on('submit', '#booking-form', function(e) {
                e.preventDefault();
                
                var formData = {
                    action: 'create_booking',
                    nonce: seyahat_marketplace.nonce,
                    tour_id: tourId,
                    tour_date: $('#tour_date').val(),
                    guests: $('#guests').val(),
                };
                
                $.ajax({
                    url: seyahat_marketplace.ajax_url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#booking-form').addClass('loading');
                    },
                    success: function(response) {
                        if (response.success) {
                            SeyahatMarketplace.showNotification(response.data.message, 'success');
                            modal.fadeOut(function() {
                                modal.remove();
                            });
                            
                            if (response.data.redirect_url) {
                                setTimeout(function() {
                                    window.location.href = response.data.redirect_url;
                                }, 2000);
                            }
                        } else {
                            SeyahatMarketplace.showNotification(response.data.message, 'error');
                        }
                    },
                    error: function() {
                        SeyahatMarketplace.showNotification(seyahat_marketplace.strings.error, 'error');
                    },
                    complete: function() {
                        $('#booking-form').removeClass('loading');
                    }
                });
            });
        },
        
        // Perform search
        performSearch: function(query) {
            $.ajax({
                url: seyahat_marketplace.ajax_url,
                type: 'POST',
                data: {
                    action: 'seyahat_search',
                    nonce: seyahat_marketplace.nonce,
                    query: query
                },
                success: function(response) {
                    if (response.success) {
                        SeyahatMarketplace.displaySearchResults(response.data);
                    }
                }
            });
        },
        
        // Display search results
        displaySearchResults: function(results) {
            var resultsContainer = $('.search-results');
            
            if (!resultsContainer.length) {
                resultsContainer = $('<div class="search-results">').insertAfter('.seyahat-search-form');
            }
            
            var html = '<div class="search-results-content">';
            
            if (results.tours && results.tours.length > 0) {
                html += '<h4>' + seyahat_marketplace.strings.tours + '</h4>';
                html += '<div class="search-tours">';
                
                results.tours.forEach(function(tour) {
                    html += `
                        <div class="search-result-item">
                            <div class="result-thumbnail">
                                ${tour.thumbnail ? '<img src="' + tour.thumbnail + '" alt="' + tour.title + '">' : ''}
                            </div>
                            <div class="result-content">
                                <h5><a href="${tour.permalink}">${tour.title}</a></h5>
                                <p class="result-location">${tour.location}</p>
                                <p class="result-price">${tour.price} ${tour.currency}</p>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
            }
            
            if (results.destinations && results.destinations.length > 0) {
                html += '<h4>' + seyahat_marketplace.strings.destinations + '</h4>';
                html += '<div class="search-destinations">';
                
                results.destinations.forEach(function(destination) {
                    html += `
                        <div class="search-result-item">
                            <a href="${destination.link}">${destination.name} (${destination.count})</a>
                        </div>
                    `;
                });
                
                html += '</div>';
            }
            
            html += '</div>';
            
            resultsContainer.html(html);
        },
        
        // Filter tours
        filterTours: function() {
            var filters = {};
            
            $('.tour-filters select, .tour-filters input').each(function() {
                var name = $(this).attr('name');
                var value = $(this).val();
                
                if (value) {
                    filters[name] = value;
                }
            });
            
            $.ajax({
                url: seyahat_marketplace.ajax_url,
                type: 'POST',
                data: {
                    action: 'filter_tours',
                    nonce: seyahat_marketplace.nonce,
                    filters: filters
                },
                beforeSend: function() {
                    $('.tours-grid').addClass('loading');
                },
                success: function(response) {
                    if (response.success) {
                        $('.tours-grid').html(response.data.tours_html);
                    }
                },
                complete: function() {
                    $('.tours-grid').removeClass('loading');
                }
            });
        },
        
        // Show notification
        showNotification: function(message, type) {
            var notification = $('<div class="notification notification-' + type + '">')
                .html('<span>' + message + '</span><button class="notification-close">&times;</button>');
            
            $('body').append(notification);
            
            setTimeout(function() {
                notification.addClass('show');
            }, 100);
            
            notification.on('click', '.notification-close', function() {
                notification.removeClass('show');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            });
            
            // Auto remove after 5 seconds
            setTimeout(function() {
                notification.removeClass('show');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 5000);
        }
    };

})(jQuery);
