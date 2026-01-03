@extends('backend.dashboards.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ __('Trash Supplier Products') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="supplier-products-trash-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Product Name') }}</th>
                                    <th>{{ __('Supplier') }}</th>
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
        let trashTable = $('#supplier-products-trash-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.supplier-products.trash.data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'product_name', name: 'name_en' },
                { data: 'supplier_name', name: 'supplier.name' },
                { data: 'trash_action', name: 'trash_action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            pageLength: 10,
            responsive: true,
            language: languages[language],
            buttons: [{
                extend: 'print',
                exportOptions: { columns: [0, 1, 2] }
            },
            {
                extend: 'excel',
                text: 'Excel',
                title: 'Trash Supplier Products',
                exportOptions: { columns: [0, 1, 2] }
            },
            {
                extend: 'copy',
                exportOptions: { columns: [0, 1, 2] }
            }
            ]
        });

        function restore(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to restore this product?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.supplier-products.restore", ":id") }}'.replace(':id', id),
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            trashTable.ajax.reload();
                            Swal.fire('Restored!', response.message, 'success');
                        }
                    });
                }
            });
        }

        function forceDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the product!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete permanently!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.supplier-products.force-delete", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            trashTable.ajax.reload();
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    });
                }
            });
        }
    </script>
@endpush