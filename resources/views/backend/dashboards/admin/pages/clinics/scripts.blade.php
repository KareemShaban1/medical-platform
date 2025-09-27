@push('scripts')
    <script>
        let table = $('#clinics-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.clinics.data') }}',
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'is_allowed',
                    name: 'is_allowed'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'clinic_users',
                    name: 'clinic_users'
                },
                {
                    data: 'approval',
                    name: 'approval'
                },
                {
                    data: 'attachments',
                    name: 'attachments',
                    orderable: false,
                    searchable: false
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Clinics Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
            ],
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass(
                    'pagination-rounded');
            }
        });

        $('#images').on('change', function(e) {
            const files = this.files;
            const $preview = $('#imagesPreview');
            $preview.empty(); // clear old previews

            if (!files || files.length === 0) {
                return;
            }

            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) return; // skip non-images

                const reader = new FileReader();
                reader.onload = function(ev) {
                    const img = $('<img>')
                        .attr('src', ev.target.result)
                        .addClass('img-fluid rounded me-2 mb-2')
                        .css({
                            maxHeight: '150px',
                            maxWidth: '150px'
                        });
                    $preview.append(img);
                }
                reader.readAsDataURL(file);
            });
        });


        // Handle status toggle in DataTable






        // Reset form
        function resetForm() {
            $('#clinicsForm')[0].reset();
            $('#clinicsForm').attr('action', '{{ route('admin.clinics.store') }}');
            $('#clinicsId').val('');
            $('#imagesPreview').hide().attr('src', '');
            $('#clinicsModal .modal-title').text('{{ __('Add Clinic') }}');
        }

        // Handle Add/Edit Form Submission
        $('#clinicsForm').on('submit', function(e) {
            e.preventDefault();

            let id = $('#clinicsId').val();
            let url = id ?
                '{{ route('admin.clinics.update', ':id') }}'.replace(':id', id) :
                '{{ route('admin.clinics.store') }}';
            let method = id ? 'POST' : 'POST'; // always POST, Laravel expects _method for PUT

            // prepare form data
            let formData = new FormData(this);

            // add hidden values manually (checkboxes)
            formData.set('status', $('#statusToggle').is(':checked') ? 1 : 0);
            formData.set('is_allowed', $('#isAllowedToggle').is(':checked') ? 1 : 0);

            if (id) {
                formData.append('_method', 'PUT'); // Laravel way to spoof PUT
            }

            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false, // don't let jQuery transform FormData
                contentType: false,
                success: function(response) {
                    $('#clinicsModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Success', response.message, 'success');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors || {};
                        let messages = [];
                        Object.keys(errors).forEach(function(key) {
                            messages.push(errors[key][0]);
                            let nameSelector = '[name="' + key + '"]';
                            let $input = $(nameSelector);
                            if (!$input.length) {
                                $input = $('#clinicsForm').find('[name^="' + key +
                                    '"], [name$="' + key + '"]');
                            }
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                $input.next('.invalid-feedback').text(errors[key][0]);
                            }
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Errors',
                            html: messages.join('<br>')
                        });
                    } else {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                }
            });
        });

        // Edit
        function editClinic(id) {
            $.get('{{ route('admin.clinics.index') }}/' + id, function(data) {
                $('#clinicsId').val(data.id);
                $('#name').val(data.name);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#isAllowedToggle').prop('checked', data.is_allowed);
                $('#statusToggle').prop('checked', data.status);

                // show existing image preview (if provided)
                if (data.images) {
                    // data.image might be full URL or storage path — attempt to use as-is
                    $('#imagesPreview').attr('src', data.images).show();
                } else {
                    $('#imagesPreview').hide().attr('src', '');
                }

                $('#clinicsForm').attr('action',
                    '{{ route('admin.clinics.update', ':id') }}'.replace(
                        ':id', id));
                $('#clinicsModal .modal-title').text('{{ __('Edit Clinic') }}');
                $('#clinicsModal').modal('show');
            });
        }


        // Delete
        function deleteClinic(id) {
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
                        url: '{{ route('admin.clinics.index') }}/' +
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
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    });
                }
            });
        }

        function showClinic(id) {
            $.get('{{ route('admin.clinics.index') }}/' + id, function(data) {
                $('#showClinicModal').modal('show');
                $('#showName').text(data.name);
                $('#showPhone').text(data.phone);
                $('#showAddress').text(data.address);
                $('#showIsAllowed').html(
                    data.is_allowed == 1 ? "<span class='badge bg-success'>{{ __('Allowed') }}</span>" :
                    "<span class='badge bg-danger'>{{ __('Not Allowed') }}</span>"
                );
                $('#showStatus').html(
                    data.status == 1 ? "<span class='badge bg-success'>{{ __('Active') }}</span>" :
                    "<span class='badge bg-danger'>{{ __('Inactive') }}</span>"
                );
                $('#showUsers').text(data.clinicUsers);
                $('#showImages').html(data.images.map(function(image) {
                    return '<img src="' + image + '" width="100" height="100">';
                }));

            });
        }

        function changeApproval(rentalSpaceId, approvementId) {
            $('#moduleId').val(rentalSpaceId);
            $('#approvementId').val(approvementId);

            // Clear old values
            $('#action').val('');
            $('#notes').val('');

            if (approvementId && approvementId !== 'null') {
                // Fetch existing approvement data
                $.ajax({
                    url: '/admin/approvements/' + approvementId,
                    method: 'GET',
                    success: function(data) {
                        $('#action').val(data.action);
                        $('#notes').val(data.notes);
                    }
                });
            }

            $('#approvalModal').modal('show');
        }

        // Handle form submit
        $('#approvalForm').on('submit', function(e) {
            e.preventDefault();

            let moduleId = $('#moduleId').val();
            let approvementId = $('#approvementId').val();

            if (approvementId) {
                $.ajax({
                    url: '/admin/approvements/' + approvementId,
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        action: $('#action').val(),
                        notes: $('#notes').val(),
                    },
                    success: function(res) {
                        $('#approvalModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success!', res.message, 'success');
                    },
                    error: function(err) {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            } else {
                $.ajax({
                    url: '/admin/approvements',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        module_id: moduleId,
                        module_type: 'App\\Models\\Clinic',
                        action: $('#action').val(),
                        notes: $('#notes').val(),
                    },
                    success: function(res) {
                        $('#approvalModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success!', res.message, 'success');
                    },
                    error: function(err) {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });


        // change status toggle
        $(document).on('change', '.toggle-boolean-status', function(e) {
            let id = $(this).data('id');
            let field = $(this).data('field');
            let value = $(this).is(':checked') ? 1 : 0;

            let url = '{{ route('admin.clinics.update-status', ':id') }}'.replace(':id', id);

            $.ajax({
                url: url,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                    field: field,
                    value: value
                },
                success: function(response) {
                    table.ajax.reload(null, false); // reload but keep current page
                    Swal.fire('Success!', response.message, 'success');
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong', 'error');
                }
            });
        });

        // change is allowed toggle
        $(document).on('change', '.toggle-boolean-is-allowed', function(e) {
            let id = $(this).data('id');
            let field = $(this).data('field');
            let value = $(this).is(':checked') ? 1 : 0;

            let url = '{{ route('admin.clinics.update-is-allowed', ':id') }}'.replace(':id', id);

            $.ajax({
                url: url,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                    field: field,
                    value: value
                },
                success: function(response) {
                    table.ajax.reload(null, false); // reload but keep current page
                    Swal.fire('Success!', response.message, 'success');
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong', 'error');
                }
            });
        });
    </script>
@endpush
