@extends('frontend.layouts.app')

@section('title', __('Patient Dashboard'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-10 px-4">
    <div class="w-full max-w-4xl">
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden">
            
            <!-- Header -->
            <div class="flex justify-between items-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4">
                <h2 class="text-xl font-semibold">{{ __('Patient Dashboard') }}</h2>

                <form method="POST" action="{{ route('user.logout') }}" onsubmit="return confirm('{{ __('Are you sure you want to logout?') }}')">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-sign-out-alt"></i>
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>

            <!-- Body -->
            <div class="p-8">

                <!-- Greeting -->
                <div class="text-center mb-10">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100">
                        👋 {{ __('Hello :name', ['name' => $patient->name]) }}
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Welcome to your patient dashboard') }}</p>
                </div>

                <!-- Feature Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @php
                        $features = [
                            ['icon' => 'fa-user', 'color' => 'text-blue-500', 'title' => __('Profile Information'), 'desc' => __('View and update your profile')],
                            ['icon' => 'fa-calendar', 'color' => 'text-green-500', 'title' => __('Appointments'), 'desc' => __('Manage your appointments')],
                            ['icon' => 'fa-file-medical', 'color' => 'text-sky-500', 'title' => __('Medical Records'), 'desc' => __('Access your medical history')],
                            ['icon' => 'fa-pills', 'color' => 'text-yellow-500', 'title' => __('Prescriptions'), 'desc' => __('View your prescriptions')],
                        ];
                    @endphp

                    @foreach($features as $feature)
                        <div class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition rounded-xl shadow p-6 text-center">
                            <i class="fas {{ $feature['icon'] }} {{ $feature['color'] }} text-4xl mb-3"></i>
                            <h5 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $feature['title'] }}</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-300">{{ $feature['desc'] }}</p>
                        </div>
                    @endforeach
                </div>

                <hr class="my-8 border-gray-300 dark:border-gray-600">

                <!-- Patient Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h6 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">
                            {{ __('Patient Information') }}
                        </h6>
                        <ul class="space-y-1 text-gray-600 dark:text-gray-300">
                            <li><strong>{{ __('Name') }}:</strong> {{ $patient->name }}</li>
                            <li><strong>{{ __('Email') }}:</strong> {{ $patient->email ?? __('Not provided') }}</li>
                            <li><strong>{{ __('Phone') }}:</strong> {{ $patient->phone }}</li>
                        </ul>
                    </div>

                    <div>
                        <h6 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">
                            {{ __('Account Status') }}
                        </h6>
                        <ul class="space-y-1 text-gray-600 dark:text-gray-300">
                            <li>
                                <strong>{{ __('Account Type') }}:</strong>
                                @if($patient->isRegistered())
                                    <span class="ml-2 px-3 py-1 bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200 rounded-full text-xs font-medium">
                                        {{ __('Registered User') }}
                                    </span>
                                @else
                                    <span class="ml-2 px-3 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-200 rounded-full text-xs font-medium">
                                        {{ __('Clinic Created') }}
                                    </span>
                                @endif
                            </li>
                            <li><strong>{{ __('Member Since') }}:</strong> {{ $patient->created_at->format('F Y') }}</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
