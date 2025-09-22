@push('scripts')
<script>
    let table = $('#rental-space-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.rental-spaces.data") }}',
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'location',
                name: 'location'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'approval',
                name: 'approval'
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
                title: 'Rental Spaces Data',
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

    // Delete
    function deleteRentalSpace(id) {
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
                    url: '{{ route("clinic.rental-spaces.destroy", ":id") }}'.replace(':id', id),
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
                    module_type: 'App\\Models\\RentalSpace',
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
    $(document).on('change', '.toggle-boolean', function(e) {
        let id = $(this).data('id');
        let field = $(this).data('field');
        let value = $(this).is(':checked') ? 1 : 0;

        let url = '{{ route("admin.rental-spaces.update-status", ":id") }}'.replace(':id', id);

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