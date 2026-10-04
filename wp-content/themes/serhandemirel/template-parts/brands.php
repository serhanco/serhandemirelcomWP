<?php
/**
 * Brand logo marquee (rows are built by assets/js/brands.js).
 *
 * @package serhandemirel
 */

?>
<!-- 4. Logo Showcase (Faded Edges & 3 Rows) -->
<section id="brands" class="relative py-24 md:py-32 overflow-hidden bg-[#050505] border-t border-white/5">
    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 mb-16 text-center">
        <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-blue-500 mb-4"><?php echo esc_html( sd_opt( 'brands_eyebrow' ) ); ?></h2>
        <h3 class="text-3xl md:text-5xl font-bold text-white"><?php echo esc_html( sd_opt( 'brands_title' ) ); ?></h3>
    </div>

    <!-- Faded Edges using CSS mask-image -->
    <div id="marquee-container" class="w-full max-w-[1400px] mx-auto flex flex-col gap-12 mask-edges pointer-events-auto">
        <!-- Rows will be injected here by JS -->
    </div>
</section>
