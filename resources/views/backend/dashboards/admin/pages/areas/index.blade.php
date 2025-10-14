@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#areasModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Add Area') }}
                    </button>
                    <!-- trash button -->
                    <a href="{{ route('admin.areas.trash') }}" class="btn btn-danger">
                        <i class="mdi mdi-trash"></i> {{ __('Trash') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Areas') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="areas-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Governorate') }}</th>
                                <th>{{ __('City') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="areasModal" tabindex="-1" role="dialog" aria-labelledby="areasModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="areasModalLabel">{{ __('Add Area') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="areasForm" method="POST">
                    @csrf
                    <input type="hidden" id="areasId">
                    <div class="row">
                        <!-- governorate -->
                        <div class="col-12 col-md-6 mb-3">
                            <label for="governorate_id"
                                class="form-label">{{ __('Governorate') }}</label>
                            <select name="governorate_id" id="governorate_id" class="form-select">
                                <option value="">{{ __('Select Governorate') }}</option>
                                @foreach ($governorates as $governorate)
                                <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- city -->
                        <div class="col-12 col-md-6 mb-3">
                            <label for="city_id"
                                class="form-label">{{ __('City') }}</label>
                            <select name="city_id" id="city_id" class="form-select">
                                <option value="">{{ __('Select City') }}</option>

                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="name"
                                class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control"
                                id="name" name="name">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit"
                            class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let table = $('#areas-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.areas.data") }}',
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'governorate',
                name: 'governorate'
            },
            {
                data: 'city',
                name: 'city'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },

        ],
        order: [
            [0, 'desc']
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
        pageLength: 10,
        responsive: true,
        language: languages[language],
        buttons: [{
                extend: 'print',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
            {
                extend: 'excel',
                text: 'Excel',
                title: 'Cities Data',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
            {
                extend: 'copy',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
        ],
        drawCallback: function() {
            $('.dataTables_paginate > .pagination').addClass(
                'pagination-rounded');
        }
    });

    // Reset form
    function resetForm() {
        $('#areasForm')[0].reset();
        $('#areasForm').attr('action', '{{ route("admin.areas.store") }}');
        $('#areasId').val('');
        $('#areasModal .modal-title').text('{{ __("Add Area") }}');
    }

    // Handle Add/Edit Form Submission
    $('#areasForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#areasId').val();
        let url = id ?
            '{{ route("admin.areas.update", ":id") }}'.replace(':id', id) :
            '{{ route("admin.areas.store") }}';
        let method = id ? 'PUT' : 'POST';



        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#areasModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', response.message,
                    'success');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON
                        .errors || {};
                    // show inline feedback and aggregated alert
                    let messages = [];
                    Object.keys(errors).forEach(
                        function(key) {
                            messages.push(errors[key][0]);
                            // find input (supports nested names like items[0][price])
                            let nameSelector = '[name="' + key + '"]';
                            let $input = $(nameSelector);
                            // fallback for inputs using array syntax:
                            if (!$input.length) {
                                // try ends-with matching
                                $input = $('#areasForm')
                                    .find('[name^="' + key + '"]');
                            }
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                $input.next('.invalid-feedback').text(errors[key][0]);
                            }
                        });
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors',
                        html: messages
                            .join(
                                '<br>'
                            )
                    });
                } else {
                    Swal.fire('Error', 'Something went wrong',
                        'error');
                }
            }
        });
    });

   function editArea(id) {
    $.get('{{ route("admin.areas.index") }}/' + id, function (data) {
        // Set modal title
        $('#areasModal .modal-title').text('{{ __("Edit Area") }}');

        // Fill basic inputs
        $('#areasId').val(data.id);
        $('#name').val(data.name);

        // Set governorate first
        $('#governorate_id').val(data.city.governorate_id).trigger('change');

        // Wait for cities to load after governorate change
        // (because cities are loaded by AJAX)
        const checkCitiesLoaded = setInterval(() => {
            if ($('#city_id option').length > 1) {
                $('#city_id').val(data.city_id);
                clearInterval(checkCitiesLoaded);
            }
        }, 200);

        // Update form action
        $('#areasForm').attr(
            'action',
            '{{ route("admin.areas.update", ":id") }}'.replace(':id', id)
        );

        // Show modal
        $('#areasModal').modal('show');
    });
}



    // Delete
    function deleteArea(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.areas.index") }}/' +
                        id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            )
                            .attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Deleted!',
                            response
                            .message,
                            'success'
                        );
                    }
                });
            }
        });
    }


    // governorate change
    $('#governorate_id').on('change', function() {
        const governorateId = $(this).val();
        const $citySelect = $('#city_id');

        $citySelect.empty().append('<option value="">{{ __("Select City") }}</option>');

        if (!governorateId) {
            return; // if no governorate selected, stop
        }

        $.ajax({
            url: '{{ route("admin.cities.get-cities-by-governorate-id") }}',
            type: 'GET',
            data: {
                id: governorateId
            },
            success: function(data) {
                if (Array.isArray(data) && data.length > 0) {
                    $.each(data, function(key, city) {
                        $citySelect.append(`<option value="${city.id}">${city.name}</option>`);
                    });
                } else {
                    $citySelect.append('<option value="">{{ __("No cities available") }}</option>');
                }
            },
            error: function() {
                alert('Error loading cities. Please try again.');
            }
        });
    });
</script>
@endpush