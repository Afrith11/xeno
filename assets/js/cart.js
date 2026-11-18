/**
 * MAISON ÉLAN - Cart & Wishlist AJAX Manager
 * Pure Vanilla JavaScript
 */

(function() {
    'use strict';

    /* ==========================================================
       1. Global Toast Notifications
       ========================================================== */
    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-item ${type}`;
        toast.innerHTML = `
            <span>${message}</span>
            <button type="button" style="color:inherit; font-size:1.1rem; line-height:1; opacity:0.7;" onclick="this.parentElement.remove()">&times;</button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }
        }, 3500);
    };

    /* ==========================================================
       2. Update Cart & Wishlist Counters
       ========================================================== */
    window.updateCartCounters = function(count) {
        const badge = document.getElementById('cartCountBadge');
        const drawerCount = document.getElementById('drawerCartCount');
        const num = parseInt(count, 10) || 0;

        if (badge) {
            badge.innerText = num;
            badge.style.display = num > 0 ? 'flex' : 'none';
        }
        if (drawerCount) {
            drawerCount.innerText = `(${num})`;
        }
    };

    window.updateWishlistCounters = function(count) {
        const badge = document.getElementById('wishlistCountBadge');
        const num = parseInt(count, 10) || 0;
        if (badge) {
            badge.innerText = num;
            badge.style.display = num > 0 ? 'flex' : 'none';
        }
    };

    /* ==========================================================
       3. Quick Slide-out Cart Drawer Logic
       ========================================================== */
    const cartDrawer = document.getElementById('cartDrawer');
    const cartDrawerOverlay = document.getElementById('cartDrawerOverlay');
    const openCartDrawerBtn = document.getElementById('openCartDrawer');
    const cartDrawerClose = document.getElementById('cartDrawerClose');
    const cartDrawerItems = document.getElementById('cartDrawerItems');
    const cartDrawerEmpty = document.getElementById('cartDrawerEmpty');
    const cartDrawerFooter = document.getElementById('cartDrawerFooter');
    const drawerSubtotal = document.getElementById('drawerSubtotal');

    window.openCartDrawer = function() {
        if (!cartDrawer || !cartDrawerOverlay) return;
        fetchCartDrawerData();
        cartDrawer.classList.add('active');
        cartDrawerOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeCartDrawer = function() {
        if (!cartDrawer || !cartDrawerOverlay) return;
        cartDrawer.classList.remove('active');
        cartDrawerOverlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    if (openCartDrawerBtn) {
        openCartDrawerBtn.addEventListener('click', (e) => {
            // If on cart.php or checkout.php, navigate normally
            if (window.location.pathname.includes('cart.php') || window.location.pathname.includes('checkout.php')) {
                return;
            }
            e.preventDefault();
            window.openCartDrawer();
        });
    }

    if (cartDrawerClose) cartDrawerClose.addEventListener('click', window.closeCartDrawer);
    if (cartDrawerOverlay) cartDrawerOverlay.addEventListener('click', window.closeCartDrawer);

    function fetchCartDrawerData() {
        fetch(`${window.MAISON.baseUrl}/api/cart.php?action=get`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.cart) {
                    renderCartDrawer(data.cart);
                }
            })
            .catch(err => console.error('Cart fetch error:', err));
    }

    function renderCartDrawer(cart) {
        window.updateCartCounters(cart.item_count);

        if (!cart.items || cart.items.length === 0) {
            if (cartDrawerEmpty) cartDrawerEmpty.style.display = 'block';
            if (cartDrawerItems) cartDrawerItems.innerHTML = '';
            if (cartDrawerFooter) cartDrawerFooter.style.display = 'none';
            return;
        }

        if (cartDrawerEmpty) cartDrawerEmpty.style.display = 'none';
        if (cartDrawerFooter) cartDrawerFooter.style.display = 'block';
        if (drawerSubtotal) drawerSubtotal.innerText = cart.formatted.subtotal;

        if (cartDrawerItems) {
            let html = '<div class="drawer-items-list">';
            cart.items.forEach(item => {
                html += `
                    <div class="drawer-item-row" data-key="${item.key}" style="display:flex; gap:14px; margin-bottom:18px; padding-bottom:16px; border-bottom:1px solid var(--border-subtle); align-items:center;">
                        <img src="${item.image}" alt="${item.name}" style="width:64px; height:85px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;">
                        <div style="flex:1;">
                            <h4 style="font-size:0.95rem; margin-bottom:4px; font-weight:500;">${item.name}</h4>
                            <div style="font-size:0.75rem; color:var(--taupe); margin-bottom:6px;">Size: ${item.size}</div>
                            <div style="font-size:0.88rem; font-weight:600;">${window.MAISON.currency}${item.price.toLocaleString('en-IN')} × ${item.quantity}</div>
                        </div>
                        <button type="button" class="drawer-remove-btn" data-key="${item.key}" style="font-size:1.3rem; color:var(--taupe);">&times;</button>
                    </div>
                `;
            });
            html += '</div>';
            cartDrawerItems.innerHTML = html;

            // Bind remove buttons inside drawer
            cartDrawerItems.querySelectorAll('.drawer-remove-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const key = btn.dataset.key;
                    removeItemFromCart(key);
                });
            });
        }
    }

    /* ==========================================================
       4. Add to Cart Handlers
       ========================================================== */
    // Card Add to Bag button
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-add-bag');
        if (btn) {
            e.preventDefault();
            const pid = btn.dataset.productId;
            addToBagDirect(pid, 1, 'Free Size', '');
        }
    });

    // PDP Add to Bag button
    const btnAddToBagPdp = document.getElementById('btnAddToBagPdp');
    const btnBuyNowPdp = document.getElementById('btnBuyNowPdp');
    const productPurchaseForm = document.getElementById('productPurchaseForm');

    if (btnAddToBagPdp && productPurchaseForm) {
        btnAddToBagPdp.addEventListener('click', (e) => {
            e.preventDefault();
            submitPdpForm(false);
        });
    }

    if (btnBuyNowPdp && productPurchaseForm) {
        btnBuyNowPdp.addEventListener('click', (e) => {
            e.preventDefault();
            submitPdpForm(true);
        });
    }

    function submitPdpForm(isBuyNow = false) {
        const formData = new FormData(productPurchaseForm);
        formData.append('action', 'add');

        fetch(`${window.MAISON.baseUrl}/api/cart.php`, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.updateCartCounters(data.cart_count);
                if (isBuyNow) {
                    window.location.href = `${window.MAISON.baseUrl}/checkout.php`;
                } else {
                    window.showToast('Piece added to your shopping bag.', 'success');
                    window.openCartDrawer();
                }
            } else {
                window.showToast(data.message || 'Could not add to bag.', 'error');
            }
        })
        .catch(() => {
            window.showToast('Server communication error.', 'error');
        });
    }

    function addToBagDirect(productId, qty, size, color) {
        const fd = new FormData();
        fd.append('action', 'add');
        fd.append('product_id', productId);
        fd.append('quantity', qty);
        fd.append('size', size);
        fd.append('color', color);

        fetch(`${window.MAISON.baseUrl}/api/cart.php`, {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.updateCartCounters(data.cart_count);
                window.showToast('Silhouette added to shopping bag.', 'success');
                window.openCartDrawer();
            } else {
                window.showToast(data.message || 'Could not add piece.', 'error');
            }
        })
        .catch(() => {
            window.showToast('Could not process bag addition.', 'error');
        });
    }

    function removeItemFromCart(itemKey) {
        const fd = new FormData();
        fd.append('action', 'remove');
        fd.append('item_key', itemKey);

        fetch(`${window.MAISON.baseUrl}/api/cart.php`, {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.cart) {
                renderCartDrawer(data.cart);
                window.showToast('Piece removed from your bag.', 'info');
                // If on cart.php, reload page to sync full table
                if (window.location.pathname.includes('cart.php')) {
                    window.location.reload();
                }
            }
        });
    }

    /* ==========================================================
       5. Wishlist Toggle Handlers
       ========================================================== */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-wishlist, #pdpWishlistToggle');
        if (btn) {
            e.preventDefault();
            const pid = btn.dataset.productId;
            if (!pid) return;

            const fd = new FormData();
            fd.append('product_id', pid);

            fetch(`${window.MAISON.baseUrl}/api/wishlist.php`, {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added') {
                        btn.classList.add('active');
                        const icon = btn.querySelector('.heart-icon');
                        if (icon) icon.setAttribute('fill', 'currentColor');
                    } else {
                        btn.classList.remove('active');
                        const icon = btn.querySelector('.heart-icon');
                        if (icon) icon.setAttribute('fill', 'none');
                    }
                    window.updateWishlistCounters(data.wishlist_count);
                    window.showToast(data.message, 'success');
                }
            })
            .catch(() => {
                window.showToast('Could not update wishlist.', 'error');
            });
        }
    });

    /* ==========================================================
       6. Cart Page Quantity Steppers
       ========================================================== */
    const cartQtyBtns = document.querySelectorAll('.cart-qty-stepper .qty-btn');
    cartQtyBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const key = btn.dataset.key;
            const action = btn.dataset.action;
            const input = btn.parentElement.querySelector('.cart-qty-input');
            let current = parseInt(input.value, 10) || 1;

            if (action === 'increase') {
                current += 1;
            } else if (action === 'decrease') {
                current = Math.max(0, current - 1);
            }

            input.value = current;

            // Submit AJAX update
            const fd = new FormData();
            fd.append('action', 'update');
            fd.append('item_key', key);
            fd.append('quantity', current);

            fetch(`${window.MAISON.baseUrl}/api/cart.php`, {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    window.showToast(data.message || 'Cannot update quantity.', 'error');
                }
            });
        });
    });

    // Remove buttons in cart.php
    const removeCartBtns = document.querySelectorAll('.cart-items-table .btn-remove-item');
    removeCartBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const key = btn.dataset.key;
            removeItemFromCart(key);
        });
    });

    // PDP Quantity Stepper (+ / -)
    const pdpQtyMinus = document.getElementById('pdpQtyMinus');
    const pdpQtyPlus = document.getElementById('pdpQtyPlus');
    const pdpQtyInput = document.getElementById('pdpQtyInput');

    if (pdpQtyMinus && pdpQtyPlus && pdpQtyInput) {
        pdpQtyMinus.addEventListener('click', () => {
            let val = parseInt(pdpQtyInput.value, 10) || 1;
            if (val > 1) pdpQtyInput.value = val - 1;
        });
        pdpQtyPlus.addEventListener('click', () => {
            let val = parseInt(pdpQtyInput.value, 10) || 1;
            const max = parseInt(pdpQtyInput.max, 10) || 10;
            if (val < max) pdpQtyInput.value = val + 1;
        });
    }

})();
