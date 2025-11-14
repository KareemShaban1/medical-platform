<section class="py-16 bg-white" id="register-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-teal-600 to-emerald-500 bg-clip-text text-transparent">
                {{ __('register now') }}
            </h2>
            <p class="mt-3 text-gray-600">{{ __('register now to get access to our platform') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <!-- Patient -->
            <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 bg-primary-gradient"></div>
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-teal-500 to-emerald-500"></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-gradient text-white flex items-center justify-center shadow">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <div>
                        <h3 class="text-xxl font-bold">{{ __('patient registration') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('register now to get access to our platform') }}</p>
                    </div>
                </div>
                <div class="mt-6 flex flex-col gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-primary-gradient text-white px-4 py-2 rounded-lg shadow hover:opacity-90">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ __('register now as a patient') }}</span>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:border-gray-400">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>{{ __('login now if you have an account') }}</span>
                    </a>
                </div>
            </div>

            <!-- Supplier -->
            <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 bg-primary-gradient"></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-gradient text-white flex items-center justify-center shadow">
                        <i class="fas fa-truck text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xxl font-bold">{{ __('supplier registration') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('register now to get access to our platform') }}</p>
                    </div>
                </div>
                <div class="mt-6 flex flex-col gap-3">
                    <a href="{{ route('supplier.register-supplier') }}" class="inline-flex items-center justify-center gap-2 bg-primary-gradient text-white px-4 py-2 rounded-lg shadow hover:opacity-90">
                        <i class="fas fa-user-plus text-white"></i>
                        <span>{{ __('register now as a supplier') }}</span>
                    </a>
                    <a href="{{ url('/supplier/login') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:border-gray-400">
                        <i class="fas fa-sign-in-alt text-gray-700"></i>
                        <span>{{ __('login now if you have an account') }}</span>
                    </a>
                </div>
            </div>

            <!-- Clinic -->
            <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 bg-primary-gradient"></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-gradient text-white flex items-center justify-center shadow">
                        <i class="fas fa-hospital text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xxl font-bold">{{ __('clinic registration') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('register now to get access to our platform') }}</p>
                    </div>
                </div>
                <div class="mt-6 flex flex-col gap-3">
                    <a href="{{ route('clinic.register-clinic') }}" class="inline-flex items-center justify-center gap-2 bg-primary-gradient text-white px-4 py-2 rounded-lg shadow hover:opacity-90">
                        <i class="fas fa-user-plus text-white"></i>
                        <span>{{ __('register now as a clinic') }}</span>
                    </a>
                    <a href="{{ url('/clinic/login') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:border-gray-400">
                        <i class="fas fa-sign-in-alt text-gray-700"></i>
                        <span>{{ __('login now if you have an account') }}</span>
                    </a>
                </div>
            </div>

            <!-- Doctor -->
            <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 bg-primary-gradient"></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-gradient text-white flex items-center justify-center shadow">
                        <i class="fas fa-user-md text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xxl font-bold">{{ __('doctor registration') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('register now to get access to our platform') }}</p>
                    </div>
                </div>
                <div class="mt-6 flex flex-col gap-3">
                    <a href="{{ route('doctor.register.show') }}" class="inline-flex items-center justify-center gap-2 bg-primary-gradient text-white px-4 py-2 rounded-lg shadow hover:opacity-90">
                        <i class="fas fa-user-plus text-white"></i>
                        <span>{{ __('register now') }}</span>
                    </a>
                    <a href="{{ url('/clinic/login') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:border-gray-400">
                        <i class="fas fa-sign-in-alt text-gray-700"></i>
                        <span>{{ __('login now') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
