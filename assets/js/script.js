/**
 * Enhanced JavaScript untuk GDSS Bantuan Rumah
 * Author: Kelompok UAS
 * Version: 2.0 - Enhanced Edition
 */

// ===== DOCUMENT READY =====
$(document).ready(function() {
    // Create floating particles
    createFloatingParticles();
    
    // Initialize all features
    initTooltips();
    initPopovers();
    autoHideAlerts();
    confirmDelete();
    formValidation();
    smoothScroll();
    setupLoading();
    tableSearch();
    setupPrint();
    setupMobileSidebar();
    animateOnScroll();
    
    console.log('%c🏠 GDSS System Enhanced Initialized ', 
        'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 18px; padding: 10px; border-radius: 5px;');
});

// ===== CREATE FLOATING PARTICLES =====
function createFloatingParticles() {
    const particlesContainer = $('<div class="particles"></div>');
    $('body').prepend(particlesContainer);
    
    for (let i = 0; i < 30; i++) {
        const particle = $('<div class="particle"></div>');
        const size = Math.random() * 5 + 2;
        const startLeft = Math.random() * 100;
        const duration = Math.random() * 10 + 15;
        const delay = Math.random() * 5;
        
        particle.css({
            width: size + 'px',
            height: size + 'px',
            left: startLeft + '%',
            bottom: '-10px',
            animationDuration: duration + 's',
            animationDelay: delay + 's'
        });
        
        particlesContainer.append(particle);
    }
}

// ===== INITIALIZE TOOLTIPS =====
function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            animation: true,
            delay: { show: 100, hide: 100 }
        });
    });
}

// ===== INITIALIZE POPOVERS =====
function initPopovers() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            animation: true,
            trigger: 'hover'
        });
    });
}

// ===== AUTO HIDE ALERTS WITH ANIMATION =====
function autoHideAlerts() {
    setTimeout(function() {
        $('.alert').each(function(index) {
            const $alert = $(this);
            setTimeout(function() {
                $alert.addClass('animate__animated animate__fadeOutRight');
                setTimeout(function() {
                    $alert.remove();
                }, 500);
            }, index * 200);
        });
    }, 5000);
}

