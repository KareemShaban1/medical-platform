<section class="w-full bg-white py-16">
    <div class="container mx-auto flex flex-col md:flex-row items-center gap-10 px-6">
        <!-- Left: Text Content -->
        <div class="md:w-1/2 space-y-6 text-position">
            <h2 class="text-4xl md:text-4xl font-bold text-gray-900 leading-tight">
                {{ __('banner heading') }}
            </h2>
            <p class="text-gray-600 text-lg leading-relaxed">
                {{ __('banner sub heading 1') }}
                <br>
                {{ __('banner sub heading 2') }}
            </p>


            <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-4">
                <a href="#register-section"
                    class="btn-primary text-white px-6 py-3 rounded-full font-semibold shadow-md hover:bg-green-700 transition">
                    {{ __('register now') }}

                </a>

                <button type="button" onclick="openDemoModal()"
                    class="flex items-center justify-center gap-2 border border-[var(--primary-color)] text-[var(--primary-color)] px-6 py-3 rounded-full font-semibold hover:bg-indigo-50 transition group">
                    <span
                        class="relative flex items-center justify-center w-8 h-8 bg-[var(--primary-color)] rounded-full mr-1 group-hover:scale-110 transition-transform">
                        <i class="fas fa-play text-white text-xs"></i>
                    </span>
                    {{ __('watch a demo') }}
                </button>
            </div>

            <div class="pt-6">
                <p class="text-lg font-xl text-gray-500 mb-3">
                    {{ __('contact us on social media') }}</p>
                <div class="flex gap-4 items-center">
                    <a href="https://web.facebook.com/tebbplus/" target="_blank" rel="noopener noreferrer"
                        class="text-[var(--primary-color)] hover:text-black transition text-2xl" title="Facebook"><i
                            class="fab fa-facebook"></i></a>
                    <a href="https://x.com/tebbplus" target="_blank" rel="noopener noreferrer"
                        class="text-[var(--primary-color)] hover:text-black transition text-2xl"
                        title="X (Twitter)"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            fill="currentColor" viewBox="0 0 16 16">
                            <path
                                d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
                        </svg></a>
                    <a href="https://www.linkedin.com/in/tebbplus/" target="_blank" rel="noopener noreferrer"
                        class="text-[var(--primary-color)] hover:text-black transition text-2xl" title="LinkedIn"><i
                            class="fab fa-linkedin"></i></a>
                    <a href="https://www.instagram.com/tebbplus/" target="_blank" rel="noopener noreferrer"
                        class="text-[var(--primary-color)] hover:text-black transition text-2xl" title="Instagram"><i
                            class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <!-- Right: Carousel -->
        <div class="md:w-1/2">
            <div id="default-carousel" class="relative w-full rounded-xl overflow-hidden shadow-lg"
                data-carousel="slide">
                <!-- Carousel wrapper -->
                <div class="relative h-64 md:h-96 overflow-hidden rounded-xl">
                    @php
                        $carouselImages = \App\Models\Carousel::active()->ordered()->get();
                        $hasImages = $carouselImages->count() > 0;
                    @endphp

                    @if ($hasImages)
                        @foreach ($carouselImages as $index => $carousel)
                            <!-- Item {{ $index + 1 }} -->
                            <div class="{{ $index > 0 ? 'hidden ' : '' }}duration-700 ease-in-out" data-carousel-item>
                                <img src="{{ $carousel->image_url }}" class="absolute block w-full h-full object-cover"
                                    alt="{{ $carousel->title ?? 'Carousel image ' . ($index + 1) }}">
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback to default images -->
                        <!-- Item 1 -->
                        <div class="duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('frontend/images/image-1.jpg') }}"
                                class="absolute block w-full h-full object-cover" alt="">
                        </div>
                        <!-- Item 2 -->
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('frontend/images/image-2.jpg') }}"
                                class="absolute block w-full h-full object-cover" alt="">
                        </div>
                        <!-- Item 3 -->
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('frontend/images/image-3.jpg') }}"
                                class="absolute block w-full h-full object-cover" alt="">
                        </div>
                        <!-- Item 4 -->
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('frontend/images/image-4.jpg') }}"
                                class="absolute block w-full h-full object-cover" alt="">
                        </div>
                    @endif

                </div>

                <!-- Controls -->
                <button type="button" style="z-index: 1000;"
                    class="absolute top-0 left-0 z-1000 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-prev>
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 bg-white/50 rounded-full group-hover:bg-white shadow-md">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                </button>
                <button type="button" style="z-index: 1000;"
                    class="absolute top-0 right-0 z-1000 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-next>
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 bg-white/50 rounded-full group-hover:bg-white shadow-md">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Video Demo Modal -->
<div id="demoModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 md:p-8" role="dialog"
    aria-modal="true" aria-labelledby="demoModalTitle">
    <!-- Backdrop with blur -->
    <div id="demoModalBackdrop"
        class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <!-- Modal Content -->
    <div id="demoModalContent"
        class="relative w-full max-w-5xl transform transition-all duration-500 ease-out scale-90 opacity-0">
        <!-- Close Button -->
        <button type="button" onclick="closeDemoModal()"
            class="absolute -top-12 right-0 md:-top-14 md:-right-14 z-10 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white transition-all duration-300 hover:scale-110 hover:rotate-90 group"
            aria-label="{{ __('Close video') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Video Container with Glow Effect -->
        <div class="relative rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/10">
            <!-- Decorative Glow -->
            <div
                class="absolute -inset-1 bg-gradient-to-r from-[var(--primary-color)] via-purple-500 to-pink-500 rounded-2xl opacity-30 blur-xl animate-pulse">
            </div>

            <!-- Video Wrapper -->
            <div class="relative bg-gray-900 rounded-2xl overflow-hidden">
                <!-- Video Header -->
                <div
                    class="absolute top-0 left-0 right-0 z-10 bg-gradient-to-b from-black/60 to-transparent px-6 py-4 pointer-events-none">
                    <h3 id="demoModalTitle"
                        class="text-white font-semibold text-lg md:text-xl flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 bg-[var(--primary-color)] rounded-full">
                            <i class="fas fa-play text-white text-xs"></i>
                        </span>
                        {{ __('Tebbplus Platform Demo') }}
                    </h3>
                </div>

                <!-- Video Element -->
                <video id="demoVideo" class="w-full aspect-video object-contain bg-black" controls
                    controlsList="nodownload" preload="metadata" poster="">
                    <source src="{{ asset('frontend/Tebbplus-Demo.mp4') }}" type="video/mp4">
                    {{ __('Your browser does not support the video tag.') }}
                </video>
            </div>
        </div>

        <!-- Video Info Footer -->
        <div class="mt-4 flex items-center justify-between text-white/60 text-sm px-2">
            <span class="flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                {{ __('See how Tebbplus can transform your medical practice') }}
            </span>
            <span class="hidden md:flex items-center gap-2">
                <kbd class="px-2 py-1 bg-white/10 rounded text-xs">ESC</kbd>
                {{ __('to close') }}
            </span>
        </div>
    </div>
