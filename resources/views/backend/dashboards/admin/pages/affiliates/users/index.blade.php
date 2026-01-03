@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Affiliate Users'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    @hasPermission('view trash affiliate users')
                    <a href="{{ route('admin.affiliates.users.trash') }}" class="btn btn-secondary">
                        <i class="mdi mdi-trash-can"></i> {{ __('View Trash') }}
                    </a>
                    @endhasPermission
                </div>
                <h4 class="page-title">{{ __('Affiliate Users') }}</h4>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Type') }}</label>
                            <select name="type" class="form-select">
                                <option value="all">{{ __('All') }}</option>
                                <option value="clinic" @selected(request('type') === 'clinic')>{{ __('Clinic Users') }}
                                </option>
                                <option value="affiliate" @selected(request('type') === 'affiliate')>
                                    {{ __('Affiliate Users') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-filter-variant"></i> {{ __('Filter') }}
                            </button>
                            <a href="{{ route('admin.affiliates.users.index') }}" class="btn btn-light">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Balance') }}</th>
                                    <th>{{ __('Total Earned') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($codes as $code)
                                @php
                                    $owner = $code->affiliateable;
                                    if (!$owner && $code->affiliateable_type === 'App\\Models\\ClinicUser') {
                                        $owner = $clinicUsersById[$code->affiliateable_id] ?? null;
                                    }
                                    if (!$owner && $code->affiliateable_type === 'App\\Models\\AffiliateUser') {
                                        $owner = $affiliateUsersById[$code->affiliateable_id] ?? null;
                                    }
                                    if (!$owner && $code->affiliateable_type === 'App\\Models\\ClinicUser') {
                                        $prefix = strtoupper(Str::before($code->code, '-'));
                                        $owner = $clinicUsersByCodePrefix[$prefix] ?? null;
                                    }
                                    $ownerType = $code->affiliateable_type === 'App\\Models\\ClinicUser' ? __('Clinic User') : __('Affiliate User');
                                    $isAffiliateUser = $code->affiliateable_type === 'App\\Models\\AffiliateUser';
                                @endphp
                                <tr>
                                    <td>{{ $owner?->name ?? __('Unknown') }}</td>
                                    <td>{{ $owner?->email ?? '-' }}</td>
                                    <td>{{ $owner?->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">{{ $ownerType }}</span>
                                    </td>
                                    <td class="fw-semibold text-primary">{{ $code->code }}</td>
                                    <td>{{ number_format($code->balance ?? 0, 2) }}</td>
                                    <td>{{ number_format($code->total_earned ?? 0, 2) }}</td>
                                    <td>
                                        @if(is_null($owner?->status))
                                            <span class="badge bg-secondary">{{ __('Unknown') }}</span>
                                        @else
                                            <span class="badge bg-{{ $owner->status ? 'success' : 'secondary' }}">
                                                {{ $owner->status ? __('Active') : __('Disabled') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $owner?->created_at?->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.affiliates.users.show', $code->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                {{ __('View') }}
                                            </a>
                                            @if($isAffiliateUser && $owner)
                                            @hasPermission('delete affiliate user')
                                            <button onclick="deleteUser({{ $owner->id }})"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            @endhasPermission
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        {{ __('No affiliate users found.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $codes->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function deleteUser(id) {
            Swal.fire({
                title: '{{ __("Delete User?") }}',
                text: '{{ __("This will soft delete the affiliate user. You can restore it later from trash.") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("Yes, delete it!") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.affiliates.users.destroy", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            Swal.fire('{{ __("Deleted!") }}', response.message, 'success').then(() => {
                                window.location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong") }}', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush