<?php
/**
 * "We can ..." word slot, pinned while scrolling.
 *
 * @package serhandemirel
 */

?>
<!-- 2. Dynamic Words Section -->
<section id="dynamic-words-section" class="relative w-full min-h-screen overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] md:w-[70vw] h-[400px] md:h-[70vw] rounded-full bg-pink-900/20 md:bg-pink-900/10 blur-[100px] md:blur-[150px] pointer-events-none z-0"></div>

    <div id="dynamic-words-content" class="relative z-10 h-screen w-full flex items-center justify-center px-4">
        <div class="text-4xl md:text-6xl lg:text-8xl font-bold flex flex-wrap justify-center items-center gap-x-3 md:gap-x-5 text-center">
            <span class="text-gray-100"><?php echo esc_html( sd_opt( 'words_prefix' ) ); ?></span>
            <div class="word-mask">
                <div id="word-slider" class="word-slider-inner gradient-text">
                    <!-- JS Injects Words Here -->
                </div>
            </div>
        </div>
    </div>
</section>
