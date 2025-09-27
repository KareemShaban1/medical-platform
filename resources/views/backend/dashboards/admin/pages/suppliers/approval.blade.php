@extends('backend.dashboards.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Supplier Approval: {{ $supplier->name }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Suppliers
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Supplier Information -->
                            <div class="col-md-6">
                                <h5>Supplier Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Name:</th>
                                        <td>{{ $supplier->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ $supplier->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address:</th>
                                        <td>{{ $supplier->address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Current Status:</th>
                                        <td>
                                            @if ($supplier->approvement)
                                                @php
                                                    $badges = [
                                                        'pending' => 'warning',
                                                        'under_review' => 'info',
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                    ];
                                                    $class = $badges[$supplier->approvement->action] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $class }}">
                                                    {{ ucfirst(str_replace('_', ' ', $supplier->approvement->action)) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">No Approval Record</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($supplier->approvement && $supplier->approvement->notes)
                                        <tr>
                                            <th>Notes:</th>
                                            <td>{{ $supplier->approvement->notes }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- Current Documents -->
                        @if ($currentDocuments->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Current Documents ({{ $currentDocuments->count() }})</h5>
                                    <div class="row">
                                        @foreach ($currentDocuments as $doc)
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        @if (in_array($doc->mime_type, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']))
                                                            <img src="{{ $doc->getUrl() }}" class="img-fluid mb-2"
                                                                style="max-height: 150px;">
                                                        @else
                                                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                                        @endif
                                                        <p class="small mb-1">{{ $doc->name }}</p>
                                                        <a href="{{ $doc->getUrl() }}" target="_blank"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> View
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
                                        <i class="fas fa-info-circle"></i> No documents uploaded yet.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Previously Rejected Documents -->
                        @if ($rejectedDocuments->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Previously Rejected Documents ({{ $rejectedDocuments->count() }})</h5>
                                    <div class="row">
                                        @foreach ($rejectedDocuments as $doc)
                                            <div class="col-md-3 mb-3">
                                                <div class="card border-danger">
                                                    <div class="card-body text-center">
                                                        @if (in_array($doc->mime_type, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']))
                                                            <img src="{{ $doc->getUrl() }}" class="img-fluid mb-2"
                                                                style="max-height: 150px;">
                                                        @else
                                                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                                        @endif
                                                        <p class="small mb-1 text-danger">{{ $doc->name }}</p>
                                                        <a href="{{ $doc->getUrl() }}" target="_blank"
                                                            class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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

                    // Require notes for rejection
                    if (action === 'rejected' && !notes.trim()) {
                        messageDiv.removeClass().addClass('alert alert-danger').html(
                            '<i class="fas fa-exclamation-circle"></i> Notes are required when rejecting an application.'
                            ).show();
                        return;
                    }

                    const submitBtn = $(this).find('button[type="submit"]');
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                });
            });
        </script>
    @endpush
@endsection
