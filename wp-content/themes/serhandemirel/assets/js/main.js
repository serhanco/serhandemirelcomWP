function initPortfolio() {
    gsap.registerPlugin(ScrollTrigger);

    // --- 0. SMOOTH SCROLL (Lenis) ---
    const lenis = new Lenis();
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => {
      lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);

    // Anchor links intercept for Lenis
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            lenis.scrollTo(this.getAttribute('href'));
        });
    });

    // --- 1. PARTICLE SYSTEM (Canvas tabanlı performanslı rastgele efektler) ---
    const canvas = document.getElementById('particle-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let width, height, particles;
        // Rastgele efekt seçimi: 0 (Constellation), 1 (Starfield), 2 (Ambient Orbs)
        const effectType = Math.floor(Math.random() * 3); 

        function initParticles() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            particles = [];

            if (effectType === 0) {
                // 0: Constellation
                const particleCount = window.innerWidth < 768 ? 70 : 180;
                for (let i = 0; i < particleCount; i++) {
                    particles.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        vx: (Math.random() - 0.5) * 0.5,
                        vy: (Math.random() - 0.5) * 0.5,
                        radius: Math.random() * 1.5 + 0.5
                    });
                }
            } else if (effectType === 1) {
                // 1: Starfield
                const particleCount = window.innerWidth < 768 ? 100 : 300;
                for (let i = 0; i < particleCount; i++) {
                    particles.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        vx: (Math.random() - 0.5) * 0.2, // Hafif sağa sola
                        vy: Math.random() * 0.8 + 0.2,   // Yavaşça yukarıdan aşağı
                        radius: Math.random() * 1.2 + 0.2,
                        opacity: Math.random() * 0.8 + 0.2
                    });
                }
            } else {
                // 2: Ambient Orbs
                const particleCount = window.innerWidth < 768 ? 15 : 25;
                for (let i = 0; i < particleCount; i++) {
                    particles.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        vx: (Math.random() - 0.5) * 0.2,
                        vy: (Math.random() - 0.5) * 0.2 - 0.1, // Çok hafif yukarı meyil
                        radius: Math.random() * 60 + 20,       // Daha büyük orblar
                        hue: Math.random() > 0.5 ? 280 : 320,  // Mor veya Fuşya
                        opacity: Math.random() * 0.3 + 0.1     // Rastgele belirginlik
                    });
                }
            }
        }

        let mouse = { x: null, y: null };
        window.addEventListener('mousemove', e => {
            mouse.x = e.x;
            mouse.y = e.y;
        });

        function animateParticles() {
            requestAnimationFrame(animateParticles);
            ctx.clearRect(0, 0, width, height);

            particles.forEach(p => {
                if (effectType === 0) {
                    // Constellation Animation
                    p.x += p.vx;
                    p.y += p.vy;
                    if (p.x < 0 || p.x > width) p.vx *= -1;
                    if (p.y < 0 || p.y > height) p.vy *= -1;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(255,255,255,0.4)';
                    ctx.fill();

                    particles.forEach(p2 => {
                        const dx = p.x - p2.x;
                        const dy = p.y - p2.y;
                        const dist = Math.sqrt(dx*dx + dy*dy);
                        if (dist < 120) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.strokeStyle = `rgba(255,255,255,${0.1 - dist/1200})`;
                            ctx.stroke();
                        }
                    });

                    if (mouse.x) {
                        const dx = p.x - mouse.x;
                        const dy = p.y - mouse.y;
                        const dist = Math.sqrt(dx*dx + dy*dy);
                        if (dist < 150) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(mouse.x, mouse.y);
                            ctx.strokeStyle = `rgba(217, 70, 239, ${0.25 - dist/600})`; 
                            ctx.stroke();
                        }
                    }
                } else if (effectType === 1) {
                    // Starfield Animation
                    p.x += p.vx;
                    p.y += p.vy;

                    // Ekran dışına çıkarsa yukarıdan tekrar başlat
                    if (p.y > height) p.y = 0;
                    if (p.x < 0) p.x = width;
                    if (p.x > width) p.x = 0;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(255,255,255,${p.opacity})`;
                    ctx.fill();
                } else {
                    // Ambient Orbs Animation
                    p.x += p.vx;
                    p.y += p.vy;

                    if (p.x < -100) p.x = width + 100;
                    if (p.x > width + 100) p.x = -100;
                    if (p.y < -100) p.y = height + 100;
                    if (p.y > height + 100) p.y = -100;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);

                    // Radial gradient for a better orb look
                    let gradient = ctx.createRadialGradient(p.x, p.y, p.radius * 0.1, p.x, p.y, p.radius);
                    gradient.addColorStop(0, `hsla(${p.hue}, 80%, 70%, ${p.opacity})`);
                    gradient.addColorStop(1, `hsla(${p.hue}, 80%, 50%, 0)`);

                    ctx.fillStyle = gradient;
                    ctx.fill();
                }
            });
        }

        initParticles();
        animateParticles();

        let lastWidth = window.innerWidth;
        window.addEventListener('resize', () => {
            if (window.innerWidth !== lastWidth) {
                lastWidth = window.innerWidth;
                initParticles();
            }
        });
    }

    // --- 2. DYNAMIC WORDS (We can... Slot efekti) ---
    const words = (window.sdMain && sdMain.words.length) ? sdMain.words : [
        "design", "prototype", "solve", "build", "develop", 
        "debug", "learn", "optimize", "ship", "prompt", 
        "collaborate", "create", "transform", "automate", 
        "innovate", "scale", "integrate", "deploy", "succeed"
    ];

    const sliderInner = document.getElementById('word-slider');
    if (sliderInner) {
        // Kelimeleri HTML'e enjekte et
        words.forEach(word => {
            const div = document.createElement('div');
            div.className = 'word-item';
            div.innerText = word + ".";
            sliderInner.appendChild(div);
        });

        const totalWords = words.length;
        // Sona kadar kaydırma miktarı
        const translationAmount = -100 * ((totalWords - 1) / totalWords);

        gsap.to(sliderInner, {
            yPercent: translationAmount,
            ease: "none",
            scrollTrigger: {
                trigger: "#dynamic-words-section",
                start: "top top", 
                end: "+=2500", // Scroll uzunluğu
                pin: "#dynamic-words-content", 
                scrub: 1, // Yumuşatma oranı
                snap: {
                    snapTo: 1 / (totalWords - 1),
                    duration: 0.2,
                    ease: "power1.inOut"
                }
            }
        });
    }

    // --- 3. EXPERTISE DRAG & BUTTON SCROLL ---
    const expertiseSlider = document.getElementById('expertise-slider');

    if (expertiseSlider) {
        let isDown = false, startX, scrollLeft;

        expertiseSlider.addEventListener('mousedown', (e) => {
            isDown = true;
            expertiseSlider.style.cursor = 'grabbing';
            startX = e.pageX - expertiseSlider.getBoundingClientRect().left;
            scrollLeft = expertiseSlider.scrollLeft;
            e.preventDefault();
        });
        document.addEventListener('mouseup', () => {
            isDown = false;
            expertiseSlider.style.cursor = 'grab';
        });
        document.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            const x = e.pageX - expertiseSlider.getBoundingClientRect().left;
            expertiseSlider.scrollLeft = scrollLeft - (x - startX);
        });

        // Ok butonları
        const prevBtn = document.getElementById('scroll-prev');
        const nextBtn = document.getElementById('scroll-next');
        if (prevBtn && nextBtn) {
            const getAmount = () => window.innerWidth < 768 ? window.innerWidth * 0.82 : 424;
            prevBtn.addEventListener('click', () => expertiseSlider.scrollBy({ left: -getAmount(), behavior: 'smooth' }));
            nextBtn.addEventListener('click', () => expertiseSlider.scrollBy({ left: getAmount(), behavior: 'smooth' }));
        }
    }

    // --- 4. LOGO MARQUEE (GSAP ile zıt yönlü sonsuz kayış) ---
    // Migrated to CSS Scroll-driven animations in the <style> tag.

    // --- 5. PORTFOLIO FILTERS ---
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');

    if(filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Aktif buton stilini değiştir
                filterBtns.forEach(b => {
                    b.classList.remove('bg-white', 'text-black');
                    b.classList.add('text-white');
                });
                btn.classList.add('bg-white', 'text-black');
                btn.classList.remove('text-white');

                const filter = btn.getAttribute('data-filter');

                // Portfolyo öğelerini filtrele
                portfolioItems.forEach(item => {
                    if (filter === 'all' || (item.getAttribute('data-category') || '').split(' ').includes(filter)) {
                        item.style.display = 'block';
                        gsap.fromTo(item, {opacity: 0, scale: 0.95}, {opacity: 1, scale: 1, duration: 0.4});
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    // --- 6. PREMIUM FULLSCREEN MOBILE MENU ---
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const iconMenu = document.getElementById('icon-menu');
    const iconClose = document.getElementById('icon-close');
    const fullscreenMenu = document.getElementById('mobile-fullscreen-menu');
    const menuContent = document.getElementById('mobile-menu-content');
    const mobileLinks = document.querySelectorAll('.mobile-link');
    let isMobileMenuOpen = false;

    function toggleMobileMenu() {
        isMobileMenuOpen = !isMobileMenuOpen;

        if (isMobileMenuOpen) {
            // Açılış (Overlay & İçerik)
            fullscreenMenu.classList.remove('opacity-0', 'pointer-events-none');
            fullscreenMenu.classList.add('opacity-100', 'pointer-events-auto');

            menuContent.classList.remove('translate-y-8', 'opacity-0', 'scale-90');
            menuContent.classList.add('translate-y-0', 'opacity-100', 'scale-100');

            // İkon Dönüşümü (Hamburger -> X)
            iconMenu.classList.add('opacity-0', 'rotate-90', 'scale-50');
            iconMenu.classList.remove('opacity-100', 'rotate-0');
            iconClose.classList.remove('opacity-0', '-rotate-90', 'scale-50');
            iconClose.classList.add('opacity-100', 'rotate-0', 'scale-100');

            document.body.style.overflow = 'hidden'; // Arkaplan scroll'u kilitle
        } else {
            // Kapanış
            fullscreenMenu.classList.add('opacity-0', 'pointer-events-none');
            fullscreenMenu.classList.remove('opacity-100', 'pointer-events-auto');

            menuContent.classList.add('translate-y-8', 'opacity-0', 'scale-90');
            menuContent.classList.remove('translate-y-0', 'opacity-100', 'scale-100');

            // İkon Dönüşümü (X -> Hamburger)
            iconClose.classList.remove('opacity-100', 'rotate-0', 'scale-100');
            iconClose.classList.add('opacity-0', '-rotate-90', 'scale-50');
            iconMenu.classList.remove('opacity-0', 'rotate-90', 'scale-50');
            iconMenu.classList.add('opacity-100', 'rotate-0');

            document.body.style.overflow = ''; // Arkaplan kilidini aç
        }
    }

    if (mobileBtn && fullscreenMenu) {
        mobileBtn.addEventListener('click', toggleMobileMenu);
        // Linke tıklanınca menüyü kapat
        mobileLinks.forEach(link => {
            link.addEventListener('click', toggleMobileMenu);
        });
    }

    // --- 7. FLOATING NAVBAR HIDE/SHOW ON SCROLL ---
    const nav = document.getElementById('main-nav');
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        // Sadece aşağı doğru ve belirli bir miktar kaydırıldığında gizle
        if (currentScroll > 150 && currentScroll > lastScroll) {
            if (nav && !isMobileMenuOpen) nav.style.transform = 'translate(-50%, -150%)';
        } else {
            if (nav) nav.style.transform = 'translate(-50%, 0)';
        }
        lastScroll = currentScroll;
    });

    // --- 8. FLOATING DUAL CONTACT BAR (Scroll tetikleyici) ---
    const stickyContact = document.getElementById('sticky-contact');
    if (stickyContact && document.getElementById('expertise')) {
        // Expertise bölümüne gelince göster
        ScrollTrigger.create({
            trigger: "#expertise",
            start: "top 80%",
            onEnter: () => stickyContact.classList.remove('translate-y-[150%]', 'opacity-0'),
            onLeaveBack: () => stickyContact.classList.add('translate-y-[150%]', 'opacity-0'),
        });
        // Contact bölümüne (Footer'a) gelince gizle (çakışmasın diye)
        ScrollTrigger.create({
            trigger: "#contact",
            start: "top 90%",
            onEnter: () => stickyContact.classList.add('translate-y-[150%]', 'opacity-0'),
            onLeaveBack: () => stickyContact.classList.remove('translate-y-[150%]', 'opacity-0'),
        });
    }

    // --- 9. UTILITIES (Saat, Yıl, Yukarı Çık) ---
    const yearEl = document.getElementById('current-year');
    if(yearEl) yearEl.textContent = new Date().getFullYear();

    const backToTop = document.getElementById('back-to-top');
    if(backToTop) {
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function updateTime() {
        const timeString = new Date().toLocaleTimeString('en-US', {
            timeZone: (window.sdMain && sdMain.timezone) || 'Europe/Istanbul', hour12: false,
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
        const timeElement = document.getElementById('footer-time');
        if(timeElement) timeElement.textContent = timeString + ' ' + ((window.sdMain && sdMain.timezoneLabel) || '');
    }
    setInterval(updateTime, 1000);
    updateTime();
}

// DOM tamamen hazır olduğunda çalıştır (Hataları önlemek için)
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(initPortfolio, 1);
} else {
    document.addEventListener("DOMContentLoaded", initPortfolio);
}
