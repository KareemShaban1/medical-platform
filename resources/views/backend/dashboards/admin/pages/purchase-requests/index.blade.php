@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Purchase Requests'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Purchase Requests') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('Purchase Requests') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="purchase-requests-table" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>{{ __('Clinic') }}</th>
                                        <th>{{ __('Categories') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Offers') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Timeline') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th>{{ __('Actions') }}</th>
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
            $('#purchase-requests-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.purchase-requests.data') }}',
                columns: [
                    { data: 'clinic_name', name: 'clinic_name' },
                    { data: 'categories', name: 'categories' },
                    { data: 'description', name: 'description' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'offers_count', name: 'offers_count' },
                    { data: 'status', name: 'status' },
                    { data: 'timeline', name: 'timeline' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush

