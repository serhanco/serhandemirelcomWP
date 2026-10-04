<?php
/**
 * Contact footer.
 *
 * @package serhandemirel
 */

?>
<!-- 7. Footer / Contact Section -->
<footer id="contact" class="relative bg-[#020202] text-white pt-24 pb-8 overflow-hidden rounded-t-[3rem] md:rounded-t-[5rem] border-t border-white/10 z-20 shadow-[0_-20px_50px_rgba(0,0,0,0.5)] -mt-6">
    <!-- Devasa Footer Glow -->
    <div class="absolute bottom-[-10%] md:bottom-[-30%] left-[50%] -translate-x-1/2 w-[500px] md:w-[80vw] h-[500px] md:h-[80vw] rounded-full bg-purple-600/15 md:bg-purple-900/10 blur-[100px] md:blur-[150px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 md:px-12 relative z-10 flex flex-col min-h-[60vh] justify-between">
        <div class="flex flex-col md:flex-row justify-between items-start gap-12">
            <div class="max-w-3xl">
                <div class="flex items-center gap-3 mb-8">
                    <div class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <span class="text-gray-400 text-xs md:text-sm font-semibold tracking-[0.2em] uppercase"><?php echo esc_html( sd_opt( 'status_badge' ) ); ?></span>
                </div>
                <h2 class="text-5xl md:text-7xl lg:text-8xl font-black tracking-tighter leading-[0.95] mb-6">
                    <?php echo esc_html( sd_opt( 'cta_line1' ) ); ?><br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"><?php echo esc_html( sd_opt( 'cta_line2' ) ); ?></span>
                </h2>
            </div>

            <div class="flex flex-col items-start md:items-end gap-6 text-right w-full md:w-auto">
                <div class="glass rounded-3xl p-6 w-full md:w-64 text-left md:text-right border-white/5">
                    <p class="text-gray-500 text-xs font-bold tracking-widest uppercase mb-2"><?php esc_html_e( 'Local Time', 'serhandemirel' ); ?></p>
                    <p id="footer-time" class="text-3xl font-light tracking-tight text-white font-mono">--:--:--</p>
                    <p class="text-gray-400 text-sm mt-1"><?php echo esc_html( sd_opt( 'city_label' ) ); ?></p>
                </div>
            </div>
        </div>

        <!-- Interaktif İletişim Butonları -->
        <div class="w-full mt-16 mb-20 flex flex-wrap gap-4">
            <button type="button" onclick="openProjectModal()" class="flex-1 min-w-[250px] group relative overflow-hidden rounded-full p-[1px]">
                <span class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-500 opacity-50 group-hover:opacity-100 transition-opacity duration-300"></span>
                <div class="relative bg-[#050505] px-8 py-6 rounded-full flex items-center justify-between transition-transform duration-300 group-hover:scale-[0.99]">
                    <span class="text-xl font-bold"><?php esc_html_e( 'Start a Project', 'serhandemirel' ); ?></span>
                    <svg class="w-6 h-6 text-white group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
            </button>
            <!-- Devasa WhatsApp Butonu -->
            <a href="<?php echo esc_url( sd_whatsapp_url() ); ?>" target="_blank" rel="noopener" data-location="footer" class="flex-1 min-w-[250px] group relative overflow-hidden rounded-full p-[1px]">
                <span class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-emerald-600 opacity-50 group-hover:opacity-100 transition-opacity duration-300"></span>
                <div class="relative bg-[#050505] px-8 py-6 rounded-full flex items-center justify-between transition-transform duration-300 group-hover:scale-[0.99]">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                        <span class="text-xl font-bold"><?php esc_html_e( 'WhatsApp', 'serhandemirel' ); ?></span>
                    </div>
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
            </a>

            <a href="<?php echo esc_url( sd_email_url() ); ?>" data-location="footer" class="flex-1 min-w-[250px] glass px-8 py-6 rounded-full flex items-center justify-between hover:bg-white/10 transition-colors border-white/5">
                <span class="text-xl font-bold"><?php esc_html_e( 'Email Me', 'serhandemirel' ); ?></span>
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </a>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-6 pt-8 border-t border-white/10 text-gray-500 text-sm font-medium">
            <p>
                <?php
                /* translators: %s: current year */
                printf( esc_html__( '© %s Serhan Demirel. All rights reserved.', 'serhandemirel' ), '<span id="current-year">' . esc_html( gmdate( 'Y' ) ) . '</span>' );
                ?>
            </p>
            <div class="flex gap-8">
                <?php
                foreach ( array( 'linkedin' => 'LinkedIn', 'github' => 'GitHub', 'instagram' => 'Instagram' ) as $network => $label ) :
                    if ( ! sd_opt( $network ) ) {
                        continue;
                    }
                    ?>
                <a href="<?php echo esc_url( sd_opt( $network ) ); ?>" target="_blank" rel="noopener" class="hover:text-white transition-colors"><?php echo esc_html( $label ); ?></a>
                <?php endforeach; ?>
            </div>
            <button id="back-to-top" class="hover:text-white transition-colors flex items-center gap-2 group cursor-pointer">
                <?php esc_html_e( 'Back to top', 'serhandemirel' ); ?>
                <svg class="w-4 h-4 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
            </button>
        </div>
    </div>
</footer>
