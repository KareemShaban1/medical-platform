@extends('frontend.layouts.app')

@section('title', __('My Tickets'))

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('My Tickets') }}</h1>
            <p class="text-gray-600">{{ __('Manage your support tickets and requests') }}</p>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button id="submitted-tab" class="tab-button active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('Submitted Tickets') }}
                    </button>
                    <button id="new-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('Submit New Ticket') }}
                    </button>
                </nav>
            </div>

            <!-- Submitted Tickets Tab -->
            <div id="submitted-content" class="tab-content p-6">
                <div class="overflow-x-auto">
                    <table id="tickets-table" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Ticket Number') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Created') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Last Reply') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>

            <!-- New Ticket Tab -->
            <div id="new-content" class="tab-content p-6 hidden">
                <form id="new-ticket-form" method="POST" action="{{ route('user.tickets.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Ticket Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Ticket Type') }} <span class="text-red-500">*</span></label>
                            <select name="type" id="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Select Ticket Type') }}</option>
                                <option value="refund">{{ __('Refund Request') }}</option>
                                <option value="complaint">{{ __('Complaint') }}</option>
                            </select>
                        </div>



                        <!-- Details -->
                        <div>
                            <label for="details" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Details') }} <span class="text-red-500">*</span></label>
                            <textarea name="details" id="details" rows="6" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="{{ __('Please provide detailed information about your request or complaint...') }}"></textarea>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Minimum 10 characters, maximum 5000 characters') }}</p>
                        </div>

                        <!-- Attachments -->
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Attachments') }}</label>
                            <input type="file" name="attachments[]" id="attachments" multiple
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                accept=".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx">
                            <p class="mt-1 text-sm text-gray-500">{{ __('You can upload up to 5 files. Allowed formats: JPEG, PNG, GIF, PDF, DOC, DOCX. Max size: 2MB per file.') }}</p>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                                {{ __('Submit Ticket') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
$(document).ready(function() {
    // Tab switching
    $('.tab-button').on('click', function() {
        const tabId = $(this).attr('id');
        const contentId = tabId.replace('-tab', '-content');

        // Update tab styles
        $('.tab-button').removeClass('active border-blue-500 text-blue-600').addClass('border-transparent text-gray-500');
        $(this).removeClass('border-transparent text-gray-500').addClass('active border-blue-500 text-blue-600');

        // Show/hide content
        $('.tab-content').addClass('hidden');
        $('#' + contentId).removeClass('hidden');
    });

    // Initialize DataTable for submitted tickets
    $('#tickets-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('user.tickets.data') }}',
        columns: [
            { data: 'ticket_number', name: 'ticket_number' },
            { data: 'type', name: 'type' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'last_reply', name: 'last_reply', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        responsive: true
    });



    // Handle ticket reply
    $('#reply-form').on('submit', function(e) {
        e.preventDefault();

        let ticketId = $(this).data('ticket-id');
        let formData = $(this).serialize();

        $.ajax({
            url: `/user/tickets/${ticketId}/reply`,
            method: 'POST',
            data: formData,
            success: function(response) {
                closeReplyModal();
                $('#tickets-table').DataTable().ajax.reload();
                toast_success(response.message);
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