// ===== CONFIRM DELETE WITH SWEET ANIMATION =====
function confirmDelete() {
    $('.btn-delete, a[href*="hapus"]').on('click', function(e) {
        e.preventDefault();
        const $this = $(this);
        
        // Create custom confirm dialog
        const confirmDialog = $(`
            <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                Konfirmasi Hapus
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">⚠️ Yakin ingin menghapus data ini?</p>
                            <p class="text-muted small">Data yang dihapus tidak dapat dikembalikan!</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                                <i class="bi bi-trash"></i> Ya, Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(confirmDialog);
        const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        modal.show();
        
        $('#confirmDeleteBtn').on('click', function() {
            modal.hide();
            showLoading();
            setTimeout(function() {
                window.location.href = $this.attr('href');
            }, 500);
        });
        
        $('#confirmDeleteModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    });
}

// ===== FORM VALIDATION =====
function formValidation() {
    var forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Shake animation for invalid form
                $(form).addClass('animate__animated animate__shakeX');
                setTimeout(function() {
                    $(form).removeClass('animate__animated animate__shakeX');
                }, 500);
            }
            form.classList.add('was-validated');
        }, false);
    });
}

// ===== SMOOTH SCROLL =====
function smoothScroll() {
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && 
            location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 1000, 'easeInOutCubic');
                return false;
            }
        }
    });
}

// ===== ENHANCED LOADING ANIMATION =====
function setupLoading() {
    $('form').on('submit', function() {
        showLoading();
    });
    
    $('.btn-loading').on('click', function() {
        showLoading();
    });
}

function showLoading() {
    var loadingHtml = `
        <div class="loading-overlay">
            <div class="text-center">
                <div class="spinner-border text-light mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-white fw-bold">Memproses...</p>
            </div>
        </div>
    `;
    $('body').append(loadingHtml);
    $('.loading-overlay').hide().fadeIn(300);
}

function hideLoading() {
    $('.loading-overlay').fadeOut(300, function() {
        $(this).remove();
    });
}

// ===== TABLE SEARCH WITH HIGHLIGHT =====
function tableSearch() {
    $('#tableSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#dataTable tbody tr').each(function() {
            var $row = $(this);
            var text = $row.text().toLowerCase();
            
            if (text.indexOf(value) > -1) {
                $row.show().addClass('animate__animated animate__fadeIn');
                
                // Highlight matching text
                $row.find('td').each(function() {
                    var $td = $(this);
                    var html = $td.html();
                    var highlighted = html.replace(
                        new RegExp(value, 'gi'),
                        '<mark class="bg-warning">$&</mark>'
                    );
                    $td.html(highlighted);
                });
            } else {
                $row.hide();
            }
        });
    });
}

// ===== PRINT FUNCTIONALITY =====
function setupPrint() {
    $('.btn-print').on('click', function() {
        // Add print styles
        $('body').addClass('printing');
        window.print();
        setTimeout(function() {
            $('body').removeClass('printing');
        }, 1000);
    });
}

// ===== MOBILE SIDEBAR TOGGLE =====
function setupMobileSidebar() {
    if ($(window).width() <= 768) {
        if (!$('.sidebar-toggle').length) {
            $('.top-navbar').prepend(`
                <button class="btn btn-primary sidebar-toggle animate__animated animate__fadeIn">
                    <i class="bi bi-list"></i>
                </button>
            `);
        }
    }
    
    $(document).on('click', '.sidebar-toggle', function() {
        $('.sidebar').toggleClass('show');
        $(this).find('i').toggleClass('bi-list bi-x');
    });
    
    $(document).on('click', function(e) {
        if ($(window).width() <= 768) {
            if (!$(e.target).closest('.sidebar, .sidebar-toggle').length) {
                $('.sidebar').removeClass('show');
                $('.sidebar-toggle i').removeClass('bi-x').addClass('bi-list');
            }
        }
    });
}

// ===== ANIMATE ON SCROLL =====
function animateOnScroll() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    document.querySelectorAll('.card, .stat-card').forEach((el) => {
        observer.observe(el);
    });
}

// ===== COUNTER ANIMATION =====
$('.counter').each(function() {
    var $this = $(this);
    var countTo = $this.attr('data-count');
    
    $({ countNum: 0 }).animate({
        countNum: countTo
    }, {
        duration: 2000,
        easing: 'swing',
        step: function() {
            $this.text(Math.floor(this.countNum));
        },
        complete: function() {
            $this.text(this.countNum);
        }
    });
});

// ===== SHOW NOTIFICATION WITH ANIMATION =====
function showNotification(message, type = 'info') {
    const icons = {
        'success': 'check-circle-fill',
        'danger': 'x-circle-fill',
        'warning': 'exclamation-triangle-fill',
        'info': 'info-circle-fill'
    };
    
    var alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed animate__animated animate__bounceInRight" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);" 
             role="alert">
            <i class="bi bi-${icons[type]} me-2"></i> 
            <strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(alertHtml);
    
    setTimeout(function() {
        $('.alert').addClass('animate__bounceOutRight');
        setTimeout(function() {
            $('.alert').remove();
        }, 500);
    }, 3000);
}

// ===== BACK TO TOP BUTTON =====
$(window).scroll(function() {
    if ($(this).scrollTop() > 300) {
        $('#backToTop').fadeIn().addClass('animate__animated animate__bounceIn');
    } else {
        $('#backToTop').fadeOut();
    }
});

$('#backToTop').click(function() {
    $('html, body').animate({scrollTop: 0}, 800);
    return false;
});

// Create back to top button if not exists
if (!$('#backToTop').length) {
    $('body').append(`
        <button id="backToTop" class="btn btn-primary" style="display: none;">
            <i class="bi bi-arrow-up"></i>
        </button>
    `);
}

// ===== CARD HOVER EFFECT =====
$('.card').hover(
    function() {
        $(this).addClass('animate__animated animate__pulse');
    },
    function() {
        $(this).removeClass('animate__animated animate__pulse');
    }
);

// ===== TABLE ROW CLICK EFFECT =====
$('.table tbody tr').on('click', function() {
    $(this).addClass('animate__animated animate__headShake');
    setTimeout(() => {
        $(this).removeClass('animate__animated animate__headShake');
    }, 500);
});

// ===== BUTTON RIPPLE EFFECT =====
$('.btn').on('click', function(e) {
    var $btn = $(this);
    var x = e.pageX - $btn.offset().left;
    var y = e.pageY - $btn.offset().top;
    
    var ripple = $('<span class="ripple"></span>');
    ripple.css({
        position: 'absolute',
        width: '0',
        height: '0',
        borderRadius: '50%',
        background: 'rgba(255, 255, 255, 0.5)',
        left: x + 'px',
        top: y + 'px',
        transform: 'translate(-50%, -50%)',
        animation: 'ripple 0.6s ease-out'
    });
    
    $btn.append(ripple);
    
    setTimeout(function() {
        ripple.remove();
    }, 600);
});

// Add ripple animation to stylesheet
if (!$('style#rippleAnimation').length) {
    $('head').append(`
        <style id="rippleAnimation">
            @keyframes ripple {
                to {
                    width: 200px;
                    height: 200px;
                    opacity: 0;
                }
            }
        </style>
    `);
}

// ===== PROGRESS BAR ANIMATION =====
$('.progress-bar').each(function() {
    var $bar = $(this);
    var width = $bar.attr('aria-valuenow');
    $bar.css('width', '0%');
    
    setTimeout(function() {
        $bar.animate({
            width: width + '%'
        }, 1500, 'easeOutCubic');
    }, 200);
});

// ===== FORM INPUT ANIMATION =====
$('.form-control, .form-select').on('focus', function() {
    $(this).parent().addClass('animate__animated animate__pulse');
}).on('blur', function() {
    $(this).parent().removeClass('animate__animated animate__pulse');
});

// ===== BADGE ANIMATION =====
$('.badge').each(function(index) {
    $(this).css({
        animationDelay: (index * 0.1) + 's'
    });
});

// ===== PREVENT DOUBLE SUBMIT =====
$('form').on('submit', function() {
    var $form = $(this);
    if ($form.data('submitted') === true) {
        return false;
    }
    $form.data('submitted', true);
    $form.find('button[type="submit"]').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Memproses...');
    
    setTimeout(function() {
        $form.data('submitted', false);
        $form.find('button[type="submit"]').prop('disabled', false);
    }, 3000);
});

// ===== KEYBOARD SHORTCUTS =====
$(document).on('keydown', function(e) {
    // Ctrl + P: Print
    if (e.ctrlKey && e.keyCode === 80) {
        e.preventDefault();
        window.print();
    }
    
    // Ctrl + S: Save
    if (e.ctrlKey && e.keyCode === 83) {
        e.preventDefault();
        $('form').first().submit();
    }
    
    // ESC: Close modal
    if (e.keyCode === 27) {
        $('.modal').modal('hide');
        $('.sidebar').removeClass('show');
    }
});

// ===== PARALLAX EFFECT FOR BACKGROUND =====
$(window).scroll(function() {
    var scrolled = $(window).scrollTop();
    $('body').css('background-position', '0 ' + (scrolled * 0.3) + 'px');
});

// ===== CONSOLE LOG STYLING =====
console.log('%c🏠 GDSS Bantuan Rumah - Enhanced Edition', 
    'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 24px; padding: 15px; border-radius: 10px; font-weight: bold;');
console.log('%c✨ Group Decision Support System v2.0', 
    'color: #667eea; font-size: 16px; font-weight: bold;');
console.log('%c🚀 Enhanced with Advanced Animations & Effects', 
    'color: #764ba2; font-size: 14px;');
console.log('%c📅 Version 2.0 - 2024', 
    'color: #666; font-size: 12px;');

// ===== PARTICLE MOUSE FOLLOW EFFECT =====
$(document).mousemove(function(e) {
    var particle = $('<div class="mouse-particle"></div>');
    particle.css({
        position: 'fixed',
        width: '5px',
        height: '5px',
        borderRadius: '50%',
        background: 'rgba(102, 126, 234, 0.6)',
        pointerEvents: 'none',
        left: e.pageX + 'px',
        top: e.pageY + 'px',
        zIndex: 9998,
        animation: 'particleFade 1s ease-out forwards'
    });
    
    $('body').append(particle);
    
    setTimeout(function() {
        particle.remove();
    }, 1000);
});

// Add particle fade animation
if (!$('style#particleFadeAnimation').length) {
    $('head').append(`
        <style id="particleFadeAnimation">
            @keyframes particleFade {
                to {
                    transform: scale(3);
                    opacity: 0;
                }
            }
        </style>
    `);
}

// ===== LOAD ANIMATE.CSS IF NOT LOADED =====
if (!$('link[href*="animate.css"]').length) {
    $('head').append('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">');
}

// ===== END OF ENHANCED JAVASCRIPT =====