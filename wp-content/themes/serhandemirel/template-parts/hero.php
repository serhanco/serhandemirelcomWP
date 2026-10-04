<?php
/**
 * Hero with particle canvas.
 *
 * @package serhandemirel
 */

?>
<!-- 1. Hero Section (with Paritcles) -->
<header id="hero" class="relative min-h-[100svh] w-full flex flex-col justify-center items-center text-center px-4 border-none overflow-x-clip">
    <!-- Parçacık Canvası -->
    <canvas id="particle-canvas" class="absolute inset-0 z-0 opacity-60 pointer-events-auto"></canvas>

    <!-- Güçlendirilmiş Hero Ambient Glow (Contact tonlarında) -->
    <div class="absolute top-[-10%] left-[-20%] md:top-[-20%] md:left-[-10%] w-[400px] md:w-[60vw] h-[400px] md:h-[60vw] rounded-full bg-purple-900/40 md:bg-purple-900/30 blur-[100px] md:blur-[150px] pointer-events-none z-0"></div>
    <div class="absolute bottom-[-10%] right-[-20%] md:right-[-10%] w-[350px] md:w-[40vw] h-[350px] md:h-[40vw] rounded-full bg-fuchsia-900/30 md:bg-fuchsia-900/20 blur-[100px] md:blur-[120px] pointer-events-none z-0"></div>

    <div class="relative z-10 hero-content mt-[-5vh]">
        <!-- Giriş Rozeti -->
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-white/10 bg-white/5 backdrop-blur-sm mb-6 shadow-[0_0_15px_rgba(255,255,255,0.05)]">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-fuchsia-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-fuchsia-500"></span>
            </span>
            <span class="text-xs font-semibold text-gray-300 tracking-wider uppercase"><?php echo esc_html( sd_opt( 'hero_badge' ) ); ?></span>
        </div>

        <h1 class="text-5xl md:text-7xl lg:text-8xl font-black tracking-tight mb-4 md:mb-6 leading-tight text-white">
            <?php echo esc_html( sd_opt( 'hero_title' ) ); ?>
        </h1>
        <p class="text-lg md:text-2xl text-gray-400 font-light max-w-2xl mx-auto">
            <?php echo esc_html( sd_opt( 'hero_subtitle' ) ); ?>
        </p>
    </div>

    <div class="absolute bottom-12 flex flex-col items-center opacity-60 hero-scroll z-10">
        <span class="text-xs tracking-widest uppercase mb-3 text-gray-400 font-medium"><?php echo esc_html( sd_opt( 'hero_explore' ) ); ?></span>
        <div class="w-[1px] h-12 bg-gradient-to-b from-transparent via-white to-transparent animate-pulse"></div>
    </div>
</header>
