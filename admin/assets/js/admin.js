/**
 * MAISON ÉLAN - Admin Console JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {

    // Auto-dismiss alerts after 4 seconds
    const alerts = document.querySelectorAll('.flash-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.4s ease';
            setTimeout(() => alert.remove(), 400);
        }, 4000);
    });

    // Auto generate SKU from name if empty in product-add
    const prodNameInput = document.getElementById('prodName');
    const prodSkuInput = document.getElementById('prodSku');

    if (prodNameInput && prodSkuInput && !prodSkuInput.value) {
        prodNameInput.addEventListener('blur', () => {
            if (!prodSkuInput.value.trim() && prodNameInput.value.trim()) {
                const words = prodNameInput.value.trim().split(/\s+/);
                const prefix = words.map(w => w[0].toUpperCase()).join('').substring(0, 3) || 'ELN';
                const random = Math.floor(100 + Math.random() * 900);
                prodSkuInput.value = `ME-${prefix}-${random}`;
            }
        });
    }

});
