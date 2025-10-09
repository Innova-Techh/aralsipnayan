{{-- SweetAlert2 Toast Configuration Component --}}
{{-- Usage: @include('components.sweetalert-config') --}}

<style>
    /* SweetAlert Toast 3D Styles - Responsive */
    .custom-toast-container {
        font-family: 'Baloo 2', cursive !important;
        margin-right: 20px !important; /* Add spacing from right edge */
        margin-bottom: 0 !important;
    }

    .custom-toast-container .swal2-popup {
        border-radius: 16px !important;
        box-shadow: 
            0 10px 25px -5px rgba(0, 0, 0, 0.3),
            0 8px 10px -6px rgba(0, 0, 0, 0.2),
            inset 0 -3px 0 rgba(0, 0, 0, 0.2) !important;
        border: none !important;
        padding: 1rem 2.5rem 1rem 1rem !important; /* Extra right padding for close button */
        backdrop-filter: none !important;
        background: #fff !important;
        transform: translateZ(0);
        transition: all 0.3s ease !important;
        margin-bottom: 80px !important; /* Move up from bottom */
    }

    .custom-toast-container .swal2-popup:hover {
        transform: translateY(-2px) translateZ(0);
        box-shadow: 
            0 15px 30px -5px rgba(0, 0, 0, 0.35),
            0 10px 15px -6px rgba(0, 0, 0, 0.25),
            inset 0 -3px 0 rgba(0, 0, 0, 0.2) !important;
    }

    /* Close Button Styling */
    .custom-toast-container .swal2-close {
        position: absolute !important;
        top: 0.5rem !important;
        right: 0.5rem !important;
        width: 2.1em !important;
        height: 2.1em !important;
        font-size: 1.5rem !important;
        line-height: 2.1em !important;
        color: #6B7280 !important;
        background: transparent !important;
        border: none !important;
        border-radius: 50% !important;
        transition: all 0.2s ease !important;
        box-shadow: none !important;
        opacity: 0.6 !important;
        cursor: pointer !important;
        padding: 0 !important;
        margin: 0 !important;
        z-index: 2 !important; /* Ensure it's above other content */
    }

    .custom-toast-container .swal2-close:hover {
        color: #374151 !important;
        background: rgba(0, 0, 0, 0.05) !important;
        opacity: 1 !important;
        transform: scale(1.1) !important;
    }

    .custom-toast-container .swal2-close:focus {
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.3) !important;
    }

    /* Icon Styling with 3D effect */
    .custom-toast-container .swal2-icon {
        margin: 0.5rem auto 0.5rem !important;
        border-width: 3px !important;
        box-shadow: 
            0 4px 8px rgba(0, 0, 0, 0.2),
            inset 0 -2px 0 rgba(0, 0, 0, 0.1) !important;
    }

    .custom-toast-container .swal2-icon.swal2-warning {
        border-color: #9333EA !important;
        color: #9333EA !important;
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%) !important;
    }

    .custom-toast-container .swal2-icon.swal2-error {
        border-color: #DC2626 !important;
        color: #DC2626 !important;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%) !important;
    }

    .custom-toast-container .swal2-icon.swal2-success {
        border-color: #059669 !important;
        color: #059669 !important;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%) !important;
    }

    .custom-toast-container .swal2-icon.swal2-info {
        border-color: #2563EB !important;
        color: #2563EB !important;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
    }

    /* Title Styling */
    .custom-toast-container .swal2-title {
        font-family: 'Baloo 2', cursive !important;
        font-weight: 600 !important;
        color: #1F2937 !important;
        padding: 0.25rem 0.5rem 0.25rem 0.5rem !important;
        margin: 0 !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
        max-width: calc(100% - 2rem) !important; /* Prevent overlap with close button */
        word-wrap: break-word !important;
    }

    /* Timer Progress Bar with 3D effect */
    .custom-toast-container .swal2-timer-progress-bar {
        background: linear-gradient(90deg, #9333EA 0%, #7C3AED 100%) !important;
        height: 4px !important;
        box-shadow: 0 2px 4px rgba(147, 51, 234, 0.3) !important;
    }

    /* Desktop Screens (1920px and above) */
    @media (min-width: 1920px) {
        .custom-toast-container .swal2-popup {
            width: 380px !important;
            min-height: 90px !important;
        }
        .custom-toast-container .swal2-icon {
            width: 2.5em !important;
            height: 2.5em !important;
        }
        .custom-toast-container .swal2-title {
            font-size: 1.1rem !important;
            line-height: 1.4 !important;
        }
    }

    /* Laptop Screens (1024px - 1919px) */
    @media (min-width: 1024px) and (max-width: 1919px) {
        .custom-toast-container .swal2-popup {
            width: 340px !important;
            min-height: 85px !important;
        }
        .custom-toast-container .swal2-icon {
            width: 2.3em !important;
            height: 2.3em !important;
        }
        .custom-toast-container .swal2-title {
            font-size: 1rem !important;
            line-height: 1.4 !important;
        }
    }

    /* Tablet Screens (768px - 1023px) */
    @media (min-width: 768px) and (max-width: 1023px) {
        .custom-toast-container .swal2-popup {
            width: 320px !important;
            min-height: 80px !important;
        }
        .custom-toast-container .swal2-icon {
            width: 2.2em !important;
            height: 2.2em !important;
        }
        .custom-toast-container .swal2-title {
            font-size: 0.95rem !important;
            line-height: 1.3 !important;
        }
    }

    /* Large Phone (640px - 767px) */
    @media (min-width: 640px) and (max-width: 767px) {
        .custom-toast-container .swal2-popup {
            width: 300px !important;
            min-height: 75px !important;
        }
        .custom-toast-container .swal2-icon {
            width: 2em !important;
            height: 2em !important;
        }
        .custom-toast-container .swal2-title {
            font-size: 0.9rem !important;
            line-height: 1.3 !important;
        }
    }

    /* Medium Phone (480px - 639px) */
    @media (min-width: 480px) and (max-width: 639px) {
        .custom-toast-container .swal2-popup {
            width: 280px !important;
            min-height: 70px !important;
        }
        .custom-toast-container .swal2-icon {
            width: 1.8em !important;
            height: 1.8em !important;
        }
        .custom-toast-container .swal2-title {
            font-size: 0.85rem !important;
            line-height: 1.3 !important;
        }
    }

    /* Small Phone (below 480px) */
    @media (max-width: 479px) {
        .custom-toast-container .swal2-popup {
            width: 260px !important;
            min-height: 65px !important;
        }
        .custom-toast-container .swal2-icon {
            width: 1.6em !important;
            height: 1.6em !important;
        }
        .custom-toast-container .swal2-title {
            font-size: 0.8rem !important;
            line-height: 1.2 !important;
        }
    }

    /* Animation for toast entrance */
    @keyframes slideInRight {
        from {
            transform: translateX(100%) translateZ(0);
            opacity: 0;
        }
        to {
            transform: translateX(0) translateZ(0);
            opacity: 1;
        }
    }

    @keyframes slideInUp {
        from {
            transform: translateY(100%) translateZ(0);
            opacity: 0;
        }
        to {
            transform: translateY(0) translateZ(0);
            opacity: 1;
        }
    }

    .custom-toast-container .swal2-show {
        animation: slideInUp 0.3s ease-out !important;
    }
</style>

<script>
    /**
     * Show Custom Toast Notification
     * @param {string} type - 'warning', 'error', 'success', 'info'
     * @param {string} message - The message to display
     * @param {number} timer - Duration in milliseconds (default: 3500)
     */
    window.showCustomToast = function(type = 'warning', message = '', timer = 3500) {
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 is not loaded. Make sure to include Vite assets.');
            return;
        }

        const Toast = Swal.mixin({
            toast: true,
            position: "bottom-end",
            showConfirmButton: false,
            showCloseButton: true,
            timer: timer,
            timerProgressBar: true,
            customClass: {
                container: 'custom-toast-container',
                popup: 'custom-toast-popup',
                title: 'custom-toast-title',
                icon: 'custom-toast-icon',
                closeButton: 'custom-toast-close'
            },
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // Icon color mapping for solid colors
        const iconColors = {
            warning: '#9333EA',
            error: '#DC2626',
            success: '#059669',
            info: '#2563EB'
        };

        Toast.fire({
            icon: type,
            title: message,
            iconColor: iconColors[type] || '#9333EA',
            background: '#ffffff',
            color: '#1F2937'
        });
    };

    // Specific helper functions for common use cases
    window.showWarningToast = function(message, timer = 3500) {
        window.showCustomToast('warning', message, timer);
    };

    window.showErrorToast = function(message, timer = 3500) {
        window.showCustomToast('error', message, timer);
    };

    window.showSuccessToast = function(message, timer = 3500) {
        window.showCustomToast('success', message, timer);
    };

    window.showInfoToast = function(message, timer = 3500) {
        window.showCustomToast('info', message, timer);
    };
</script>
