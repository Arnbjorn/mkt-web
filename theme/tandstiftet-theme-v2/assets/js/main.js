/**
 * Tändstiftet MC — Main JS
 */
(function () {
  'use strict';

  // ── Mobile nav toggle (dropdown panel under the header) ──────
  const toggle = document.getElementById('nav-toggle');
  const nav    = document.getElementById('primary-nav');

  function closeNav() {
    if (!nav || !toggle) return;
    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
  }

  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open);
    });

    // Esc closes it, and so does tapping anywhere outside the panel
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) closeNav();
    });
    document.addEventListener('click', e => {
      if (nav.classList.contains('is-open') && !nav.contains(e.target) && !toggle.contains(e.target)) closeNav();
    });
  }

  // ── Header fit ───────────────────────────────────────────────
  // The header is one row: logo · menu · button. If the menu is long enough that it
  // would wrap onto a second row, switch to a compact header (hides the tagline and
  // tightens link spacing). Leaving the phone/tablet range also closes any open menu.
  const siteHeader = document.querySelector('.site-header');
  const siteBrand  = siteHeader ? siteHeader.querySelector('.site-branding') : null;
  const siteCta    = siteHeader ? siteHeader.querySelector('.site-header__cta') : null;

  function fitHeader() {
    if (!siteHeader || !siteBrand || !nav) return;
    siteHeader.classList.remove('is-compact');
    if (window.innerWidth <= 1099) return;            // hamburger layout takes over
    closeNav();
    const brandBottom = siteBrand.offsetTop + siteBrand.offsetHeight - 1;
    const wrapped = nav.offsetTop >= brandBottom || (siteCta && siteCta.offsetTop >= brandBottom);
    if (wrapped) siteHeader.classList.add('is-compact');
  }

  let fitTimer;
  window.addEventListener('resize', () => { clearTimeout(fitTimer); fitTimer = setTimeout(fitHeader, 120); });
  window.addEventListener('load', fitHeader);
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(fitHeader);
  fitHeader();

  // ── Riders filter (anchor smooth scroll + active state) ─────
  const filterBtns = document.querySelectorAll('.riders-filter__btn[href^="#"]');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
    });
  });

  // ── Simple gallery lightbox ──────────────────────────────────
  const galleryItems = document.querySelectorAll('.gallery-item');
  if (galleryItems.length) {
    const overlay = document.createElement('div');
    overlay.id = 'tmc-lightbox';
    overlay.style.cssText = [
      'position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,0.92)',
      'display:none;align-items:center;justify-content:center;cursor:zoom-out'
    ].join(';');

    const img = document.createElement('img');
    img.style.cssText = 'max-width:90vw;max-height:90vh;object-fit:contain;box-shadow:0 0 60px rgba(0,0,0,0.8)';

    const close = document.createElement('button');
    close.textContent = '✕';
    close.style.cssText = [
      'position:absolute;top:1.5rem;right:2rem;background:none;border:none',
      'color:#fff;font-size:2rem;cursor:pointer;opacity:0.7;line-height:1'
    ].join(';');
    close.setAttribute('aria-label', 'Stäng');

    overlay.appendChild(img);
    overlay.appendChild(close);
    document.body.appendChild(overlay);

    const openLightbox = src => {
      img.src = src;
      overlay.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    };
    const closeLightbox = () => {
      overlay.style.display = 'none';
      document.body.style.overflow = '';
    };

    galleryItems.forEach(item => {
      item.style.cursor = 'zoom-in';
      item.addEventListener('click', () => {
        const imgEl = item.querySelector('img');
        if (imgEl) openLightbox(imgEl.src.replace(/-\d+x\d+(\.\w+)$/, '$1'));
      });
    });

    close.addEventListener('click', closeLightbox);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeLightbox(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
  }

  // ── Sticky header shadow on scroll ──────────────────────────
  const header = document.querySelector('.site-header');
  if (header) {
    const onScroll = () => {
      header.style.boxShadow = window.scrollY > 10
        ? '0 4px 24px rgba(0,0,0,0.5)'
        : 'none';
    };
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // ── Animate cards on scroll ──────────────────────────────────
  if ('IntersectionObserver' in window) {
    const animItems = document.querySelectorAll('.rider-card, .news-card, .event-card');
    const observer  = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.animation = 'fadeUp 0.45s ease both';
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    animItems.forEach((el, i) => {
      el.style.opacity = '0';
      el.style.animationDelay = (i % 4) * 60 + 'ms';
      observer.observe(el);
    });

    const style = document.createElement('style');
    style.textContent = `
      @keyframes fadeUp {
        from { opacity:0; transform:translateY(18px); }
        to   { opacity:1; transform:translateY(0); }
      }
    `;
    document.head.appendChild(style);
  }

  // ── Longpage sticky sidenav — highlight active section ──────
  const sidenav = document.getElementById('longpage-sidenav');
  if (sidenav) {
    const navLinks = sidenav.querySelectorAll('.longpage-sidenav__link');
    const sections = Array.from(navLinks)
      .map(l => document.getElementById(l.dataset.target))
      .filter(Boolean);

    const setActive = id => {
      navLinks.forEach(l => l.classList.toggle('is-active', l.dataset.target === id));
      const active = sidenav.querySelector('.is-active');
      if (active) active.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    };

    if (sections.length) {
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) setActive(entry.target.id);
        });
      }, { rootMargin: '-20% 0px -70% 0px' });

      sections.forEach(s => observer.observe(s));

      navLinks.forEach(link => {
        link.addEventListener('click', e => {
          e.preventDefault();
          const target = document.getElementById(link.dataset.target);
          if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
      });
    }
  }

  // ── Hero slideshow ───────────────────────────────────────────
  const slides = document.querySelectorAll('.hero__slide');
  const dots   = document.querySelectorAll('.hero__dot');

  if (slides.length > 1) {
    let current  = 0;
    let interval = null;

    const goTo = idx => {
      slides[current].classList.remove('is-active');
      dots[current] && dots[current].classList.remove('is-active');
      current = (idx + slides.length) % slides.length;
      slides[current].classList.add('is-active');
      dots[current] && dots[current].classList.add('is-active');
    };

    const start = () => { interval = setInterval(() => goTo(current + 1), 5000); };
    const reset = () => { clearInterval(interval); start(); };

    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => { goTo(i); reset(); });
    });

    const hero = document.querySelector('.hero--slideshow');
    if (hero) {
      hero.addEventListener('mouseenter', () => clearInterval(interval));
      hero.addEventListener('mouseleave', start);
    }

    start();
  }

})();
