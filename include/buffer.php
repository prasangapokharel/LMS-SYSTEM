<?php
/**
 * School LMS - Ultra Fast Smooth Loader
 * include/loader.php
 * 
 * Ultra-fast, lightweight loader with transparent background
 * No conflicts guaranteed with isolated CSS namespace
 */
?>

<div id="lms-ultrafast-loader" class="lms-ultrafast-overlay">
    <div class="lms-ultrafast-container">
        <div class="lms-ultrafast-spinner"></div>
        <div class="lms-ultrafast-text">
            <span class="lms-loading-dots">Loading</span>
        </div>
    </div>
</div>

<style>
/* ========================================
   ULTRA FAST LMS LOADER - NO CONFLICTS
   ======================================== */

/* Main overlay with transparent background */
.lms-ultrafast-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(248, 250, 252, 0.75) !important;
    backdrop-filter: blur(6px) !important;
    -webkit-backdrop-filter: blur(6px) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 99999 !important;
    opacity: 1 !important;
    visibility: visible !important;
    transition: opacity 0.25s ease, visibility 0.25s ease !important;
    pointer-events: auto !important;
    box-sizing: border-box !important;
}

.lms-ultrafast-overlay.lms-fade-out {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
}

/* Container for centering */
.lms-ultrafast-container {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    text-align: center !important;
    animation: lms-fadeInUp 0.3s ease-out !important;
    box-sizing: border-box !important;
}

