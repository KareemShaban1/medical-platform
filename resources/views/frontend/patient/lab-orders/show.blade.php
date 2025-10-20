@extends('frontend.layouts.app')

@section('title', __('Lab Result'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-10 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden">
            <div class="flex justify-between items-center bg-gradient-to-r from-sky-600 to-blue-600 text-white px-6 py-4">
                <h2 class="text-xl font-semibold">{{ __('Lab Result') }} #{{ $order->id }}</h2>
                <a href="{{ route('user.lab-orders.index') }}" class="text-white/90 hover:text-white text-sm">{{ __('Back') }}</a>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $order->test_name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Clinic') }}: {{ $order->clinic?->name }} • {{ __('Reviewed at') }}: {{ $order->reviewed_at?->format('Y-m-d H:i') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Lab') }}: {{ $order->lab_name ?: '—' }}</p>
                    @if($order->result_comment)
                        <div class="mt-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>{{ __('Doctor/Clinic Comment') }}:</strong> {{ $order->result_comment }}</p>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-2">{{ __('Files') }}</h4>
                    @if(count($order->attachments) > 0)
                        <ul class="space-y-2">
                            @foreach($order->attachments as $file)
                                <li class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-700 dark:text-gray-200">{{ $file['name'] }}</span>
                                    <a href="{{ $file['url'] }}" target="_blank" class="px-3 py-1 rounded bg-gray-800 text-white text-sm">{{ __('Download') }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 dark:text-gray-300">{{ __('No files available.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
