<?php
/**
 * Floating WhatsApp / Start a Project bar.
 *
 * @package serhandemirel
 */

if ( ! sd_opt( 'sticky_bar' ) ) {
	return;
}
?>
<!-- Floating Dual Contact Bar (Mobilde ve masaüstünde scroll edince çıkar) -->
<div id="sticky-contact" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] translate-y-[150%] opacity-0 transition-all duration-500 ease-out pointer-events-none flex gap-3">
    <a href="<?php echo esc_url( sd_whatsapp_url() ); ?>" target="_blank" rel="noopener" data-location="sticky_bar" class="pointer-events-auto group flex items-center gap-2 bg-[#050505] border border-emerald-500/30 px-4 md:px-5 py-3 rounded-full shadow-[0_0_20px_rgba(16,185,129,0.2)] hover:border-emerald-500/70 hover:bg-emerald-500/10 transition-all hover:scale-105">
        <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
        <span class="text-sm font-semibold text-emerald-400 whitespace-nowrap hidden md:block"><?php esc_html_e( 'WhatsApp', 'serhandemirel' ); ?></span>
    </a>
    <button type="button" onclick="openProjectModal()" class="pointer-events-auto group flex items-center gap-3 bg-white/10 border border-white/20 backdrop-blur-2xl px-6 py-3 rounded-full shadow-2xl hover:bg-white/20 transition-all hover:scale-105">
        <div class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
        </div>
        <span class="text-sm font-semibold text-white whitespace-nowrap"><?php esc_html_e( 'Start a Project', 'serhandemirel' ); ?></span>
    </button>
</div>
