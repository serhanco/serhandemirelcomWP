<?php
/**
 * Fullscreen mobile menu and floating glass navbar.
 *
 * Links come from the menu assigned to "Main menu" under Appearance → Menus;
 * without one, they point to the visible front-page sections.
 *
 * @package serhandemirel
 */

$sd_items     = sd_nav_items();
$sd_languages = sd_language_links();
?>
<!-- Fullscreen Premium Mobile Menu -->
<div id="mobile-fullscreen-menu" class="fixed inset-0 z-[100] bg-[#0a0a0a]/95 backdrop-blur-3xl opacity-0 pointer-events-none transition-all duration-500 flex flex-col justify-[safe_center] items-center overflow-y-auto py-28">
    <div id="mobile-menu-content" class="flex flex-col gap-8 text-center scale-90 translate-y-8 opacity-0 transition-all duration-500 delay-100 w-full px-6">
        <?php foreach ( $sd_items as $sd_item ) : ?>
        <a href="<?php echo esc_url( $sd_item['url'] ); ?>" class="mobile-link text-5xl font-black text-gray-400 hover:text-white transition-colors flex items-center justify-center gap-4">
            <?php if ( $sd_item['icon'] ) : ?><span class="text-4xl"><?php echo esc_html( $sd_item['icon'] ); ?></span><?php endif; ?> <?php echo esc_html( $sd_item['label'] ); ?>
        </a>
        <?php endforeach; ?>
        <a href="<?php echo esc_url( sd_anchor( '#contact' ) ); ?>" class="mobile-link mt-8 py-5 w-full rounded-full bg-white text-black text-2xl font-bold hover:bg-gray-200 transition-colors flex items-center justify-center gap-3">
            <span class="text-3xl">👋</span> <?php esc_html_e( "Let's Talk", 'serhandemirel' ); ?>
        </a>
        <?php if ( count( $sd_languages ) > 1 ) : ?>
        <nav class="mt-2" aria-labelledby="sd-lang-mobile-label">
            <p id="sd-lang-mobile-label" class="mb-3 text-xs font-semibold tracking-[0.2em] uppercase text-gray-500"><?php esc_html_e( 'Language', 'serhandemirel' ); ?></p>
            <ul class="grid grid-cols-3 gap-2 max-w-xs mx-auto">
                <?php foreach ( $sd_languages as $sd_lang ) : ?>
                <li>
                    <a href="<?php echo esc_url( $sd_lang['url'] ); ?>" hreflang="<?php echo esc_attr( $sd_lang['locale'] ); ?>" lang="<?php echo esc_attr( $sd_lang['locale'] ); ?>" data-sd-lang="<?php echo esc_attr( $sd_lang['slug'] ); ?>"<?php echo $sd_lang['current'] ? ' aria-current="true"' : ''; ?> class="flex flex-col items-center justify-center min-h-[3.5rem] rounded-2xl border transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70 <?php echo $sd_lang['current'] ? 'border-white bg-white text-black' : 'border-white/15 text-white hover:bg-white/10 hover:border-white/30'; ?>">
                        <span class="text-base font-bold tracking-wider"><?php echo esc_html( strtoupper( $sd_lang['slug'] ) ); ?></span>
                        <span class="text-[11px] font-medium <?php echo $sd_lang['current'] ? 'text-gray-600' : 'text-gray-400'; ?>"><?php echo esc_html( $sd_lang['name'] ); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Floating Glass Navbar -->
