@extends('backend.dashboards.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Suppliers
                        </a>
                    </div>
                    <h4 class="page-title">{{ $supplier->name }} - Products</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if ($products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Product Name</th>
                                            <th>Categories</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td>{{ $product->id }}</td>
                                                <td>
                                                    @if ($product->first_image)
                                                        <img src="{{ $product->first_image }}" alt="Product"
                                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                    @else
                                                        <span class="badge bg-secondary">No Image</span>
                                                    @endif
                                                </td>
                                                <td>{{ $product->name }}</td>
                                                <td>
                                                    @if ($product->categories->count() > 0)
                                                        @foreach ($product->categories->take(2) as $category)
                                                            <span class="badge bg-primary me-1">{{ $category->name }}</span>
                                                        @endforeach
                                                        @if ($product->categories->count() > 2)
                                                            <span
                                                                class="badge bg-info">+{{ $product->categories->count() - 2 }}</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">No Categories</span>
                                                    @endif
                                                </td>
                                                <td>${{ number_format($product->price_after ?? $product->price_before, 2) }}
                                                </td>
                                                <td>
                                                    @php
                                                        $stockClass =
                                                            $product->stock > 10
                                                                ? 'success'
                                                                : ($product->stock > 0
                                                                    ? 'warning'
                                                                    : 'danger');
                                                    @endphp
                                                    <span class="badge bg-{{ $stockClass }}">{{ $product->stock }}</span>
                                                </td>
                                                <td>
                                                    @if ($product->approvement)
                                                        @php
                                                            $badges = [
                                                                'pending' => 'warning',
                                                                'under_review' => 'info',
                                                                'approved' => 'success',
                                                                'rejected' => 'danger',
                                                            ];
                                                            $class =
                                                                $badges[$product->approvement->action] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $class }}">
                                                            {{ ucfirst(str_replace('_', ' ', $product->approvement->action)) }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">No Record</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('admin.supplier-products.show', $product->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <button onclick="updateApprovalStatus({{ $product->id }})"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Products Found</h5>
                                <p class="text-muted">This supplier hasn't added any products yet.</p>
                            </div>
                        @endif
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
                                <option value="under_review" {{ $product->approvement->action == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="approved" {{ $product->approvement->action == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $product->approvement->action == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                placeholder="Add notes (required for rejection)">{{ $product->approvement->notes }}</textarea>
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
                    url: `{{ route('admin.supplier-products.update-approval-status' , ':id') }}`.replace(':id', productId),
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action,
                        notes: notes
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#approvalModal').modal('hide');
                            location.reload();
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
