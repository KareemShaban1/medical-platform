@extends('backend.dashboards.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ __('Supplier Products') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="supplier-products-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Product Name') }}</th>
                                    <th>{{ __('Supplier') }}</th>
                                    <th>{{ __('Categories') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Stock') }}</th>
                                    <th>{{ __('Approval Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Status Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Approval Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="approvalForm">
                        @csrf
                        <input type="hidden" id="productId">

                        <div class="mb-3">
                            <label for="action" class="form-label">Status</label>
                            <select name="action" id="action" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="under_review">Under Review</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                placeholder="Add notes (required for rejection)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitApproval()">Update Status</button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#supplier-products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.supplier-products.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product_name',
                        name: 'name_en'
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier.name'
                    },
                    {
                        data: 'categories',
                        name: 'categories',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'price',
                        name: 'price_after'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'approval_status',
                        name: 'approval_status',
                        orderable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
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
                            columns: [0, 2, 3, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [0, 2, 3, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: [0, 2, 3, 5, 6, 7]
                        }
                    }
                ]
            });
        });

        function updateApprovalStatus(productId) {
            $('#productId').val(productId);
            $('#approvalModal').modal('show');
        }


        function submitApproval() {
            const productId = $('#productId').val();
            const action = $('#action').val();
            const notes = $('#notes').val();

            if (action === 'rejected' && !notes.trim()) {
                alert('Notes are required when rejecting a product.');
                return;
            }

            $.ajax({
                url: `{{ route('admin.supplier-products.update-approval-status', ':id') }}`.replace(':id', productId),
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    notes: notes
                },
                success: function(response) {
                    if (response.success) {
                        $('#approvalModal').modal('hide');
                        $('#supplier-products-table').DataTable().ajax.reload();
                        Swal.fire('Success!', response.message, 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong', 'error');
                }
            });
        }
    </script>
@endpush
@endsection
