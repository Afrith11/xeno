/**
 * MAISON ÉLAN - Main Client-Side JavaScript
 * Pure Vanilla JavaScript (No jQuery / No Node / No Build Required)
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ==========================================================
       1. Sticky Header Compact on Scroll
       ========================================================== */
    const siteHeader = document.getElementById('site-header');
    if (siteHeader) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                siteHeader.classList.add('scrolled');
            } else {
                siteHeader.classList.remove('scrolled');
            }
        }, { passive: true });
    }

    /* ==========================================================
       2. Mobile Navigation Drawer
       ========================================================== */
    const mobileMenuOpen = document.getElementById('mobileMenuOpen');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const mobileDrawerOverlay = document.getElementById('mobileDrawerOverlay');

    function openMobileMenu() {
        if (mobileDrawer && mobileDrawerOverlay) {
            mobileDrawer.classList.add('active');
            mobileDrawerOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileMenu() {
        if (mobileDrawer && mobileDrawerOverlay) {
            mobileDrawer.classList.remove('active');
            mobileDrawerOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (mobileMenuOpen) mobileMenuOpen.addEventListener('click', openMobileMenu);
    if (mobileMenuClose) mobileMenuClose.addEventListener('click', closeMobileMenu);
    if (mobileDrawerOverlay) mobileDrawerOverlay.addEventListener('click', closeMobileMenu);

    /* ==========================================================
       3. Search Modal Overlay & Live Search
       ========================================================== */
    const searchModalTrigger = document.getElementById('searchModalTrigger');
    const searchModalClose = document.getElementById('searchModalClose');
    const searchOverlay = document.getElementById('searchOverlay');
    const liveSearchInput = document.getElementById('liveSearchInput');
    const liveSearchResults = document.getElementById('liveSearchResults');

    function openSearchModal() {
        if (searchOverlay) {
            searchOverlay.classList.add('active');
            if (liveSearchInput) {
                setTimeout(() => liveSearchInput.focus(), 100);
            }
        }
    }

    function closeSearchModal() {
        if (searchOverlay) {
            searchOverlay.classList.remove('active');
            if (liveSearchResults) {
                liveSearchResults.style.display = 'none';
                liveSearchResults.innerHTML = '';
            }
        }
    }

    if (searchModalTrigger) searchModalTrigger.addEventListener('click', openSearchModal);
    if (searchModalClose) searchModalClose.addEventListener('click', closeSearchModal);

    // Escape key closes search & modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeSearchModal();
            closeMobileMenu();
            closeQuickView();
            closeLightbox();
            closeSizeGuide();
        }
    });

    // Debounced live search
    let searchDebounceTimer = null;
    if (liveSearchInput && liveSearchResults) {
        liveSearchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            clearTimeout(searchDebounceTimer);

            if (query.length < 2) {
                liveSearchResults.style.display = 'none';
                liveSearchResults.innerHTML = '';
                return;
            }

            searchDebounceTimer = setTimeout(() => {
                fetch(`${window.MAISON.baseUrl}/api/search.php?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.results && data.results.length > 0) {
                            let html = '<div class="live-search-list">';
                            data.results.forEach(item => {
                                html += `
                                    <a href="${item.url}" class="live-search-item">
                                        <img src="${item.image}" alt="${item.name}" class="live-search-thumb">
                                        <div class="live-search-info">
                                            <div class="live-search-title">${item.name}</div>
                                            <span class="live-search-category">${item.category}</span>
                                            <div class="live-search-price">${item.price}</div>
                                        </div>
                                    </a>
                                `;
                            });
                            html += `
                                <div class="live-search-footer" style="padding-top:10px; text-align:center;">
                                    <a href="${data.view_all_url}" class="btn-text">View All Results (${data.count}+) &rarr;</a>
                                </div>
                            </div>`;
                            liveSearchResults.innerHTML = html;
                            liveSearchResults.style.display = 'block';
                        } else {
                            liveSearchResults.innerHTML = '<p style="padding:14px; text-align:center; color:var(--charcoal-muted); font-size:0.88rem;">No silhouettes found for “' + query + '”.</p>';
                            liveSearchResults.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                    });
            }, 250);
        });
    }

    /* ==========================================================
       4. Quick View Modal
       ========================================================== */
    const quickViewBackdrop = document.getElementById('quickViewBackdrop');
    const quickViewBody = document.getElementById('quickViewBody');
    const quickViewClose = document.getElementById('quickViewClose');

    function openQuickView(productId, slug) {
        if (!quickViewBackdrop || !quickViewBody) return;
        quickViewBackdrop.classList.add('active');
        document.body.style.overflow = 'hidden';

        quickViewBody.innerHTML = `
            <div class="quickview-loading" style="text-align:center; padding:60px 0;">
                <div class="luxury-spinner"></div>
                <p style="margin-top:16px; font-size:0.88rem; color:var(--charcoal-muted);">Loading Maison Élan piece...</p>
            </div>
        `;

        const url = `${window.MAISON.baseUrl}/api/quickview.php?id=${encodeURIComponent(productId)}&slug=${encodeURIComponent(slug || '')}`;
        fetch(url)
            .then(res => res.text())
            .then(html => {
                quickViewBody.innerHTML = html;
                bindQuickViewForm();
            })
            .catch(() => {
                quickViewBody.innerHTML = '<p class="text-danger" style="text-align:center; padding:40px;">Failed to load silhouette. Please try again.</p>';
            });
    }

    function closeQuickView() {
        if (quickViewBackdrop) {
            quickViewBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (quickViewClose) quickViewClose.addEventListener('click', closeQuickView);
    if (quickViewBackdrop) {
        quickViewBackdrop.addEventListener('click', (e) => {
            if (e.target === quickViewBackdrop) closeQuickView();
        });
    }

    // Delegate quickview click from cards
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-quickview');
        if (btn) {
            e.preventDefault();
            const pid = btn.dataset.productId;
            const slug = btn.dataset.productSlug;
            openQuickView(pid, slug);
        }
    });

    function bindQuickViewForm() {
        const form = document.getElementById('quickViewForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                formData.append('action', 'add');

                fetch(`${window.MAISON.baseUrl}/api/cart.php`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        closeQuickView();
                        window.showToast('Piece added to your shopping bag.', 'success');
                        if (window.updateCartCounters) window.updateCartCounters(data.cart_count);
                        if (window.openCartDrawer) window.openCartDrawer();
                    } else {
                        window.showToast(data.message || 'Could not add piece.', 'error');
                    }
                })
                .catch(() => {
                    window.showToast('Server error while updating bag.', 'error');
                });
            });
        }
    }

    /* ==========================================================
       5. Lookbook Masonry Lightbox
       ========================================================== */
    const lookbookItems = document.querySelectorAll('.lookbook-item');
    const lookbookLightbox = document.getElementById('lookbookLightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const lightboxCaption = document.getElementById('lightboxCaption');
    const lightboxClose = document.getElementById('lightboxClose');

    lookbookItems.forEach(item => {
        item.addEventListener('click', () => {
            const src = item.dataset.imgSrc || item.querySelector('img').src;
            const title = item.dataset.lookTitle || '';
            if (lookbookLightbox && lightboxImg) {
                lightboxImg.src = src;
                if (lightboxCaption) lightboxCaption.innerText = title;
                lookbookLightbox.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    function closeLightbox() {
        if (lookbookLightbox) {
            lookbookLightbox.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    if (lookbookLightbox) {
        lookbookLightbox.addEventListener('click', (e) => {
            if (e.target === lookbookLightbox) closeLightbox();
        });
    }

    /* ==========================================================
       6. Accordion Toggles (PDP)
       ========================================================== */
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    accordionHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const parentItem = header.closest('.accordion-item');
            if (parentItem) {
                parentItem.classList.toggle('active');
            }
        });
    });

    /* ==========================================================
       7. Size Guide Modal
       ========================================================== */
    const sizeGuideModal = document.getElementById('sizeGuideModal');
    const openSizeGuideBtn = document.getElementById('openSizeGuideModalBtn');
    const openSizeGuideLink = document.getElementById('openSizeGuideLink');
    const sizeGuideClose = document.getElementById('sizeGuideClose');

    function openSizeGuide() {
        if (sizeGuideModal) {
            sizeGuideModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSizeGuide() {
        if (sizeGuideModal) {
            sizeGuideModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (openSizeGuideBtn) openSizeGuideBtn.addEventListener('click', openSizeGuide);
    if (openSizeGuideLink) openSizeGuideLink.addEventListener('click', openSizeGuide);
    if (sizeGuideClose) sizeGuideClose.addEventListener('click', closeSizeGuide);
    if (sizeGuideModal) {
        sizeGuideModal.addEventListener('click', (e) => {
            if (e.target === sizeGuideModal) closeSizeGuide();
        });
    }

    /* ==========================================================
       8. Horizontal Bestsellers Carousel Controls
       ========================================================== */
    const bestsellersCarousel = document.getElementById('bestsellersCarousel');
    const bestsellerPrev = document.getElementById('bestsellerPrev');
    const bestsellerNext = document.getElementById('bestsellerNext');

    if (bestsellersCarousel) {
        if (bestsellerPrev) {
            bestsellerPrev.addEventListener('click', () => {
                bestsellersCarousel.scrollBy({ left: -320, behavior: 'smooth' });
            });
        }
        if (bestsellerNext) {
            bestsellerNext.addEventListener('click', () => {
                bestsellersCarousel.scrollBy({ left: 320, behavior: 'smooth' });
            });
        }
    }

    /* ==========================================================
       9. Mobile Filters Drawer (Shop Catalog)
       ========================================================== */
    const openMobileFilterBtn = document.getElementById('openMobileFilterBtn');
    const closeMobileFilterBtn = document.getElementById('closeMobileFilterBtn');
    const shopSidebarFilters = document.getElementById('shopSidebarFilters');
    const mobileFilterBackdrop = document.getElementById('mobileFilterBackdrop');

    function openMobileFilter() {
        if (shopSidebarFilters && mobileFilterBackdrop) {
            shopSidebarFilters.classList.add('active');
            mobileFilterBackdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileFilter() {
        if (shopSidebarFilters && mobileFilterBackdrop) {
            shopSidebarFilters.classList.remove('active');
            mobileFilterBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (openMobileFilterBtn) openMobileFilterBtn.addEventListener('click', openMobileFilter);
    if (closeMobileFilterBtn) closeMobileFilterBtn.addEventListener('click', closeMobileFilter);
    if (mobileFilterBackdrop) mobileFilterBackdrop.addEventListener('click', closeMobileFilter);

    /* ==========================================================
       10. PDP Gallery Thumbnail Switcher
       ========================================================== */
    const galleryThumbs = document.querySelectorAll('.gallery-thumb-btn');
    const galleryMainImg = document.getElementById('galleryMainImg');

    galleryThumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            galleryThumbs.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
            const newSrc = thumb.dataset.imgSrc;
            if (galleryMainImg && newSrc) {
                galleryMainImg.src = newSrc;
            }
        });
    });

    /* ==========================================================
       11. Newsletter AJAX Submissions
       ========================================================== */
    function setupNewsletter(formId, responseId) {
        const form = document.getElementById(formId);
        const resp = document.getElementById(responseId);
        if (form && resp) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(form);

                fetch(`${window.MAISON.baseUrl}/api/newsletter.php`, {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        resp.innerHTML = `<span style="color:#2D6A4F; font-size:0.85rem; font-weight:500;">${data.message}</span>`;
                        form.reset();
                    } else {
                        resp.innerHTML = `<span style="color:#8C2222; font-size:0.85rem;">${data.message}</span>`;
                    }
                })
                .catch(() => {
                    resp.innerHTML = `<span style="color:#8C2222; font-size:0.85rem;">Could not process subscription.</span>`;
                });
            });
        }
    }

    setupNewsletter('homeNewsletterForm', 'homeNewsletterResponse');
    setupNewsletter('footerNewsletterForm', 'footerNewsletterStatus');

});
