@extends('backend.dashboards.supplier.layouts.app')

@section('content')
<div class="container-fluid">
          <div class="row">
                    <div class="col-12">
                              <div class="page-title-box">
                                        <div class="page-title-right">
                                                  <a href="{{ route('supplier.users.index') }}" class="btn btn-secondary">
                                                            <i class="mdi mdi-arrow-left"></i> {{ __('Back to Users') }}
                                                  </a>
                                        </div>
                                        <h4 class="page-title">{{ __('Trashed Users') }}</h4>
                              </div>
                    </div>
          </div>

          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <table id="users-trash-table" class="table dt-responsive nowrap w-100">
                                                            <thead>
                                                                      <tr>
                                                                                <th>{{ __('ID') }}</th>
                                                                                <th>{{ __('Name') }}</th>
                                                                                <th>{{ __('Email') }}</th>
                                                                                <th>{{ __('Phone') }}</th>
                                                                                <th>{{ __('Role') }}</th>
                                                                                <th>{{ __('Status') }}</th>
                                                                                <th>{{ __('Deleted At') }}</th>
                                                                                <th>{{ __('Actions') }}</th>
                                                                      </tr>
                                                            </thead>
                                                  </table>
                                        </div>
                              </div>
                    </div>
          </div>
</div>
@endsection

@push('scripts')
<script>
let trashTable = $('#users-trash-table').DataTable({
          ajax: '{{ route("supplier.users.trash.data") }}',
          columns: [{
                              data: 'id',
                              name: 'id'
                    },
                    {
                              data: 'name',
                              name: 'name'
                    },
                    {
                              data: 'email',
                              name: 'email'
                    },
                    {
                              data: 'phone',
                              name: 'phone'
                    },
                    {
                              data: 'roles',
                              name: 'roles',
                              orderable: false,
                              searchable: false
                    },
                    {
                              data: 'status',
                              name: 'status'
                    },
                    {
                              data: 'deleted_at',
                              name: 'deleted_at'
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
                                        columns: [0, 1, 2, 3, 4, 5]
                              }
                    },
                    {
                              extend: 'excel',
                              text: 'Excel',
                              title: 'Trashed Users Data',
                              exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5]
                              }
                    },
                    {
                              extend: 'copy',
                              exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5]
                              }
                    },
          ],
          drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass(
                              'pagination-rounded');
          }
});

// Restore User
function restoreUser(id) {
          Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to restore this user?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, restore it!'
          }).then((result) => {
                    if (result.isConfirmed) {
                              $.ajax({
                                        url: '{{ route("supplier.users.restore", ":id") }}'.replace(':id', id),
                                        method: 'POST',
                                        headers: {
                                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(response) {
                                                  trashTable.ajax.reload();
                                                  Swal.fire('Restored!', response.message, 'success');
                                        }
                              });
                    }
          });
}

// Force Delete User
function forceDeleteUser(id) {
          Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the user. You won't be able to revert this!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete permanently!'
          }).then((result) => {
                    if (result.isConfirmed) {
                              $.ajax({
                                        url: '{{ route("supplier.users.force.delete", ":id") }}'.replace(':id', id),
                                        method: 'DELETE',
                                        headers: {
                                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(response) {
                                                  trashTable.ajax.reload();
                                                  Swal.fire('Deleted!', response.message, 'success');
                                        },
                                        error: function(xhr) {
                                                  let errorMessage = 'Something went wrong';
                                                  if (xhr.responseJSON && xhr.responseJSON.message) {
                                                        errorMessage = xhr.responseJSON.message;
                                                  } else if (xhr.responseText) {
                                                        try {
                                                              let response = JSON.parse(xhr.responseText);
                                                              errorMessage = response.message || errorMessage;
                                        }
                                        }

                                                  Swal.fire('Error!', errorMessage, 'error');
                                        }
                              });
                    }
          });
}
</script>
@endpush
