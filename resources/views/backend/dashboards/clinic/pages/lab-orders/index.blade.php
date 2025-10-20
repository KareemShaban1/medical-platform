@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="page-title">{{ __('Lab Orders') }}</h4>
        <a href="{{ route('clinic.lab-orders.create') }}" class="btn btn-primary">{{ __('Create Lab Order') }}</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <select id="filter-status" class="form-select">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="received">{{ __('Received') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
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
                <div class="col-md-3"><input type="date" id="filter-from" class="form-control" /></div>
                <div class="col-md-3"><input type="date" id="filter-to" class="form-control" /></div>
            </div>

            <table id="lab-orders-table" class="table table-striped w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Patient') }}</th>
                        <th>{{ __('Test Name') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Doctor') }}</th>
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
function reloadTable(){
    $('#lab-orders-table').DataTable().ajax.reload();
}
$(function(){
    const table = $('#lab-orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('clinic.lab-orders.data') }}',
            data: function(d){
                d.status = $('#filter-status').val();
                d.patient_id = $('#filter-patient').val();
                d.date_from = $('#filter-from').val();
                d.date_to = $('#filter-to').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'patient', name: 'patient.name' },
            { data: 'test_name' },
            { data: 'status_badge', orderable:false, searchable:false },
            { data: 'doctor', name: 'creator.name' },
            { data: 'created_at' },
            { data: 'action', orderable:false, searchable:false },
        ]
    });
    $('#filter-status,#filter-patient,#filter-from,#filter-to').on('change', reloadTable);
});

function openUpload(id){
    const input = $('<input type="file" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" />');
    input.on('change', function(){
        const form = new FormData();
        for (const f of this.files){ form.append('results[]', f); }
        form.append('_token', '{{ csrf_token() }}');
        fetch('{{ route('clinic.lab-orders.upload', ':id') }}'.replace(':id', id), { method:'POST', body: form })
            .then(r=>location.reload());
    });
    input.trigger('click');
}
function markCompleted(id){
    fetch('{{ route('clinic.lab-orders.complete', ':id') }}'.replace(':id', id), {
        method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(()=>location.reload());
}
</script>
@endpush
@endsection

