@extends('frontend.layouts.app')

@section('title', __('Ticket Details') . ' - ' . $ticket->ticket_number)

@section('content')
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
                <!-- Header -->
                <div
                    class="flex justify-between items-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4">
                    <h2 class="text-xl font-semibold">{{ __('Ticket') }} #{{ $ticket->ticket_number }}</h2>
                    <a href="{{ route('doctor.tickets.index') }}"
                        class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Tickets') }}
                    </a>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Ticket Info -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('Ticket Info') }}</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between"><span
                                            class="text-gray-500">{{ __('Type') }}:</span>{!! $ticket->type_badge !!}</div>
                                    <div class="flex justify-between"><span
                                            class="text-gray-500">{{ __('Status') }}:</span>{!! $ticket->status_badge !!}
                                    </div>
                                    <div class="flex justify-between"><span
                                            class="text-gray-500">{{ __('Created') }}:</span><span>{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($ticket->getMedia('ticket_attachments')->count())
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('Attachments') }}</h3>
                                    <ul class="space-y-2 text-sm">
                                        @foreach($ticket->getMedia('ticket_attachments') as $media)
                                            <li><a href="{{ $media->getUrl() }}" target="_blank"
                                                    class="text-blue-600 hover:underline"><i
                                                        class="fas fa-file me-1"></i>{{ $media->file_name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Conversation -->
                        <div class="lg:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Conversation') }}</h3>

                            <!-- Original Message -->
                            <div class="flex mb-4">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-content-center flex-shrink-0">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ __('You') }} <span
                                            class="text-gray-500 font-normal">{{ $ticket->created_at->diffForHumans() }}</span>
                                    </p>
                                    <p class="text-gray-700 mt-1">{{ $ticket->details }}</p>
                                </div>
                            </div>

                            @foreach($ticket->replies as $reply)
                                <div class="flex mb-4 {{ $reply->is_admin_reply ? 'justify-end' : '' }}">
                                    @if(!$reply->is_admin_reply)
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-content-center flex-shrink-0">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div class="{{ $reply->is_admin_reply ? 'mr-3 text-right' : 'ml-3' }}">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $reply->is_admin_reply ? __('Admin') : __('You') }} <span
                                                class="text-gray-500 font-normal">{{ $reply->created_at->diffForHumans() }}</span>
                                        </p>
                                        <p
                                            class="text-gray-700 mt-1 {{ $reply->is_admin_reply ? 'bg-gray-100 p-3 rounded-lg inline-block' : '' }}">
                                            {{ $reply->message }}
                                        </p>
                                    </div>
                                    @if($reply->is_admin_reply)
                                        <div
                                            class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-content-center flex-shrink-0">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            @if(!$ticket->isClosed())
                                <hr class="my-6">
                                <form id="reply-form">
                                    @csrf
                                    <div class="mb-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('Your Reply') }}</label>
                                        <textarea name="message" rows="3" required minlength="5"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                            placeholder="{{ __('Type your reply...') }}"></textarea>
                                    </div>
                                    <button type="submit"
                                        class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                                        <i class="fas fa-paper-plane me-1"></i> {{ __('Send Reply') }}
                                    </button>
                                </form>
                            @else
                                <div class="bg-gray-100 rounded-lg p-4 text-center text-gray-600">
                                    <i class="fas fa-lock me-1"></i> {{ __('This ticket is closed.') }}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#reply-form').on('submit', function (e) {
            e.preventDefault();
            $.post('{{ route('doctor.tickets.reply', $ticket->id) }}', $(this).serialize(), function (r) {
                if (r.status === 'success') {
                    toastr.success(r.message);
                    location.reload();
                }
            }).fail(function (xhr) {
                toastr.error(xhr.responseJSON?.message || '{{ __('Error') }}');
            });
        });
    </script>
@endpush