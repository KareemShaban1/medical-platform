@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
          <div class="row">
                    <div class="col-12">
                              <div class="page-title-box">
                                        <div class="page-title-right">
                                                  <a href="{{ route('clinic.users.trash') }}" class="btn btn-warning me-2">
                                                            <i class="mdi mdi-delete"></i> {{ __('Trash') }}
                                                  </a>
                                                  <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#usersModal" onclick="resetForm()">
                                                            <i class="mdi mdi-plus"></i> {{ __('Add User') }}
                                                  </button>
                                        </div>
                                        <h4 class="page-title">{{ __('Users') }}</h4>
                              </div>
                    </div>
          </div>

          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <table id="users-table" class="table dt-responsive nowrap w-100">
                                                            <thead>
                                                                      <tr>
                                                                                <th>{{ __('ID') }}</th>
                                                                                <th>{{ __('Name') }}</th>
                                                                                <th>{{ __('Email') }}</th>
                                                                                <th>{{ __('Phone') }}</th>
                                                                                <th>{{ __('Roles') }}</th>
                                                                                <th>{{ __('Status') }}</th>
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
<div class="modal fade" id="usersModal" tabindex="-1" role="dialog" aria-labelledby="usersModalLabel"
          aria-hidden="true">
          <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                              <div class="modal-header">
                                        <h5 class="modal-title" id="usersModalLabel">{{ __('Add User') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                  aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                        <form id="usersForm" method="POST">
                                                  @csrf
                                                  <input type="hidden" id="usersId">
                                                  <div class="row">
                                                            <div class="col-12 col-md-6 mb-3">
                                                                      <label for="name"
                                                                                class="form-label">{{ __('Name') }}</label>
                                                                      <input type="text" class="form-control"
                                                                                id="name" name="name" required>
                                                                      <div class="invalid-feedback"></div>
                                                            </div>

                                                            <div class="col-12 col-md-6 mb-3">
                                                                      <label for="email"
                                                                                class="form-label">{{ __('Email') }}</label>
                                                                      <input type="email" class="form-control"
                                                                                id="email" name="email" required>
                                                                      <div class="invalid-feedback"></div>
                                                            </div>

                                                            <div class="col-12 col-md-6 mb-3">
                                                                      <label for="phone"
                                                                                class="form-label">{{ __('Phone') }}</label>
                                                                      <input type="text" class="form-control"
                                                                                id="phone" name="phone" required>
                                                                      <div class="invalid-feedback"></div>
                                                            </div>

                                                            <div class="col-12 col-md-6 mb-3">
                                                                      <label for="password"
                                                                                class="form-label">{{ __('Password') }}</label>
                                                                      <input type="password" class="form-control"
                                                                                id="password" name="password" required>
                                                                      <div class="invalid-feedback"></div>
                                                            </div>

                                                            <div class="col-12 col-md-6 mb-3">
                                                                      <label for="password_confirmation"
                                                                                class="form-label">{{ __('Confirm Password') }}</label>
                                                                      <input type="password" class="form-control"
                                                                                id="password_confirmation" name="password_confirmation" required>
                                                                      <div class="invalid-feedback"></div>
                                                            </div>

                                                            <div class="col-12 col-md-6 mb-3">
                                                                      <label for="role"
                                                                                class="form-label">{{ __('Role') }}</label>
                                                                      <select class="form-control" id="role"
                                                                                name="role" required>
                                                                                <option value="">{{ __('Select a Role') }}</option>
                                                                      </select>
                                                                      <div class="invalid-feedback"></div>
                                                            </div>

                                                            <div class="col-12 col-md-6 mb-3">
                                                                      <div class="form-check form-switch mt-4">
                                                                                <input type="hidden" name="status"
                                                                                          id="statusHidden" value="1">
                                                                                <input type="checkbox"
                                                                                          class="form-check-input"
                                                                                          id="statusToggle" checked>
                                                                                <label class="form-check-label"
                                                                                          for="statusToggle">{{ __('Status') }}</label>
                                                                      </div>
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
let table = $('#users-table').DataTable({
          ajax: '{{ route("clinic.users.data") }}',
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
                              title: 'Users Data',
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

// Load roles for dropdown
function loadRoles() {
          $.get('{{ route("clinic.users.roles") }}', function(data) {
                    $('#role').empty();
                    $('#role').append('<option value="">{{ __("Select a Role") }}</option>');
                    data.forEach(function(role) {
                              $('#role').append('<option value="' + role.name + '">' + role.name + '</option>');
                    });
          });
}

// Reset form
function resetForm() {
          $('#usersForm')[0].reset();
          $('#usersForm').attr('action', '{{ route("clinic.users.store") }}');
          $('#usersId').val('');
          $('#usersModal .modal-title').text('{{ __("Add User") }}');
          $('#password').prop('required', true);
          $('#password_confirmation').prop('required', true);
          $('.is-invalid').removeClass('is-invalid');
          $('.invalid-feedback').text('');
          loadRoles();
}

// Handle Add/Edit Form Submission
$('#usersForm').on('submit', function(e) {
          e.preventDefault();
          let id = $('#usersId').val();
          let url = id ?
                    '{{ route("clinic.users.update", ":id") }}'.replace(':id', id) :
                    '{{ route("clinic.users.store") }}';
          let method = id ? 'PUT' : 'POST';

          let formData = new FormData(this);

          if (method === 'PUT') {
                    formData.append('_method', 'PUT');
          }

          $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                              $('#usersModal').modal('hide');
                              table.ajax.reload();
                              Swal.fire('Success', response.message,
                                        'success');
                    },
                    error: function(xhr) {
                              if (xhr.status === 422) {
                                        let errors = xhr.responseJSON
                                                  .errors || {};
                                        let messages = [];
                                        Object.keys(errors).forEach(
                                                  function(
                                                            key
                                                  ) {
                                                            messages.push(errors[
                                                                                key
                                                                      ]
                                                                      [
                                                                                0
                                                                      ]
                                                            );
                                                            let nameSelector =
                                                                      '[name="' +
                                                                      key +
                                                                      '"]';
                                                            let $input =
                                                                      $(
                                                                                nameSelector
                                                                      );
                                                            if (!$input
                                                                      .length
                                                            ) {
                                                                      $input = $(
                                                                                          '#usersForm'
                                                                                )
                                                                                .find('[name^="' +
                                                                                          key +
                                                                                          '"], [name$="' +
                                                                                          key +
                                                                                          '"]'
                                                                                );
                                                            }
                                                            if ($input
                                                                      .length
                                                            ) {
                                                                      $input.addClass(
                                                                                'is-invalid'
                                                                      );
                                                                      $input.next(
                                                                                          '.invalid-feedback'
                                                                                )
                                                                                .text(errors[
                                                                                                    key
                                                                                          ]
                                                                                          [
                                                                                                    0
                                                                                          ]
                                                                                );
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
function editUser(id) {
          $.get('{{ route("clinic.users.index") }}/' + id, function(data) {
                    $('#usersId').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                    $('#statusToggle').prop('checked', data.status);

                    loadRoles();
                    setTimeout(function() {
                              if (data.roles && data.roles.length > 0) {
                                        $('#role').val(data.roles[0].name);
                              }
                    }, 500);

                    $('#password').prop('required', false);
                    $('#password_confirmation').prop('required', false);
                    $('#usersForm').attr('action',
                              '{{ route("clinic.users.update", ":id") }}'.replace(
                                        ':id', id));
                    $('#usersModal .modal-title').text('{{ __("Edit User") }}');
                    $('#usersModal').modal('show');
          });
}

// Delete
function deleteUser(id) {
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
                                        url: '{{ route("clinic.users.destroy", ":id") }}'.replace(':id', id),
                                        method: 'DELETE',
                                        headers: {
                                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(response) {
                                                  table.ajax.reload();
                                                  Swal.fire('Deleted!', response.message, 'success');
                                        },
                                        error: function(xhr) {
                                                  Swal.fire('Error!', 'Something went wrong', 'error');
                                        }
                              });
                    }
          });
}

$('#statusToggle').on('change', function() {
          $('#statusHidden').val($(this).is(':checked') ? 1 : 0);
});

// Handle status toggle in DataTable
$(document).on('change', '.toggle-status', function() {
          let userId = $(this).data('id');
          let isChecked = $(this).is(':checked');

          $.ajax({
                    url: '{{ route("clinic.users.toggle.status", ":id") }}'.replace(':id', userId),
                    method: 'POST',
                    headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                              // Update the badge text and class
                              let $label = $(this).siblings('label');
                              let $badge = $label.find('.badge');

                              if (isChecked) {
                                        $badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                              } else {
                                        $badge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                              }

                              Swal.fire('Success', response.message, 'success');
                    }.bind(this),
                    error: function(xhr) {
                              // Revert the toggle state
                              $(this).prop('checked', !isChecked);
                              Swal.fire('Error', 'Something went wrong', 'error');
                    }.bind(this)
          });
});

// Initialize roles on page load
$(document).ready(function() {
          loadRoles();
});
</script>
@endpush
