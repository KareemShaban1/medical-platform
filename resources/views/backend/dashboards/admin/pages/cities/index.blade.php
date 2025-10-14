@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#citiesModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Add City') }}
                    </button>
                    <!-- trash button -->
                     <a href="{{ route('admin.cities.trash') }}" class="btn btn-danger">
                        <i class="mdi mdi-trash"></i> {{ __('Trash') }}
                     </a>
                </div>
                <h4 class="page-title">{{ __('Cities') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="cities-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Governorate') }}</th>
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
<div class="modal fade" id="citiesModal" tabindex="-1" role="dialog" aria-labelledby="citiesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="citiesModalLabel">{{ __('Add City') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="citiesForm" method="POST">
                    @csrf
                    <input type="hidden" id="citiesId">
                    <div class="row">
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
    let table = $('#cities-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.cities.data") }}',
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'governorate',
                name: 'governorate'
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
        $('#citiesForm')[0].reset();
        $('#citiesForm').attr('action', '{{ route("admin.cities.store") }}');
        $('#citiesId').val('');
        $('#citiesModal .modal-title').text('{{ __("Add City") }}');
    }

    // Handle Add/Edit Form Submission
    $('#citiesForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#citiesId').val();
        let url = id ?
            '{{ route("admin.cities.update", ":id") }}'.replace(':id', id) :
            '{{ route("admin.cities.store") }}';
        let method = id ? 'PUT' : 'POST';



        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#citiesModal').modal('hide');
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
                            let nameSelector ='[name="' + key + '"]';
                            let $input = $(nameSelector);
                            // fallback for inputs using array syntax:
                            if (!$input.length) {
                                // try ends-with matching
                                $input = $('#citiesForm')
                                    .find('[name^="' +key +'"]');
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

    // Edit
    function editCity(id) {
        $.get('{{ route("admin.cities.index") }}/' + id, function(data) {
            $('#citiesId').val(data.id);
            $('#name').val(data.name);
            $('#governorate_id').val(data.governorate_id);

            $('#citiesForm').attr('action',
                '{{ route("admin.cities.update", ":id") }}'.replace(
                    ':id', id));
            $('#citiesModal .modal-title').text('{{ __("Edit City") }}');
            $('#citiesModal').modal('show');
        });
    }


    // Delete
    function deleteCity(id) {
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
                    url: '{{ route("admin.cities.index") }}/' +
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


   
</script>
@endpush