@extends('frontend.layouts.app')

@section('title', __('My Tickets'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endpush

@section('content')
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
                <!-- Header -->
                <div
                    class="flex justify-between items-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4">
                    <h2 class="text-xl font-semibold">{{ __('My Tickets') }}</h2>
                    <a href="{{ route('doctor.dashboard') }}"
                        class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
                    </a>
                </div>

                <div class="p-6">
                    <!-- Nav tabs -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8">
                            <button id="submitted-tab"
                                class="tab-btn active border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">{{ __('Submitted Tickets') }}</button>
                            <button id="new-tab"
                                class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">{{ __('Submit New Ticket') }}</button>
                        </nav>
                    </div>

                    <!-- Submitted Tickets -->
                    <div id="submitted-content" class="tab-content">
                        <div class="overflow-x-auto">
                            <table id="tickets-table" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Ticket #') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Type') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Created') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Last Reply') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- New Ticket Form -->
                    <div id="new-content" class="tab-content hidden">
                        <form id="new-ticket-form" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Ticket Type') }}
                                        <span class="text-red-500">*</span></label>
                                    <select name="type" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">{{ __('Select Type') }}</option>
                                        @foreach($ticketTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Details') }} <span
                                            class="text-red-500">*</span></label>
                                    <textarea name="details" rows="6" required minlength="10" maxlength="5000"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500"
                                        placeholder="{{ __('Please describe your issue or request...') }}"></textarea>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2">{{ __('Attachments') }}</label>
                                    <input type="file" name="attachments[]" multiple
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                        accept=".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx">
                                    <p class="mt-1 text-sm text-gray-500">{{ __('Max 5 files. Max 2MB each.') }}</p>
                                </div>
                                <div>
                                    <button type="submit"
                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition"
                                        @if($ticketTypes->isEmpty()) disabled @endif>
                                        {{ __('Submit Ticket') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {
            $('.tab-btn').on('click', function () {
                $('.tab-btn').removeClass('active border-blue-500 text-blue-600').addClass('border-transparent text-gray-500');
                $(this).removeClass('border-transparent text-gray-500').addClass('active border-blue-500 text-blue-600');
                $('.tab-content').addClass('hidden');
                $('#' + $(this).attr('id').replace('-tab', '-content')).removeClass('hidden');
            });

            $('#tickets-table').DataTable({
                processing: true, serverSide: true,
                ajax: '{{ route('doctor.tickets.data') }}',
                columns: [
                    { data: 'ticket_number' }, { data: 'type' }, { data: 'status' },
                    { data: 'created_at' }, { data: 'last_reply', orderable: false },
                    { data: 'action', orderable: false, searchable: false }
                ], order: [[3, 'desc']]
            });

            $('#new-ticket-form').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: '{{ route('doctor.tickets.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (r) {
                        if (r.status === 'success') {
                            $('#new-ticket-form')[0].reset();
                            $('#submitted-tab').click();
                            $('#tickets-table').DataTable().ajax.reload();
                            toastr.success(r.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(function(msgs) {
                                msgs.forEach(function(msg) { toastr.error(msg); });
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || '{{ __('Error') }}');
                        }
                    }
                });
            });
        });
    </script>
@endpush