@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="page-title">{{ __('Invoices') }}</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <select id="filter-doctor" class="form-select">
                        <option value="">{{ __('All Doctors') }}</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filter-patient" class="form-select">
                        <option value="">{{ __('All Patients') }}</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input type="date" id="filter-from" class="form-control" /></div>
                <div class="col-md-2"><input type="date" id="filter-to" class="form-control" /></div>
                <div class="col-md-2">
                    <select id="filter-status" class="form-select">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="unpaid">{{ __('Unpaid') }}</option>
                        <option value="paid">{{ __('Paid') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                </div>
            </div>

            <table id="invoices-table" class="table table-striped w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Patient') }}</th>
                        <th>{{ __('Doctor') }}</th>
                        <th>{{ __('Subtotal') }}</th>
                        <th>{{ __('Discount') }}</th>
                        <th>{{ __('Tax') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function reloadInvoices(){ $('#invoices-table').DataTable().ajax.reload(); }
$(function(){
    const table = $('#invoices-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('clinic.invoices.data') }}',
            data: function(d){
                d.doctor_profile_id = $('#filter-doctor').val();
                d.patient_id = $('#filter-patient').val();
                d.start_date = $('#filter-from').val();
                d.end_date = $('#filter-to').val();
                d.status = $('#filter-status').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'patient_name', name: 'patient.user.name' },
            { data: 'doctor_name', name: 'doctorProfile.name' },
            { data: 'subtotal' },
            { data: 'discount' },
            { data: 'tax' },
            { data: 'total_fmt', orderData: [6] },
            { data: 'status_badge', orderable:false, searchable:false },
            { data: 'created_at' },
            { data: 'action', orderable:false, searchable:false },
        ]
    });
    $('#filter-doctor,#filter-patient,#filter-from,#filter-to,#filter-status').on('change', reloadInvoices);
});
</script>
@endpush
@endsection

