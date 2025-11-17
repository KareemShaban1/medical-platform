@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Plans Management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" onclick="createPlan()">
                        <i class="mdi mdi-plus"></i> {{ __('Add Plan') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Plans Management') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Filter by Type') }}</label>
                            <select id="plan-type-filter" class="form-select form-select-sm">
                                <option value="">{{ __('All Types') }}</option>
                                <option value="doctor">{{ __('Doctor') }}</option>
                                <option value="clinic">{{ __('Clinic') }}</option>
                                <option value="supplier">{{ __('Supplier') }}</option>
                            </select>
                        </div>
                    </div>
                    <table id="plans-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Level') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Features') }}</th>
                                <th>{{ __('Active Subscriptions') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="plan-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#plans-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.plans.data') }}',
            data: function(d) {
                d.plan_type = $('#plan-type-filter').val();
            }
        },
        order: [[0, 'desc']],
        columns: [
            { data: 'id', name: 'id' },
            { data: 'plan_type', name: 'plan_type' },
            { data: 'level', name: 'level' },
            { data: 'name', name: 'name' },
            { data: 'price', name: 'price' },
            { data: 'features_count', name: 'features_count' },
            { data: 'active_subscriptions', name: 'active_subscriptions' },
            { data: 'is_active', name: 'is_active' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

    $('#plan-type-filter').on('change', function() {
        table.ajax.reload();
    });

    window.createPlan = function() {
        $.get('{{ route('admin.plans.create') }}', function(html) {
            $('#plan-modal').html(html).modal('show');
        });
    };

    window.editPlan = function(id) {
        $.get('{{ route('admin.plans.edit', ['id' => '__ID__']) }}'.replace('__ID__', id), function(resp) {
            if (resp.success && resp.html) {
                $('#plan-modal').html(resp.html).modal('show');
            } else {
                $('#plan-modal').html(resp).modal('show');
            }
        });
    };

    window.managePlanFeatures = function(id) {
        $.get('{{ route('admin.plans.features', ['id' => '__ID__']) }}'.replace('__ID__', id), function(resp) {
            if (resp.success && resp.html) {
                $('#plan-modal').html(resp.html).modal('show');
            }
        });
    };

    window.deletePlan = function(id) {
        Swal.fire({
            title: '{{ __('Are you sure?') }}',
            text: '{{ __('This will delete the plan. Existing subscriptions will remain active.') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('Yes, delete it!') }}',
            cancelButtonText: '{{ __('Cancel') }}',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('admin.plans.destroy', ['id' => '__ID__']) }}'.replace('__ID__', id),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(resp) {
                        Swal.fire({
                            title: '{{ __('Deleted!') }}',
                            text: resp.message || '{{ __('Plan deleted successfully') }}',
                            icon: 'success',
                            confirmButtonColor: '#079184',
                        });
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.message || '{{ __('Failed to delete plan') }}';
                        Swal.fire({
                            title: '{{ __('Error') }}',
                            text: error,
                            icon: 'error',
                            confirmButtonColor: '#079184',
                        });
                    }
                });
            }
        });
    };
});
</script>
@endpush

