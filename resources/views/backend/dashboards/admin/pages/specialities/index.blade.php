@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#itemModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Add Speciality') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Doctor Specialities') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="items-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name English') }}</th>
                                <th>{{ __('Name Arabic') }}</th>
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
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalLabel">{{ __('Add Speciality') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="itemForm" method="POST">
                    @csrf
                    <input type="hidden" id="itemId">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="name_ar" class="form-label">{{ __('Name Arabic') }}</label>
                            <input type="text" class="form-control" id="name_ar" name="name_ar">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="name_en" class="form-label">{{ __('Name English') }}</label>
                            <input type="text" class="form-control" id="name_en" name="name_en">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
<script>
    let table = $('#items-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.specialities.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name_en', name: 'name_en' },
            { data: 'name_ar', name: 'name_ar' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[0, 'desc']],
        dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
        pageLength: 10,
        responsive: true,
        language: languages[language],
        buttons: [
            { extend: 'print', exportOptions: { columns: [0,1,2] } },
            { extend: 'excel', text: 'Excel', title: 'Specialities Data', exportOptions: { columns: [0,1,2] } },
            { extend: 'copy', exportOptions: { columns: [0,1,2] } },
        ],
        drawCallback: function() {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });

    function resetForm() {
        $('#itemForm')[0].reset();
        $('#itemForm').attr('action', '{{ route("admin.specialities.store") }}');
        $('#itemId').val('');
        $('#itemModal .modal-title').text('{{ __("Add Speciality") }}');
        clearErrors();
    }

    function clearErrors(){
        $('#itemForm .is-invalid').removeClass('is-invalid');
        $('#itemForm .invalid-feedback').text('');
    }

    // Submit create/update
    $('#itemForm').on('submit', function(e){
        e.preventDefault();
        let id = $('#itemId').val();
        let url = id ? '{{ route("admin.specialities.update", ":id") }}'.replace(':id', id) : '{{ route("admin.specialities.store") }}';
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(resp){
                $('#itemModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', resp.message, 'success');
            },
            error: function(xhr){
                if (xhr.status === 422) {
                    clearErrors();
                    const errors = xhr.responseJSON?.errors || {};
                    Object.keys(errors).forEach(function (key) {
                        let $input = $('[name="'+key+'"]').first();
                        $input.addClass('is-invalid');
                        $input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
                }
            }
        });
    });

    window.editItem = function(id){
        $.get('{{ route("admin.specialities.show", ":id") }}'.replace(':id', id), function(item){
            resetForm();
            $('#itemId').val(item.id);
            $('#name_en').val(item.name_en);
            $('#name_ar').val(item.name_ar);
            $('#itemForm').attr('action', '{{ route("admin.specialities.update", ":id") }}'.replace(':id', id));
            $('#itemModal .modal-title').text('{{ __("Edit Speciality") }}');
            $('#itemModal').modal('show');
        });
    }

    window.deleteItem = function(id){
        Swal.fire({
            title: '{{ __("Delete Speciality?") }}',
            text: '{{ __("This action cannot be undone") }}',
            icon: 'warning',
            showCancelButton: true
        }).then((res)=>{
            if(res.isConfirmed){
                $.ajax({
                    url: '{{ route("admin.specialities.destroy", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(resp){
                        table.ajax.reload();
                        Swal.fire('Success', resp.message, 'success');
                    },
                    error: function(xhr){
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
                    }
                })
            }
        });
    }
</script>
@endpush

