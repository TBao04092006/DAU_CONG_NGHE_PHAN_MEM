/**
 * LibraNova Library Management - Client-Side JavaScript
 * Pure Vanilla JavaScript for DOM interactions, VietQR generator, and real-time calculations.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Auto re-initialize Lucide icons if dynamically loaded
    if (window.lucide) {
        window.lucide.createIcons();
    }
});

// Format currency helper
function formatVND(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}
