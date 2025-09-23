@push('scripts')
<script>
    let table = $('#blog-categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.blog-categories.data") }}',
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name_en',
                name: 'name_en'
            },
            {
                data: 'name_ar',
                name: 'name_ar'
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
                title: 'Blog Categories Data',
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
        $('#blogCategoriesForm')[0].reset();
        $('#blogCategoriesForm').attr('action', '{{ route("admin.blog-categories.store") }}');
        $('#blogCategoriesId').val('');
        $('#blogCategoriesModal .modal-title').text('{{ __("Add Blog Category") }}');
    }

    // Handle Add/Edit Form Submission
    $('#blogCategoriesForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#blogCategoriesId').val();
        let url = id ?
            '{{ route("admin.blog-categories.update", ":id") }}'.replace(':id', id) :
            '{{ route("admin.blog-categories.store") }}';
        let method = id ? 'PUT' : 'POST';

        $('#statusHidden').val($('#statusToggle').is(':checked') ? 1 : 0);


        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#blogCategoriesModal').modal('hide');
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
                            let nameSelector =
                                '[name="' +
                                key +
                                '"]';
                            let $input =
                                $(
                                    nameSelector
                                );
                            // fallback for inputs using array syntax:
                            if (!$input
                                .length
                            ) {
                                // try ends-with matching
                                $input = $(
                                        '#blogCategoriesForm'
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
                                    .text(errors[key][0]);
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
    function editBlogCategory(id) {
        $.get('{{ route("admin.blog-categories.index") }}/' + id, function(data) {
            $('#blogCategoriesId').val(data.id);
            $('#name_en').val(data.name_en);
            $('#name_ar').val(data.name_ar);
            $('#statusToggle').prop('checked', data.status);

            $('#blogCategoriesForm').attr('action',
                '{{ route("admin.blog-categories.update", ":id") }}'.replace(
                    ':id', id));
            $('#blogCategoriesModal .modal-title').text('{{ __("Edit Blog Category") }}');
            $('#blogCategoriesModal').modal('show');
        });
    }


    // Delete
    function deleteBlogCategory(id) {
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
                    url: '{{ route("admin.blog-categories.index") }}/' +
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

    
      // change status toggle
      $(document).on('change', '.toggle-boolean', function(e) {
        let id = $(this).data('id');
        let field = $(this).data('field');
        let value = $(this).is(':checked') ? 1 : 0;

        let url = '{{ route("admin.blog-categories.update-status", ":id") }}'.replace(':id', id);

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