/* Ultra fast spinner */
.lms-ultrafast-spinner {
    width: 40px !important;
    height: 40px !important;
    display: grid !important;
    border-radius: 50% !important;
    -webkit-mask: radial-gradient(farthest-side, #0000 40%, #474bff 41%) !important;
    mask: radial-gradient(farthest-side, #0000 40%, #474bff 41%) !important;
    background: linear-gradient(0deg, rgba(71, 75, 255, 0.5) 50%, rgba(71, 75, 255, 1) 0) center/3.2px 100%,
                linear-gradient(90deg, rgba(71, 75, 255, 0.25) 50%, rgba(71, 75, 255, 0.75) 0) center/100% 3.2px !important;
    background-repeat: no-repeat !important;
    animation: lms-spinner-ultrafast 0.6s infinite steps(12) !important;
    margin-bottom: 12px !important;
    box-sizing: border-box !important;
}

.lms-ultrafast-spinner::before,
.lms-ultrafast-spinner::after {
    content: "" !important;
    grid-area: 1/1 !important;
    border-radius: 50% !important;
    background: inherit !important;
    opacity: 0.915 !important;
    transform: rotate(30deg) !important;
    box-sizing: border-box !important;
}

.lms-ultrafast-spinner::after {
    opacity: 0.83 !important;
    transform: rotate(60deg) !important;
}

/* Loading text */
.lms-ultrafast-text {
    color: #4a5568 !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.4 !important;
    letter-spacing: 0.5px !important;
    box-sizing: border-box !important;
}

.lms-loading-dots::after {
    content: '' !important;
    animation: lms-dots 1.4s infinite !important;
}

/* ========================================
   KEYFRAME ANIMATIONS
   ======================================== */

@keyframes lms-spinner-ultrafast {
    100% {
        transform: rotate(1turn);
    }
}

@keyframes lms-fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(15px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes lms-dots {
    0%, 20% {
        content: '';
    }
    40% {
        content: '.';
    }
    60% {
        content: '..';
    }
    80%, 100% {
        content: '...';
    }
}

/* ========================================
   RESPONSIVE & MOBILE OPTIMIZED
   ======================================== */

@media (max-width: 768px) {
    .lms-ultrafast-spinner {
        width: 35px !important;
        height: 35px !important;
        margin-bottom: 10px !important;
    }
    
    .lms-ultrafast-text {
        font-size: 13px !important;
    }
}

@media (max-width: 480px) {
    .lms-ultrafast-spinner {
        width: 32px !important;
        height: 32px !important;
    }
    
    .lms-ultrafast-text {
        font-size: 12px !important;
    }
}

/* ========================================
   ACCESSIBILITY & PERFORMANCE
   ======================================== */

@media (prefers-reduced-motion: reduce) {
    .lms-ultrafast-spinner {
        animation-duration: 1.2s !important;
    }
    
    .lms-ultrafast-container {
        animation: none !important;
    }
    
    .lms-loading-dots::after {
        animation-duration: 2s !important;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .lms-ultrafast-overlay {
        background: rgba(15, 23, 42, 0.80) !important;
    }
    
    .lms-ultrafast-text {
        color: #e2e8f0 !important;
    }
}

/* Prevent body scroll when loader is active */
body.lms-loading-active {
    overflow: hidden !important;
    position: fixed !important;
    width: 100% !important;
    height: 100% !important;
}

/* ========================================
   HIGH SPECIFICITY RESET (NO CONFLICTS)
   ======================================== */

.lms-ultrafast-overlay * {
    box-sizing: border-box !important;
    margin: 0 !important;
    padding: 0 !important;
}

.lms-ultrafast-overlay,
.lms-ultrafast-overlay *,
.lms-ultrafast-overlay *::before,
.lms-ultrafast-overlay *::after {
    -webkit-animation-fill-mode: both !important;
    animation-fill-mode: both !important;
    -webkit-backface-visibility: hidden !important;
    backface-visibility: hidden !important;
}
</style>

<script>
/**
 * Ultra Fast LMS Loader Controller
 * Optimized for performance with zero conflicts
 */

(function() {
    'use strict';
    
    // Namespace to prevent conflicts
    window.LMSUltraLoader = {
        loader: null,
        isActive: false,
        
        init: function() {
            this.loader = document.getElementById('lms-ultrafast-loader');
            if (!this.loader) return;
            
            this.isActive = true;
            document.body.classList.add('lms-loading-active');
            
            // Auto-hide when page loads
            if (document.readyState === 'complete') {
                setTimeout(() => this.hide(), 300);
            } else {
                window.addEventListener('load', () => {
                    setTimeout(() => this.hide(), 300);
                });
            }
            
            // Safety timeout - force hide after 4 seconds
            setTimeout(() => {
                if (this.isActive) this.hide();
            }, 4000);
        },
        
        show: function() {
            if (!this.loader) {
                // Create loader if doesn't exist
                const loaderHTML = `
                    <div id="lms-ultrafast-loader" class="lms-ultrafast-overlay">
                        <div class="lms-ultrafast-container">
                            <div class="lms-ultrafast-spinner"></div>
                            <div class="lms-ultrafast-text">
                                <span class="lms-loading-dots">Loading</span>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', loaderHTML);
                this.loader = document.getElementById('lms-ultrafast-loader');
            }
            
            if (this.loader) {
                this.loader.classList.remove('lms-fade-out');
                document.body.classList.add('lms-loading-active');
                this.isActive = true;
            }
        },
        
        hide: function() {
            if (this.loader && this.isActive) {
                this.loader.classList.add('lms-fade-out');
                document.body.classList.remove('lms-loading-active');
                this.isActive = false;
                
                // Remove from DOM after animation
                setTimeout(() => {
                    if (this.loader && this.loader.parentNode) {
                        this.loader.parentNode.removeChild(this.loader);
                        this.loader = null;
                    }
                }, 250);
            }
        },
        
        toggle: function() {
            if (this.isActive) {
                this.hide();
            } else {
                this.show();
            }
        }
    };
    
    // Auto-initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.LMSUltraLoader.init();
        });
    } else {
        window.LMSUltraLoader.init();
    }
    
    // Global convenience functions
    window.showLMSLoader = function() {
        window.LMSUltraLoader.show();
    };
    
    window.hideLMSLoader = function() {
        window.LMSUltraLoader.hide();
    };
    
    // AJAX integration (jQuery if available)
    if (typeof $ !== 'undefined' && $.ajaxSetup) {
        $(document).ajaxStart(function() {
            window.LMSUltraLoader.show();
        });
        $(document).ajaxStop(function() {
            window.LMSUltraLoader.hide();
        });
    }
    
    // Vanilla JS Fetch wrapper
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            window.LMSUltraLoader.show();
            return originalFetch.apply(this, args)
                .finally(() => {
                    setTimeout(() => window.LMSUltraLoader.hide(), 200);
                });
        };
    }
    
})();
</script>

<?php
/**
 * PHP Helper Functions
 */

function lms_include_ultrafast_loader($auto_hide = true) {
    static $included = false;
    if (!$included) {
        // Loader is already included above
        $included = true;
        
        if (!$auto_hide) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    window.LMSUltraLoader.show();
                });
            </script>';
        }
    }
}

function lms_show_loader_and_redirect($url, $delay = 800) {
    echo '<script>
        window.LMSUltraLoader.show();
        setTimeout(function() {
            window.location.href = "' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '";
        }, ' . intval($delay) . ');
    </script>';
}

function lms_add_loader_to_forms($selector = 'form') {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const forms = document.querySelectorAll("' . addslashes($selector) . '");
            forms.forEach(function(form) {
                form.addEventListener("submit", function() {
                    window.LMSUltraLoader.show();
                });
            });
        });
    </script>';
}

function lms_add_loader_to_links($selector = 'a[href]:not([href^="#"]):not([target="_blank"])') {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const links = document.querySelectorAll("' . addslashes($selector) . '");
            links.forEach(function(link) {
                link.addEventListener("click", function(e) {
                    const href = this.getAttribute("href");
                    if (href && href !== "#" && !href.startsWith("javascript:")) {
                        window.LMSUltraLoader.show();
                    }
                });
            });
        });
    </script>';
}
?>
