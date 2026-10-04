<?php
/**
 * Core Expertise cards slider.
 *
 * @package serhandemirel
 */

?>
<!-- 3. Core Expertise Section -->
<section id="expertise" class="relative py-24 md:py-32 bg-[#080808] border-t border-white/5 z-10 overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-[-5%] md:top-[-10%] right-[-20%] md:right-[-10%] w-[350px] md:w-[50vw] h-[350px] md:h-[50vw] rounded-full bg-blue-900/20 md:bg-blue-900/10 blur-[100px] md:blur-[150px] pointer-events-none z-0"></div>

    <div class="relative z-10 w-full">
        <!-- Başlık ve Yönlendirme (Kapsayıcı içinde) -->
        <div class="max-w-7xl mx-auto px-6 lg:px-8 mb-12 md:mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div>
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-purple-500 mb-4"><?php echo esc_html( sd_opt( 'expertise_eyebrow' ) ); ?></h2>
                <h3 class="text-3xl md:text-5xl font-bold text-white max-w-2xl"><?php echo esc_html( sd_opt( 'expertise_title' ) ); ?></h3>
            </div>

            <!-- Modern Navigation Controls -->
            <div class="flex items-center gap-4 relative z-20 self-end md:self-auto">
                <button id="scroll-prev" class="w-12 h-12 rounded-full glass flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all duration-300 group cursor-pointer">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="scroll-next" class="w-12 h-12 rounded-full glass flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all duration-300 group cursor-pointer">
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

        <!-- Horizontal Slider Container -->
        <div class="relative w-full">
            <div id="expertise-slider" style="display:flex; flex-wrap:nowrap; gap:1.5rem; overflow-x:auto; scroll-behavior:smooth; -webkit-overflow-scrolling:touch; padding-bottom:2rem; padding-left:1.5rem; cursor:grab; scrollbar-width:none;">

                <?php
                $cards   = sd_expertise_cards();
                $accents = sd_accent_classes();
                $icons   = sd_icon_paths();
                foreach ( $cards as $i => $card ) :
                    $accent = $accents[ $card['accent'] ] ?? $accents['blue'];
                    $icon   = $icons[ $card['icon'] ] ?? $icons['growth'];
                    $style  = 'flex:0 0 auto; width:min(82vw,400px);' . ( count( $cards ) - 1 === $i ? ' margin-right:1.5rem;' : '' );
                    ?>
                <div style="<?php echo esc_attr( $style ); ?>" class="glass p-8 rounded-3xl hover:bg-white/10 transition-colors duration-500 flex flex-col select-none">
                    <div class="w-12 h-12 <?php echo esc_attr( $accent ); ?> rounded-2xl flex items-center justify-center mb-6 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo esc_attr( $icon ); ?>"></path></svg>
                    </div>
                    <h4 class="text-2xl font-bold text-white mb-3"><?php echo esc_html( $card['title'] ); ?></h4>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6 flex-grow"><?php echo esc_html( $card['description'] ); ?></p>
                    <?php if ( $card['tags'] ) : ?>
                    <div class="flex flex-wrap gap-2 mt-auto">
                        <?php foreach ( $card['tags'] as $tag ) : ?>
                        <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full text-xs text-gray-300"><?php echo esc_html( $tag ); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</section>
