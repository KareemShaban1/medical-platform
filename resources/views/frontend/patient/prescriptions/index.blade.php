@extends('frontend.layouts.app')

@section('title', __('My Prescriptions'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-10 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('My Prescriptions') }}</h1>
      <a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
        <i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
      </a>
    </div>

    @if($prescriptions->count())
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($prescriptions as $prescription)
          <a href="{{ route('user.prescriptions.show', $prescription->id) }}" class="block bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 hover:shadow-2xl hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                  {{ $prescription->doctorProfile?->name ?? __('Doctor') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ __('Created') }}: {{ $prescription->created_at->format('M d, Y') }}
                </p>
              </div>
              <div class="flex -space-x-2">
                @foreach(array_slice($prescription->images, 0, 3) as $img)
                  <img src="{{ $img }}" class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-700 object-cover" alt="prescription image">
                @endforeach
              </div>
            </div>
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-300">
              {{ \Illuminate\Support\Str::limit($prescription->notes, 120) }}
            </div>
            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
              {{ __('Items') }}: {{ $prescription->items->count() }}
            </div>
          </a>
        @endforeach
      </div>

      <div class="mt-6">
        {{ $prescriptions->links() }}
      </div>
    @else
      <div class="text-center bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-10">
        <i class="fas fa-prescription-bottle-alt text-5xl text-gray-400 mb-3"></i>
        <p class="text-gray-600 dark:text-gray-300">{{ __('No prescriptions found') }}</p>
      </div>
    @endif
  </div>
  </div>
@endsection

