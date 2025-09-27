@extends('backend.dashboards.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Product Details: {{ $product->name }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.supplier-products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Product Information -->
                            <div class="col-md-8">
                                <h5>Product Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Name (EN):</th>
                                        <td>{{ $product->name_en }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name (AR):</th>
                                        <td>{{ $product->name_ar }}</td>
                                    </tr>
                                    <tr>
                                        <th>SKU:</th>
                                        <td>{{ $product->sku }}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier:</th>
                                        <td>{{ $product->supplier->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Price Before:</th>
                                        <td>${{ number_format($product->price_before, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Price After:</th>
                                        <td>${{ number_format($product->price_after, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Stock:</th>
                                        <td>{{ $product->stock }}</td>
                                    </tr>
                                    <tr>
                                        <th>Categories:</th>
                                        <td>
                                            @if ($product->categories->count() > 0)
                                                @foreach ($product->categories as $category)
                                                    <span class="badge bg-primary me-1">{{ $category->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No categories</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Current Status:</th>
                                        <td>
                                            @if ($product->approvement)
                                                @php
                                                    $badges = [
                                                        'pending' => 'warning',
                                                        'under_review' => 'info',
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                    ];
                                                    $class = $badges[$product->approvement->action] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $class }}">
                                                    {{ ucfirst(str_replace('_', ' ', $product->approvement->action)) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">No Approval Record</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($product->approvement && $product->approvement->notes)
                                        <tr>
                                            <th>Notes:</th>
                                            <td>{{ $product->approvement->notes }}</td>
                                        </tr>
                                    @endif
                                </table>

                                <!-- Description -->
                                <h5 class="mt-4">Description</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>English:</h6>
                                        <p>{{ $product->description_en ?? 'No description' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Arabic:</h6>
                                        <p>{{ $product->description_ar ?? 'No description' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Update Form -->
                            <div class="col-md-4">
                                @if ($product->approvement)
                                    <h5>Update Approval Status</h5>
                                    <div id="statusMessage" class="alert" style="display: none;"></div>
                                    <form id="statusForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="action" class="form-label">Status</label>
                                            <select name="action" id="action" class="form-select" required>
                                                <option value="under_review"
                                                    {{ $product->approvement->action == 'under_review' ? 'selected' : '' }}>
                                                    Under Review</option>
                                                <option value="approved"
                                                    {{ $product->approvement->action == 'approved' ? 'selected' : '' }}>
                                                    Approved</option>
                                                <option value="rejected"
                                                    {{ $product->approvement->action == 'rejected' ? 'selected' : '' }}>
                                                    Rejected</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                                placeholder="Add notes (required for rejection)">{{ $product->approvement->notes }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Status
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Product Images -->
                        @if ($product->images && count($product->images) > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Product Images</h5>
                                    <div class="row">
                                        @foreach ($product->images as $image)
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <img src="{{ $image }}" class="card-img-top"
                                                        style="height: 200px; object-fit: cover;">
                                                    <div class="card-body text-center p-2">
                                                        <a href="{{ $image }}" target="_blank"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> View Full Size
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No product images uploaded.
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#statusForm').submit(function(e) {
                    e.preventDefault();

                    const action = $('#action').val();
                    const notes = $('#notes').val();
                    const messageDiv = $('#statusMessage');

                    if (action === 'rejected' && !notes.trim()) {
                        messageDiv.removeClass().addClass('alert alert-danger').html(
                            '<i class="fas fa-exclamation-circle"></i> Notes are required when rejecting a product.'
                        ).show();
                        return;
                    }

                    const submitBtn = $(this).find('button[type="submit"]');
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

                    $.ajax({
                        url: '{{ route('admin.supplier-products.update-approval-status', $product->id) }}',
                        method: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            action: action,
                            notes: notes
                        },
                        success: function(response) {
                            if (response.success) {
                                messageDiv.removeClass().addClass('alert alert-success').html(
                                        '<i class="fas fa-check-circle"></i> ' + response.message)
                                    .show();
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                messageDiv.removeClass().addClass('alert alert-danger').html(
                                    '<i class="fas fa-exclamation-circle"></i> ' + (response
                                        .message || 'Update failed')).show();
                            }
                        },
                        error: function() {
                            messageDiv.removeClass().addClass('alert alert-danger').html(
                                '<i class="fas fa-exclamation-circle"></i> Update failed. Please try again.'
                            ).show();
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).html(
                                '<i class="fas fa-save"></i> Update Status');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
