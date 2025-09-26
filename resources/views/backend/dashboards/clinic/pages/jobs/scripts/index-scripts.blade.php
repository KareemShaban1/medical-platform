@push('scripts')
<script>
       let table = $('#job-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("clinic.jobs.data") }}',
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'main_image',
                name: 'main_image'
            },
            {
                data: 'title',
                name: 'title'
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
                    columns: [0, 1, 2]
                }
            },
            {
                extend: 'excel',
                text: 'Excel',
                title: 'Courses Data',
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

      // change status toggle
      $(document).on('change', '.toggle-boolean', function(e) {
        let id = $(this).data('id');
        let field = $(this).data('field');
        let value = $(this).is(':checked') ? 1 : 0;

        let url = '{{ route("clinic.jobs.update-status", ":id") }}'.replace(':id', id);

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

        // Delete Job
        function deleteJob(id) {
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
                    url: '{{ route("clinic.jobs.destroy", ":id") }}'.replace(':id', id),
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


    
</script>
@endpush