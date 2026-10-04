<?php
/**
 * "Start a Project" form modal.
 *
 * @package serhandemirel
 */

?>
<!-- Project Modal (Lightbox Style) -->
<div id="projectModal" class="fixed inset-0 z-[999] hidden items-center justify-center">
    <!-- Backdrop -->
    <div id="projectModalBackdrop" class="absolute inset-0 bg-black/60 backdrop-blur-md opacity-0 transition-opacity duration-300 cursor-pointer" onclick="closeProjectModal()"></div>

    <!-- Modal Content -->
    <div id="projectModalContent" class="relative w-[90%] max-w-2xl max-h-[90vh] overflow-y-auto bg-[#0a0a0a] border border-white/10 rounded-3xl shadow-2xl scale-95 opacity-0 transition-all duration-300">
        <!-- Close button -->
        <button type="button" onclick="closeProjectModal()" aria-label="<?php esc_attr_e( 'Close', 'serhandemirel' ); ?>" class="absolute top-4 right-4 p-2 text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-full transition-colors z-10 cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="p-8 md:p-12">
            <div id="formContainer">
            <div class="mb-8 text-center">
                <h3 class="text-3xl md:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 mb-2"><?php echo esc_html( sd_opt( 'form_title' ) ); ?></h3>
                <p class="text-gray-400 text-lg"><?php echo esc_html( sd_opt( 'form_intro' ) ); ?></p>
            </div>

            <form id="projectForm" class="space-y-6" autocomplete="off">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 text-left">
                        <label for="name" class="block text-sm font-medium text-gray-400 ml-1"><?php esc_html_e( 'Name', 'serhandemirel' ); ?></label>
                        <input type="text" id="name" name="Name" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-transparent transition-all" placeholder="<?php esc_attr_e( 'John Doe', 'serhandemirel' ); ?>" autocomplete="off">
                    </div>
                    <div class="space-y-2 text-left">
                        <label for="email" class="block text-sm font-medium text-gray-400 ml-1"><?php esc_html_e( 'Email', 'serhandemirel' ); ?></label>
                        <input type="email" id="email" name="Email" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-transparent transition-all" placeholder="<?php esc_attr_e( 'john@example.com', 'serhandemirel' ); ?>" autocomplete="off">
                    </div>
                </div>

                <div class="space-y-2 text-left">
                    <label for="message" class="block text-sm font-medium text-gray-400 ml-1"><?php esc_html_e( 'Project Details', 'serhandemirel' ); ?></label>
                    <textarea id="message" name="Message" rows="4" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-transparent transition-all resize-none" placeholder="<?php esc_attr_e( 'Describe your project, goals, and timeline...', 'serhandemirel' ); ?>" autocomplete="off"></textarea>
                </div>


                <div id="formMessage" class="hidden rounded-xl p-4 text-sm font-medium transition-all"></div>

                <button type="submit" id="submitBtn" class="w-full group relative overflow-hidden rounded-xl p-[1px] mt-2">
                    <span class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-500 opacity-80 group-hover:opacity-100 transition-opacity duration-300"></span>
                    <div class="relative bg-[#0a0a0a] px-8 py-4 rounded-xl flex items-center justify-center gap-2 transition-all duration-300 hover:bg-transparent">
                        <span class="text-lg font-bold text-white"><?php echo esc_html( sd_opt( 'form_button' ) ); ?></span>
                        <svg class="w-5 h-5 text-white group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </button>
            </form>
        </div>

        <div id="successContainer" class="hidden text-center py-12">
            <div class="w-20 h-20 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-3xl md:text-4xl font-bold text-white mb-4">
                <?php
                /* translators: %s: visitor's first name */
                printf( esc_html__( 'Awesome, %s!', 'serhandemirel' ), '<span id="successName" class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></span>' );
                ?>
            </h3>
            <p class="text-gray-400 text-lg mb-8"><?php echo esc_html( sd_opt( 'form_success_text' ) ); ?></p>
            <button type="button" onclick="closeProjectModal()" class="px-8 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full text-white font-medium transition-colors">
                <?php esc_html_e( 'Close Window', 'serhandemirel' ); ?>
            </button>
        </div>
        </div>
    </div>
</div>
