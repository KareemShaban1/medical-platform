@extends('frontend.layouts.app')

@section('title', __('Medical Record'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-10 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden">
            <div class="flex justify-between items-center bg-gradient-to-r from-sky-600 to-blue-600 text-white px-6 py-4">
                <h2 class="text-xl font-semibold">{{ __('Medical Record') }}</h2>
                <a href="{{ route('user.medical-records.index') }}" class="text-white/90 hover:text-white text-sm">{{ __('Back') }}</a>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <div class="text-gray-700 dark:text-gray-100 font-semibold">{{ __('Doctor') }}</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ $record->doctor?->name ?: 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-gray-700 dark:text-gray-100 font-semibold">{{ __('Appointment Date') }}</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ optional($record->appointment?->period?->date)->format('Y-m-d') ?: 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-gray-700 dark:text-gray-100 font-semibold">{{ __('Visit Type') }}</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ [0=>'Initial',1=>'Follow-up',2=>'Consultation'][$record->visit_type] ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-gray-700 dark:text-gray-100 font-semibold">{{ __('Chief Complaint') }}</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ $record->chief_complaint ?: __('N/A') }}</div>
                </div>
                <div>
                    <div class="text-gray-700 dark:text-gray-100 font-semibold">{{ __('Diagnosis') }}</div>
                    <div class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $record->diagnosis ?: __('N/A') }}</div>
                </div>
                <div>
                    <div class="text-gray-700 dark:text-gray-100 font-semibold">{{ __('Treatment') }}</div>
                    <div class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $record->treatment ?: __('N/A') }}</div>
                </div>
                <div>
                    <div class="text-gray-700 dark:text-gray-100 font-semibold">{{ __('Notes') }}</div>
                    <div class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $record->notes ?: __('N/A') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
