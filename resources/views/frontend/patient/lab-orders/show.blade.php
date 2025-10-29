@extends('frontend.layouts.app')

@section('title', __('Lab Result'))

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
            <div class="flex justify-between items-center bg-gradient-to-r from-sky-600 to-blue-600 text-white px-6 py-4">
                <h2 class="text-2xl font-bold text-gray-800">{{ __('Lab Result') }} #{{ $order->id }}</h2>
                <a href="{{ route('user.lab-orders.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 transition">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to lab Orders') }}
                </a>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $order->test_name }}</h3>
                    <p class="text-sm text-gray-500">{{ __('Clinic') }}: {{ $order->clinic?->name }} • {{ __('Reviewed at') }}: {{ $order->reviewed_at?->format('Y-m-d H:i') }}</p>
                    <p class="text-sm text-gray-500">{{ __('Lab') }}: {{ $order->lab_name ?: '—' }}</p>
                    @if($order->result_comment)
                        <div class="mt-3 p-3 rounded-lg bg-gray-50">
                            <p class="text-sm text-gray-700"><strong>{{ __('Doctor/Clinic Comment') }}:</strong> {{ $order->result_comment }}</p>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">{{ __('Files') }}</h4>
                    @if(count($order->attachments) > 0)
                        <ul class="space-y-2">
                            @foreach($order->attachments as $file)
                                <li class="flex items-center justify-between p-3 rounded-lg border border-gray-200">
                                    <span class="text-gray-700">{{ $file['name'] }}</span>
                                    <a href="{{ $file['url'] }}" target="_blank" class="px-3 py-1 rounded bg-gray-800 text-white text-sm">{{ __('Download') }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">{{ __('No files available.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
