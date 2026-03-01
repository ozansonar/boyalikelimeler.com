/* ============================================================
   BOYALI KELİMELER — Custom Theme JavaScript
   Stack: Vanilla JS (ES6+) — jQuery YASAK
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* -- Navbar Scroll Effect -------------------------------- */
    const navbar = document.querySelector('.navbar-bk');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-bk--scrolled');
            } else {
                navbar.classList.remove('navbar-bk--scrolled');
            }
        }, { passive: true });
    }

    /* -- Mega Menu (Mobile Toggle + Desktop Close) ----------- */
    var megaToggles = document.querySelectorAll('.navbar-bk__link--dropdown');
    megaToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            var parentItem = this.closest('.navbar-bk__mega-item');
            if (!parentItem) return;

            // Mobile: toggle open class
            var isOpen = parentItem.classList.contains('navbar-bk__mega-item--open');

            // Close all other open menus
            document.querySelectorAll('.navbar-bk__mega-item--open').forEach(function (item) {
                item.classList.remove('navbar-bk__mega-item--open');
            });

            if (!isOpen) {
                parentItem.classList.add('navbar-bk__mega-item--open');
            }
        });
    });

    // Close mega menus on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.navbar-bk__mega-item')) {
            document.querySelectorAll('.navbar-bk__mega-item--open').forEach(function (item) {
                item.classList.remove('navbar-bk__mega-item--open');
            });
        }
    });

    // Close mega menus on ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.navbar-bk__mega-item--open').forEach(function (item) {
                item.classList.remove('navbar-bk__mega-item--open');
            });
        }
    });

    /* -- Tab System ------------------------------------------ */
    const tabButtons = document.querySelectorAll('[data-tab-target]');
    tabButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-tab-target');
            const targetPanel = document.getElementById(targetId);
            if (!targetPanel) return;

            // Deactivate all tabs
            tabButtons.forEach(function (b) {
                b.classList.remove('tabs-bk__item--active');
                b.setAttribute('aria-selected', 'false');
            });

            // Hide all panels
            document.querySelectorAll('.tabs-bk__panel').forEach(function (panel) {
                panel.classList.remove('tabs-bk__panel--active');
            });

            // Activate clicked tab and panel
            this.classList.add('tabs-bk__item--active');
            this.setAttribute('aria-selected', 'true');
            targetPanel.classList.add('tabs-bk__panel--active');
        });
    });

    /* -- Hero Slider (Premium Full-width) ------------------- */
    const heroSlides = document.querySelectorAll('.hero-slider__slide');
    const heroDots = document.querySelectorAll('.hero-slider__dot');
    const progressBar = document.querySelector('.hero-slider__progress-bar');
    const prevBtn = document.querySelector('[data-hero-prev]');
    const nextBtn = document.querySelector('[data-hero-next]');
    let currentHeroSlide = 0;
    let heroSliderInterval = null;
    const SLIDER_DURATION = 6000;

    function resetProgress() {
        if (!progressBar) return;
        progressBar.style.animation = 'none';
        progressBar.offsetHeight;
        progressBar.style.animation = 'sliderProgress ' + (SLIDER_DURATION / 1000) + 's linear infinite';
    }

    function showHeroSlide(index) {
        heroSlides.forEach(function (slide) {
            slide.classList.remove('hero-slider__slide--active');
        });
        heroDots.forEach(function (dot) {
            dot.classList.remove('hero-slider__dot--active');
        });

        if (heroSlides[index]) {
            heroSlides[index].classList.add('hero-slider__slide--active');
        }
        if (heroDots[index]) {
            heroDots[index].classList.add('hero-slider__dot--active');
        }
        currentHeroSlide = index;
        resetProgress();
    }

    function nextHeroSlide() {
        var next = (currentHeroSlide + 1) % heroSlides.length;
        showHeroSlide(next);
    }

    function prevHeroSlide() {
        var prev = (currentHeroSlide - 1 + heroSlides.length) % heroSlides.length;
        showHeroSlide(prev);
    }

    function restartAutoplay() {
        clearInterval(heroSliderInterval);
        heroSliderInterval = setInterval(nextHeroSlide, SLIDER_DURATION);
    }

    if (heroSlides.length > 1) {
        heroDots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                var idx = parseInt(this.getAttribute('data-hero-dot'), 10);
                showHeroSlide(idx);
                restartAutoplay();
            });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                prevHeroSlide();
                restartAutoplay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                nextHeroSlide();
                restartAutoplay();
            });
        }

        resetProgress();
        heroSliderInterval = setInterval(nextHeroSlide, SLIDER_DURATION);
    }

    /* -- Poll Selection -------------------------------------- */
    const pollOptions = document.querySelectorAll('.poll__option');
    pollOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            pollOptions.forEach(function (o) {
                o.classList.remove('poll__option--selected');
            });
            this.classList.add('poll__option--selected');
        });
    });

    /* -- Scroll Animations ----------------------------------- */
    /* AOS.js handles scroll animations (see AOS.init above) */

    /* -- Hero Section Background Gradient Animation ---------- */
    var heroSliderBgs = document.querySelectorAll('.hero-slider__bg');
    var gradients = [
        'linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%)',
        'linear-gradient(135deg, #2d1b2e 0%, #1a1a2e 50%, #0d1b2a 100%)',
        'linear-gradient(135deg, #1a1a1e 0%, #2a1a0e 50%, #1a1a2e 100%)'
    ];

    heroSliderBgs.forEach(function (bg, i) {
        bg.style.cssText = 'background:' + (gradients[i] || gradients[0]) + ';filter:brightness(0.4)';
    });

    /* -- Smooth Scroll for anchor links ---------------------- */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;

            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* -- AOS.js Init ----------------------------------------- */
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 700,
            easing: 'ease-out-cubic',
            once: true,
            offset: 20
        });
    }

    /* -- Video Gallery Swiper Slider ------------------------- */
    var videoSwiperEl = document.querySelector('.video-gallery__swiper');
    if (videoSwiperEl && typeof Swiper !== 'undefined') {
        new Swiper('.video-gallery__swiper', {
            slidesPerView: 3,
            spaceBetween: 12,
            grabCursor: true,
            navigation: {
                prevEl: '.video-gallery__nav-btn--prev',
                nextEl: '.video-gallery__nav-btn--next'
            },
            breakpoints: {
                0: {
                    slidesPerView: 1.5,
                    spaceBetween: 8
                },
                480: {
                    slidesPerView: 2,
                    spaceBetween: 10
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 12
                }
            }
        });
    }

    /* -- Video Modal (YouTube Embed) ------------------------- */
    var videoModal = document.getElementById('videoModal');
    var videoIframe = document.getElementById('videoIframe');

    if (videoModal && videoIframe) {
        var videoItems = document.querySelectorAll('.video-gallery__item[data-video-id]');

        function openVideoModal(videoId) {
            videoIframe.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0';
            videoModal.classList.add('video-modal--active');
            document.body.style.overflow = 'hidden';
        }

        function closeVideoModal() {
            videoIframe.src = '';
            videoModal.classList.remove('video-modal--active');
            document.body.style.overflow = '';
        }

        videoItems.forEach(function (item) {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                var videoId = this.getAttribute('data-video-id');
                if (videoId) {
                    openVideoModal(videoId);
                }
            });
        });

        videoModal.querySelector('.video-modal__overlay').addEventListener('click', closeVideoModal);
        videoModal.querySelector('.video-modal__close').addEventListener('click', closeVideoModal);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && videoModal.classList.contains('video-modal--active')) {
                closeVideoModal();
            }
        });
    }

    /* -- Member Grid: Search, Sort & Pagination --------------- */
    var memberGrid = document.getElementById('memberGrid');
    var memberSearch = document.getElementById('memberSearch');
    var memberCountText = document.getElementById('memberCountText');
    var memberNoResult = document.getElementById('memberNoResult');
    var paginationContainer = document.getElementById('memberPagination');
    var paginationPages = document.getElementById('paginationPages');
    var paginationPrev = document.getElementById('paginationPrev');
    var paginationNext = document.getElementById('paginationNext');

    if (memberGrid && memberSearch) {
        var ITEMS_PER_PAGE = 8;
        var currentPage = 1;
        var allMembers = Array.from(memberGrid.querySelectorAll('.col-lg-3'));
        var originalOrder = allMembers.slice();
        var filteredMembers = allMembers.slice();
        var currentSort = 'default';

        function getMemberName(el) {
            var nameEl = el.querySelector('.member-card__name');
            return nameEl ? nameEl.textContent.trim().toLowerCase() : '';
        }

        function turkishSort(a, b) {
            return a.localeCompare(b, 'tr');
        }

        function sortMembers(type) {
            currentSort = type;

            if (type === 'default') {
                filteredMembers.sort(function (a, b) {
                    return originalOrder.indexOf(a) - originalOrder.indexOf(b);
                });
            } else if (type === 'az') {
                filteredMembers.sort(function (a, b) {
                    return turkishSort(getMemberName(a), getMemberName(b));
                });
            } else if (type === 'za') {
                filteredMembers.sort(function (a, b) {
                    return turkishSort(getMemberName(b), getMemberName(a));
                });
            } else if (type === 'random') {
                for (var i = filteredMembers.length - 1; i > 0; i--) {
                    var j = Math.floor(Math.random() * (i + 1));
                    var temp = filteredMembers[i];
                    filteredMembers[i] = filteredMembers[j];
                    filteredMembers[j] = temp;
                }
            }
        }

        function filterMembers(query) {
            var q = query.toLowerCase().trim();

            if (q === '') {
                filteredMembers = allMembers.slice();
            } else {
                filteredMembers = allMembers.filter(function (el) {
                    var name = getMemberName(el);
                    var roleEl = el.querySelector('.member-card__role');
                    var role = roleEl ? roleEl.textContent.trim().toLowerCase() : '';
                    return name.indexOf(q) !== -1 || role.indexOf(q) !== -1;
                });
            }

            if (currentSort !== 'default') {
                sortMembers(currentSort);
            }
        }

        function getTotalPages() {
            return Math.max(1, Math.ceil(filteredMembers.length / ITEMS_PER_PAGE));
        }

        function renderPage() {
            var totalPages = getTotalPages();
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            var start = (currentPage - 1) * ITEMS_PER_PAGE;
            var end = start + ITEMS_PER_PAGE;

            allMembers.forEach(function (el) {
                el.style.display = 'none';
            });

            filteredMembers.forEach(function (el, idx) {
                if (idx >= start && idx < end) {
                    el.style.display = '';
                    memberGrid.appendChild(el);
                }
            });

            if (filteredMembers.length === 0) {
                memberNoResult.classList.add('member-no-result--visible');
                paginationContainer.style.display = 'none';
            } else {
                memberNoResult.classList.remove('member-no-result--visible');
                paginationContainer.style.display = totalPages > 1 ? 'flex' : 'none';
            }

            memberCountText.textContent = filteredMembers.length + ' yoldaş gösteriliyor';
            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            paginationPages.innerHTML = '';
            paginationPrev.disabled = currentPage <= 1;
            paginationNext.disabled = currentPage >= totalPages;

            for (var i = 1; i <= totalPages; i++) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'member-pagination__page' + (i === currentPage ? ' member-pagination__page--active' : '');
                btn.textContent = i;
                btn.setAttribute('data-page', i);
                paginationPages.appendChild(btn);
            }
        }

        paginationPages.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-page]');
            if (btn) {
                currentPage = parseInt(btn.getAttribute('data-page'), 10);
                renderPage();
                memberGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        paginationPrev.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                renderPage();
                memberGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        paginationNext.addEventListener('click', function () {
            if (currentPage < getTotalPages()) {
                currentPage++;
                renderPage();
                memberGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        var sortButtons = document.querySelectorAll('.member-toolbar__sort-btn');
        sortButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                sortButtons.forEach(function (b) {
                    b.classList.remove('member-toolbar__sort-btn--active');
                });
                this.classList.add('member-toolbar__sort-btn--active');

                var sortType = this.getAttribute('data-sort');
                sortMembers(sortType);
                currentPage = 1;
                renderPage();
            });
        });

        var searchTimeout = null;
        memberSearch.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            var val = this.value;
            searchTimeout = setTimeout(function () {
                filterMembers(val);
                currentPage = 1;
                renderPage();
            }, 200);
        });

        renderPage();
    }
});
