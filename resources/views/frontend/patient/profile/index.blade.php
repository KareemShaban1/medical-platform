@extends('frontend.layouts.app')

@section('title', __('Profile Information'))

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
  <div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">{{ __('Profile Information') }}</h1>
      <a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 transition">
        <i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
      </a>
    </div>

    <div class="bg-white shadow-xl rounded-2xl p-6">
      <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
          <label class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
          <input type="text" name="name" value="{{ old('name', $user?->name) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
          @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
          <input type="email" name="email" value="{{ old('email', $user?->email) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
          @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
          <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-gray-100 text-gray-700 shadow-sm cursor-not-allowed" disabled>
          @error('phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between">
          <a href="{{ route('user.profile.password') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition">
            <i class="fas fa-key mr-1"></i> {{ __('Change Password') }}
          </a>
          <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold">
            {{ __('Save Changes') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
