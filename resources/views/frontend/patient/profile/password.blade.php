@extends('frontend.layouts.app')

@section('title', __('Change Password'))

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
  <div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
      <div class="flex justify-between items-center bg-gradient-to-r from-sky-600 to-blue-600 text-white px-6 py-4">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Change Password') }}</h2>
        <a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 transition">
          <i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
        </a>
      </div>
      <div class="p-6">
        <form action="{{ route('user.profile.password.update') }}" method="POST" class="space-y-6">
          @csrf
          @method('PUT')

          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Current Password') }}</label>
            <input type="password" name="current_password" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            @error('current_password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
              <input type="password" name="password" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
              @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
              <input type="password" name="password_confirmation" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
          </div>

          <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold">
              {{ __('Update Password') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
