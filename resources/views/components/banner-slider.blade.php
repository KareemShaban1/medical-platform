@props(['position' => null, 'pageIdentifier' => null, 'height' => 'auto', 'autoHeight' => true])

@php
    use App\Models\Banner;
    
    // Get current page identifier if not provided
    if (!$pageIdentifier) {
        $routeName = request()->route() ? request()->route()->getName() : null;
        
        // Map route names to page identifiers used in target_pages
        $pageMap = [
            'home' => 'home',
            'about' => 'about',
            'terms' => 'terms',
            'privacy' => 'privacy',
            'return-policy' => 'return-policy',
            'shipping-policy' => 'shipping-policy',
            'products' => 'products',
            'products.show' => 'products.show',
            'products.category' => 'products.category',
            'products.on-sale' => 'products.on-sale',
            'products.in-stock' => 'products.in-stock',
            'clinics' => 'clinics',
            'clinics.show' => 'clinics.show',
            'doctors.index' => 'doctors',
            'rental-spaces' => 'rental-spaces',
            'rental-spaces.show' => 'rental-spaces.show',
            'suppliers' => 'suppliers',
            'suppliers.show' => 'suppliers.show',
            'blogs' => 'blogs',
            'blogs.show' => 'blogs.show',
            'courses' => 'courses',
            'courses.show' => 'courses.show',
            'jobs' => 'jobs',
            'jobs.show' => 'jobs.show',
        ];
        
        $pageIdentifier = $pageMap[$routeName] ?? $routeName;
    }
    
    // Get banners for this page
    $banners = Banner::active()
        ->when($position, fn($q) => $q->byPosition($position))
        ->forPage($pageIdentifier)
        ->ordered()
        ->get();
    
    $bannerCount = $banners->count();
@endphp

@if($bannerCount > 0)
    <section class="banner-slider-section" style="position: relative; width: 100%; overflow: hidden;">
        @if($bannerCount > 1)
            <!-- Multiple Banners - Use Slider -->
            <div class="swiper banner-slider-swiper" style="width: 100%;">
                <div class="swiper-wrapper">
                    @foreach($banners as $banner)
                        <div class="swiper-slide">
                            <x-banner :banner="$banner" />
                        </div>
                    @endforeach
                </div>
                
                <!-- Navigation -->
                <div class="swiper-button-next" style="color: white; background: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px;"></div>
                <div class="swiper-button-prev" style="color: white; background: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px;"></div>
                
                <!-- Pagination -->
                <div class="swiper-pagination" style="bottom: 20px;"></div>
            </div>
        @else
            <!-- Single Banner - No Slider -->
            @foreach($banners as $banner)
                <x-banner :banner="$banner" />
            @endforeach
        @endif
    </section>

    @if($bannerCount > 1)
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper for banner slider
            const bannerSwiper = new Swiper('.banner-slider-swiper', {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: {{ $bannerCount > 2 ? 'true' : 'false' }},
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                @if($autoHeight)
                autoHeight: true,
                @endif
            });
            
            // Track banner views
            @foreach($banners as $banner)
                @if($loop->first)
                    // Track view for first banner (visible on load)
                    fetch('{{ route('api.banners.track-view', $banner->id) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    }).catch(err => console.log('View tracking failed'));
                @endif
            @endforeach
            
            // Track view when slide changes
            bannerSwiper.on('slideChange', function() {
                const activeIndex = this.activeIndex;
                const banners = @json($banners->pluck('id')->toArray());
                if (banners[activeIndex]) {
                    fetch('{{ url('/api/banners') }}/' + banners[activeIndex] + '/track-view', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    }).catch(err => console.log('View tracking failed'));
                }
            });
        });
        </script>
        @endpush
    @else
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Track view for single banner
            @foreach($banners as $banner)
                fetch('{{ route('api.banners.track-view', $banner->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                }).catch(err => console.log('View tracking failed'));
            @endforeach
        });
        </script>
        @endpush
    @endif
@endif

