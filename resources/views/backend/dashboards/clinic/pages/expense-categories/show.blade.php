@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right d-flex gap-2">
                    <a href="{{ route('clinic.expense-categories.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Categories') }}
                    </a>
                    <a href="{{ route('clinic.expense-categories.edit', $expenseCategory->id) }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil"></i> {{ __('Edit') }}
                    </a>
                    <button class="btn btn-danger" onclick="deleteCategory({{ $expenseCategory->id }})">
                        <i class="mdi mdi-trash-can"></i> {{ __('Delete') }}
                    </button>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-file-tree text-primary"></i>
                    {{ __('Expense Category') }}: {{ $expenseCategory->name }}
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="header-title mb-0">{{ __('Category Details') }}</h4>
                        <span class="badge {{ $expenseCategory->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $expenseCategory->status ? __('Active') : __('Inactive') }}
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded bg-light">
                                <small class="text-muted d-block">{{ __('Name') }}</small>
                                <div class="fs-5 fw-semibold">{{ $expenseCategory->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded bg-light">
                                <small class="text-muted d-block">{{ __('Status') }}</small>
                                <div class="fs-5 fw-semibold">
                                    {{ $expenseCategory->status ? __('Enabled') : __('Disabled') }}
                                </div>
                            </div>
                        </div>

                        @if(!empty($expenseCategory->notes))
                        <div class="col-12">
                            <div class="p-3 rounded bg-light">
                                <small class="text-muted d-block">{{ __('Notes') }}</small>
                                <div>{{ $expenseCategory->notes }}</div>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <div class="p-3 rounded bg-light">
                                <small class="text-muted d-block">{{ __('Created At') }}</small>
                                <div>{{ optional($expenseCategory->created_at)->format('M d, Y - h:i A') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded bg-light">
                                <small class="text-muted d-block">{{ __('Last Updated') }}</small>
                                <div>{{ optional($expenseCategory->updated_at)->format('M d, Y - h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Shortcuts -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">{{ __('Quick Actions') }}</h4>

                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-semibold">{{ __('Status') }}</div>
                            <small class="text-muted">{{ __('Toggle availability for this category') }}</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input type="checkbox" class="form-check-input" id="toggle-status"
                                   {{ $expenseCategory->status ? 'checked' : '' }}
                                   onchange="toggleStatus({{ $expenseCategory->id }}, this.checked)">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('clinic.expenses.index') }}?category_id={{ $expenseCategory->id }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-receipt"></i> {{ __('View Expenses in this Category') }}
                        </a>
                        <a href="{{ route('clinic.expense-categories.edit', $expenseCategory->id) }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-pencil"></i> {{ __('Edit Category') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">{{ __('Tips') }}</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="mdi mdi-lightbulb-on-outline text-warning"></i> {{ __('Use clear, consistent names for categories to improve reporting.') }}</li>
                        <li class="mb-2"><i class="mdi mdi-lightbulb-on-outline text-warning"></i> {{ __('Disable categories you no longer use instead of deleting.') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(id, checked) {
    $.ajax({
        url: '{{ route('clinic.expense-categories.update-status', ':id') }}'.replace(':id', id),
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: {
            id: id,
            field: 'status',
            value: checked ? 1 : 0
        },
        success: function(resp) {
            if (resp && resp.message) {
                Swal.fire('Success', resp.message, 'success');
            } else {
                Swal.fire('Success', '{{ __('Status updated') }}', 'success');
            }
        },
        error: function() {
            $('#toggle-status').prop('checked', !checked);
            Swal.fire('Error', '{{ __('Something went wrong') }}', 'error');
        }
    });
}

function deleteCategory(id) {
    Swal.fire({
        title: '{{ __('Are you sure?') }}',
        text: '{{ __('This action cannot be undone') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('Yes, delete it!') }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('clinic.expense-categories.destroy', ':id') }}'.replace(':id', id),
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(resp) {
                    Swal.fire('Deleted', resp.message || '{{ __('Category deleted') }}', 'success');
                    setTimeout(() => window.location.href = '{{ route('clinic.expense-categories.index') }}', 800);
                },
                error: function() {
                    Swal.fire('Error', '{{ __('Something went wrong') }}', 'error');
                }
            });
        }
    });
}
</script>
@endpush
