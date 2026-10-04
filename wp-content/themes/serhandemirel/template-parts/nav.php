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
<div id="mobile-fullscreen-menu" class="fixed inset-0 z-[100] bg-[#0a0a0a]/95 backdrop-blur-3xl opacity-0 pointer-events-none transition-all duration-500 flex flex-col justify-center items-center">
    <div id="mobile-menu-content" class="flex flex-col gap-10 text-center scale-90 translate-y-8 opacity-0 transition-all duration-500 delay-100 w-full px-6">
        <?php foreach ( $sd_items as $sd_item ) : ?>
        <a href="<?php echo esc_url( $sd_item['url'] ); ?>" class="mobile-link text-5xl font-black text-gray-400 hover:text-white transition-colors flex items-center justify-center gap-4">
            <?php if ( $sd_item['icon'] ) : ?><span class="text-4xl"><?php echo esc_html( $sd_item['icon'] ); ?></span><?php endif; ?> <?php echo esc_html( $sd_item['label'] ); ?>
        </a>
        <?php endforeach; ?>
        <a href="<?php echo esc_url( sd_anchor( '#contact' ) ); ?>" class="mobile-link mt-8 py-5 w-full rounded-full bg-white text-black text-2xl font-bold hover:bg-gray-200 transition-colors flex items-center justify-center gap-3">
            <span class="text-3xl">👋</span> <?php esc_html_e( "Let's Talk", 'serhandemirel' ); ?>
        </a>
        <?php if ( count( $sd_languages ) > 1 ) : ?>
        <div class="flex justify-center gap-2 text-sm font-semibold">
            <?php foreach ( $sd_languages as $sd_lang ) : ?>
            <a href="<?php echo esc_url( $sd_lang['url'] ); ?>" hreflang="<?php echo esc_attr( $sd_lang['locale'] ); ?>" lang="<?php echo esc_attr( $sd_lang['locale'] ); ?>" class="px-3 py-2 rounded-full border <?php echo $sd_lang['current'] ? 'border-white bg-white text-black' : 'border-white/20 text-gray-300 hover:bg-white/10'; ?>"><?php echo esc_html( strtoupper( $sd_lang['slug'] ) ); ?></a>
            <?php endforeach; ?>
        </div>
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
        <div class="relative group">
            <button type="button" class="flex items-center gap-1 hover:text-white transition-colors uppercase" aria-haspopup="true" aria-label="<?php esc_attr_e( 'Language', 'serhandemirel' ); ?>">
                <?php echo esc_html( sd_current_lang() ); ?>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div class="absolute right-0 top-full pt-3 hidden group-hover:block group-focus-within:block">
                <div class="flex flex-col min-w-[9rem] rounded-2xl bg-[#0a0a0a]/95 backdrop-blur-xl border border-white/10 p-2">
                    <?php foreach ( $sd_languages as $sd_lang ) : ?>
                    <a href="<?php echo esc_url( $sd_lang['url'] ); ?>" hreflang="<?php echo esc_attr( $sd_lang['locale'] ); ?>" lang="<?php echo esc_attr( $sd_lang['locale'] ); ?>" class="px-3 py-2 rounded-xl hover:bg-white/10 <?php echo $sd_lang['current'] ? 'text-white' : 'text-gray-400'; ?>"><?php echo esc_html( $sd_lang['name'] ); ?></a>
                    <?php endforeach; ?>
                </div>
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