<nav id="main-nav" class="fixed top-6 left-1/2 -translate-x-1/2 z-[110] w-[90%] max-w-4xl rounded-full bg-white/5 backdrop-blur-xl border border-white/10 px-6 py-4 flex justify-between items-center transition-all duration-300">
    <a href="<?php echo esc_url( sd_anchor( '#' ) ); ?>" class="text-xl font-extrabold tracking-tighter cursor-pointer text-white relative z-20">SD.</a>

    <!-- Desktop Menu -->
    <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-300">
        <?php foreach ( $sd_items as $sd_item ) : ?>
        <a href="<?php echo esc_url( $sd_item['url'] ); ?>" class="hover:text-white transition-colors"><?php echo esc_html( $sd_item['label'] ); ?></a>
        <?php endforeach; ?>
        <?php if ( count( $sd_languages ) > 1 ) : ?>
        <div class="relative group" data-sd-lang-menu>
            <button type="button" id="sd-lang-button" class="flex items-center gap-1.5 rounded-full border border-white/10 px-3 py-1.5 text-gray-300 hover:text-white hover:bg-white/10 hover:border-white/20 group-[.is-open]:text-white group-[.is-open]:bg-white/10 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60" aria-haspopup="true" aria-expanded="false" aria-controls="sd-lang-panel">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0c2.2-2.4 3.3-5.4 3.3-9S14.2 5.4 12 3m0 18c-2.2-2.4-3.3-5.4-3.3-9S9.8 5.4 12 3M3.5 9h17M3.5 15h17"></path></svg>
                <span class="sr-only"><?php esc_html_e( 'Language:', 'serhandemirel' ); ?></span>
                <span class="text-xs font-semibold tracking-wider uppercase"><?php echo esc_html( sd_current_lang() ); ?></span>
                <svg class="w-3 h-3 transition-transform duration-200 group-[.is-open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div id="sd-lang-panel" class="absolute right-0 top-full pt-3 invisible opacity-0 translate-y-1 pointer-events-none transition duration-200 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto group-[.is-open]:visible group-[.is-open]:opacity-100 group-[.is-open]:translate-y-0 group-[.is-open]:pointer-events-auto">
                <ul class="w-56 rounded-2xl bg-[#0a0a0a]/95 backdrop-blur-xl border border-white/10 p-1.5 shadow-2xl shadow-black/50" aria-labelledby="sd-lang-button">
                    <?php foreach ( $sd_languages as $sd_lang ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $sd_lang['url'] ); ?>" hreflang="<?php echo esc_attr( $sd_lang['locale'] ); ?>" lang="<?php echo esc_attr( $sd_lang['locale'] ); ?>" data-sd-lang="<?php echo esc_attr( $sd_lang['slug'] ); ?>"<?php echo $sd_lang['current'] ? ' aria-current="true"' : ''; ?> class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors focus:outline-none focus-visible:bg-white/10 hover:bg-white/10 <?php echo $sd_lang['current'] ? 'text-white' : 'text-gray-400 hover:text-white'; ?>">
                            <span class="w-6 text-[11px] font-bold tracking-wider <?php echo $sd_lang['current'] ? 'text-fuchsia-400' : 'text-gray-500'; ?>"><?php echo esc_html( strtoupper( $sd_lang['slug'] ) ); ?></span>
                            <span class="flex-1"><?php echo esc_html( $sd_lang['name'] ); ?></span>
                            <?php if ( $sd_lang['current'] ) : ?>
                            <svg class="w-4 h-4 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        <a href="<?php echo esc_url( sd_anchor( '#contact' ) ); ?>" class="px-5 py-2 rounded-full bg-white text-black font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Contact', 'serhandemirel' ); ?></a>
    </div>

    <!-- Mobile Menu Trigger -->
    <button id="mobile-menu-btn" class="md:hidden text-white focus:outline-none relative w-8 h-8 z-[120] flex items-center justify-center bg-transparent" aria-label="<?php esc_attr_e( 'Menu', 'serhandemirel' ); ?>">
        <!-- Hamburger Icon -->
        <svg id="icon-menu" class="w-6 h-6 absolute transition-all duration-300 transform rotate-0 opacity-100 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        <!-- Close Icon -->
        <svg id="icon-close" class="w-6 h-6 absolute transition-all duration-300 transform -rotate-90 opacity-0 scale-50 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</nav>