</div>

<!-- Demo Modal Styles -->
<style>
    #demoModal.show {
        display: flex !important;
    }

    #demoModal.show #demoModalBackdrop {
        opacity: 1;
    }

    #demoModal.show #demoModalContent {
        opacity: 1;
        transform: scale(1);
    }

    /* Video controls styling */
    #demoVideo::-webkit-media-controls-panel {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    }

    /* Pulse animation for glow */
    @keyframes subtle-pulse {

        0%,
        100% {
            opacity: 0.3;
        }

        50% {
            opacity: 0.5;
        }
    }

    .animate-pulse {
        animation: subtle-pulse 3s ease-in-out infinite;
    }
</style>

<!-- Demo Modal Script -->
<script>
    function openDemoModal() {
        const modal = document.getElementById('demoModal');
        const video = document.getElementById('demoVideo');

        // Show modal with animation
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Trigger animation after a small delay
        requestAnimationFrame(() => {
            modal.classList.add('show');
        });

        // Focus the modal for accessibility
        modal.focus();
    }

    function closeDemoModal() {
        const modal = document.getElementById('demoModal');
        const video = document.getElementById('demoVideo');

        // Pause video when closing
        if (video) {
            video.pause();
        }

        // Hide with animation
        modal.classList.remove('show');

        // Wait for animation to complete before hiding
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    // Close modal on backdrop click
    document.getElementById('demoModalBackdrop')?.addEventListener('click', closeDemoModal);

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('demoModal');
            if (modal && modal.classList.contains('show')) {
                closeDemoModal();
            }
        }
    });
</script>
