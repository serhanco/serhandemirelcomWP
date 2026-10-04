// 1. TEMA AYARI
const LOGO_THEME = 'white'; 

// 2. SATIR SAYISI AYARI (Desktop vs Mobil)
const ROWS_DESKTOP = 5;
const ROWS_MOBILE = 5;

// 3. KAYDIRMA HIZI AYARI (Saniye cinsinden - Sayı küçüldükçe hızlanır, büyüdükçe yavaşlar)
const SPEED_DESKTOP = 50; 
const SPEED_MOBILE = 30; 

// 4. LOGO SIRALAMASI (Brands menüsünden, yoksa inc/brands.php'den gelir)
const BRAND_LOGOS = sdBrands.logos;

const escapeAttr = (value) => String(value || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');

document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById('marquee-container');
    if (!container) return;

    let currentRows = -1;

    const renderLogos = () => {
        // Determine if mobile (width < 768px matches standard Tailwind md breakpoint)
        const isMobile = window.innerWidth < 768;
        const targetRows = isMobile ? ROWS_MOBILE : ROWS_DESKTOP;
        const targetSpeed = isMobile ? SPEED_MOBILE : SPEED_DESKTOP;

        // Apply dynamic speed setting
        container.style.setProperty('--marquee-speed', `${targetSpeed}s`);

        // Sadece satır sayısı değiştiğinde tekrar render et
        if (targetRows === currentRows) return;
        currentRows = targetRows;

        container.innerHTML = '';

        // Satır sayısına tam bölünmesi için eksik logo varsa array'in başından kopyalayarak tamamla
        let logosToRender = [...BRAND_LOGOS];
        const remainder = logosToRender.length % targetRows;
        if (remainder !== 0) {
            const needed = targetRows - remainder;
            for (let i = 0; i < needed; i++) {
                logosToRender.push(BRAND_LOGOS[i]);
            }
        }

        // Her satırda tam eşit sayıda (chunk_size) logo olacak
        const chunk_size = logosToRender.length / targetRows;

        for (let i = 0; i < targetRows; i++) {
            const chunk = logosToRender.slice(i * chunk_size, (i + 1) * chunk_size);
            if (chunk.length === 0) break; 

            // Generate base HTML for the chunk
            let chunkHtml = '';
            chunk.forEach(logo => {
                const src = LOGO_THEME === 'white' ? logo.white : (logo.color || logo.white);
                const img = `<img src="${escapeAttr(src)}" alt="${escapeAttr(logo.alt)}" class="h-10 md:h-12 w-auto opacity-40 hover:opacity-100 transition-opacity">`;
                chunkHtml += logo.url ? `<a href="${escapeAttr(logo.url)}" target="_blank" rel="noopener" class="shrink-0">${img}</a>` : img;
            });

            // To guarantee the infinite loop has no blank gaps,
            // the "original half" must be strictly wider than the screen.
            // We estimate one logo + gap width to be ~200px.
            const estimatedChunkWidth = chunk.length * 200;
            const screenWidth = window.innerWidth;

            // Multiply the chunk if it's too short to cover the screen
            const multiplier = Math.max(1, Math.ceil(screenWidth / estimatedChunkWidth));

            let originalSetHtml = '';
            for (let m = 0; m < multiplier; m++) {
                originalSetHtml += chunkHtml;
            }

            const rowDiv = document.createElement('div');
            // Animation direction alternates
            const directionClass = i % 2 === 0 ? 'marquee-row-left' : 'marquee-row-right';

            // Note: CSS negative margin for staggered effect
            const marginLeft = i % 2 === 0 ? '' : 'ml-[-10%] md:ml-[-20%]';

            rowDiv.className = `${directionClass} flex items-center justify-start gap-12 md:gap-16 whitespace-nowrap w-max px-8 ${marginLeft}`;

            // The container is Original Set + Clone Set (so we duplicate originalSetHtml once)
            // This means translateX(-50%) will shift exactly by Original Set width!
            rowDiv.innerHTML = originalSetHtml + originalSetHtml;

            container.appendChild(rowDiv);
        }
    };

    // Render on load
    renderLogos();

    // Re-render efficiently on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(renderLogos, 200);
    });
});
