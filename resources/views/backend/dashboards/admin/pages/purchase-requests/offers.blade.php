@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Request Offers'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.purchase-requests.index') }}">{{ __('Purchase Requests') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Offers') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('Offers for Request') }} #{{ $request->id }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Request Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>{{ __('Clinic') }}:</strong></td>
                                <td>{{ $request->clinic->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Categories') }}:</strong></td>
                                <td>{{ $request->categories->pluck('name')->join(', ') ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Quantity') }}:</strong></td>
                                <td>{{ $request->quantity }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Timeline') }}:</strong></td>
                                <td>{{ $request->timeline ? $request->timeline->format('Y-m-d') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Status') }}:</strong></td>
                                <td>
                                    @php
                                        $badges = ['open' => 'success', 'closed' => 'primary', 'canceled' => 'danger'];
                                        $class = $badges[$request->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $class }}">{{ ucfirst($request->status) }}</span>
                                </td>
                            </tr>
                        </table>
                        <div>
                            <strong>{{ __('Description') }}:</strong>
                            <p class="mb-0">{{ $request->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Offers') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="offers-table" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>{{ __('Supplier') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Discount') }}</th>
                                        <th>{{ __('Final Price') }}</th>
                                        <th>{{ __('Delivery Time') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#offers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.purchase-requests.offers.data', $request->id) }}',
                columns: [
                    { data: 'supplier_name', name: 'supplier_name' },
                    { data: 'price', name: 'price' },
                    { data: 'discount', name: 'discount' },
                    { data: 'final_price', name: 'final_price' },
                    { data: 'delivery_time', name: 'delivery_time' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' }
                ]
            });
        });
    </script>
@endpush

