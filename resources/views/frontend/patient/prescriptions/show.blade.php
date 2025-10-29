@extends('frontend.layouts.app')

@section('title', __('Prescription Details'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-10 px-4">
  <div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Prescription Details') }}</h1>
      <a href="{{ route('user.prescriptions.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
        <i class="fas fa-arrow-left"></i> {{ __('Back to Prescriptions') }}
      </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Doctor') }}</div>
          <div class="text-gray-800 dark:text-gray-100 font-semibold">{{ $prescription->doctorProfile?->name ?? '-' }}</div>
        </div>
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Date') }}</div>
          <div class="text-gray-800 dark:text-gray-100 font-semibold">{{ $prescription->created_at->format('M d, Y') }}</div>
        </div>
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Appointment Date') }}</div>
          <div class="text-gray-800 dark:text-gray-100 font-semibold">{{ optional($prescription->appointment?->period?->date)->format('M d, Y') ?? '-' }}</div>
        </div>
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Notes') }}</div>
          <div class="text-gray-800 dark:text-gray-100">{{ $prescription->notes ?? '-' }}</div>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Prescription Items') }}</h2>
      @if($prescription->items->count())
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/40">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Drug') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Dose') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Frequency') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Duration') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Notes') }}</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              @foreach($prescription->items as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">{{ $item->drug_name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $item->dose }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $item->frequency }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $item->duration }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $item->notes }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center text-gray-600 dark:text-gray-300">{{ __('No items added') }}</div>
      @endif
    </div>

    @if(count($prescription->images))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Attachments') }}</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($prescription->images as $img)
          <a href="{{ $img }}" target="_blank" class="block">
            <img src="{{ $img }}" class="rounded-lg shadow object-cover w-full h-32" alt="attachment">
          </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

