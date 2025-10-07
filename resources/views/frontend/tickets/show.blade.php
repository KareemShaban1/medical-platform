@extends('frontend.layouts.app')

@section('title', __('Ticket Details'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $ticket->ticket_number }}</h1>
                    <p class="text-gray-600">{{ __('Ticket Details and Conversation') }}</p>
                </div>
                <div class="flex space-x-2">
                    {!! $ticket->status_badge !!}
                    {!! $ticket->type_badge !!}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Ticket Details -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Ticket Details') }}</h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">{{ $ticket->details }}</p>
                    </div>

                    @if($ticket->attachments)
                        <div class="mt-4">
                            <h3 class="text-md font-medium text-gray-900 mb-2">{{ __('Attachments') }}</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($ticket->attachments as $attachment)
                                    <a href="{{ $attachment['url'] }}" target="_blank"
                                       class="flex items-center p-2 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-600 hover:text-blue-800 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm truncate">{{ $attachment['name'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Conversation -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Conversation') }}</h2>

                    <div class="space-y-4 mb-6">
                        @forelse($ticket->replies as $reply)
                            <div class="flex {{ $reply->is_admin_reply ? 'justify-start' : 'justify-end' }}">
                                <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-lg {{ $reply->is_admin_reply ? 'bg-gray-100 text-gray-800' : 'bg-blue-600 text-white' }}">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium">{{ $reply->author_name }}</span>
                                        <span class="text-xs {{ $reply->is_admin_reply ? 'text-gray-500' : 'text-blue-100' }}">
                                            {{ $reply->created_at->format('M d, H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-sm">{{ $reply->message }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-7-4L3 20l4-4c-1.105-1.105-2-2.447-2-4 0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No replies yet') }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ __('Start the conversation by sending a reply.') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Reply Form -->
                    @if($ticket->isOpen())
                        <div class="border-t pt-6">
                            <h3 class="text-md font-medium text-gray-900 mb-3">{{ __('Send Reply') }}</h3>
                            <form id="reply-form" method="POST" action="{{ route('user.tickets.reply', $ticket->id) }}">
                                @csrf
                                <div class="mb-4">
                                    <textarea name="message" rows="4" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="{{ __('Type your reply here...') }}"></textarea>
                                </div>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    {{ __('Send Reply') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="border-t pt-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">{{ __('Ticket Closed') }}</h3>
                                        <p class="mt-1 text-sm text-yellow-700">{{ __('This ticket is closed and cannot receive new replies.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Actions') }}</h3>
                    <div class="space-y-3">
                        <a href="{{ route('user.tickets.index') }}" class="w-full bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            {{ __('Back to Tickets') }}
                        </a>
                    </div>
                </div>

                <!-- Ticket Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Ticket Information') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('Ticket Number') }}</span>
                            <p class="text-sm text-gray-900">{{ $ticket->ticket_number }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('Type') }}</span>
                            <div class="mt-1">{!! $ticket->type_badge !!}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('Status') }}</span>
                            <div class="mt-1">{!! $ticket->status_badge !!}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('Created') }}</span>
                            <p class="text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        @if($ticket->closed_at)
                            <div>
                                <span class="text-sm font-medium text-gray-500">{{ __('Closed') }}</span>
                                <p class="text-sm text-gray-900">{{ $ticket->closed_at->format('M d, Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle reply form submission
    $('#reply-form').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                // Reload the page to show new reply
                location.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                if (errors) {
                    Object.keys(errors).forEach(function(key) {
                        toast_error(errors[key][0]);
                    });
                } else {
                    toast_error(xhr.responseJSON.message || '{{ __('Something went wrong') }}');
                }
            }
        });
    });
});
</script>
@endpush
