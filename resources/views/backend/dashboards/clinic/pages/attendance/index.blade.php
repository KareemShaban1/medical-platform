@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">{{ __('Attendance') }}</h4>
                <div>
                    <button class="btn btn-success me-1" id="btnCheckIn"><i class="mdi mdi-login-variant"></i> {{ __('Check In') }}</button>
                    <button class="btn btn-warning me-1" id="btnCheckOut"><i class="mdi mdi-logout-variant"></i> {{ __('Check Out') }}</button>
                    <button class="btn btn-outline-danger" id="btnAbsence"><i class="mdi mdi-calendar-remove"></i> {{ __('Request Absence') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Today Logs') }} ({{ $today }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('Time') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->at->format('H:i') }}</td>
                                        <td>{{ $log->clinicUser->name }}</td>
                                        <td><span class="badge {{ $log->check_type === 'check_in' ? 'bg-success' : ($log->check_type === 'check_out' ? 'bg-warning' : 'bg-danger') }}">{{ ucfirst(str_replace('_',' ',$log->check_type)) }}</span></td>
                                        <td>
                                            @if($log->check_type === 'absence_request')
                                                @if($log->approved_at)
                                                    <span class="badge bg-success">{{ __('Approved') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Pending') }}</span>
                                                @endif
                                                @if(($log->media_count ?? 0) > 0)
                                                    <button class="btn btn-link btn-sm p-0 ms-2" onclick="viewAttachments({{ $log->id }})">{{ __('View Attachments') }} ({{ $log->media_count }})</button>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->notes }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted">{{ __('No logs') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Pending Absence Requests') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('Date/Time') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                    <th>{{ __('Attachments') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pending as $p)
                                    <tr>
                                        <td>{{ $p->at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $p->clinicUser->name }}</td>
                                        <td>{{ $p->notes }}</td>
                                        <td>
                                            @if(($p->media_count ?? 0) > 0)
                                                <button class="btn btn-outline-info btn-sm" onclick="viewAttachments({{ $p->id }})">{{ __('View') }} ({{ $p->media_count }})</button>
                                            @else
                                                <span class="text-muted">{{ __('None') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="approve({{ $p->id }})">{{ __('Approve') }}</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted">{{ __('No pending requests') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Hours Calculator') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('User') }}</label>
                            <select id="hcUser" class="form-select">
                                @foreach($clinicUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" id="hcStart" class="form-control" value="{{ now()->startOfMonth()->toDateString() }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('End Date') }}</label>
                            <input type="date" id="hcEnd" class="form-control" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" id="btnCompute">{{ __('Compute') }}</button>
                        </div>
                    </div>

                    <div id="hcResult" class="mt-3" style="display:none;">
                        <h6>{{ __('Total Payable Hours') }}: <span id="hcHours">0:00</span></h6>
                        <div id="hcAnomalies"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#btnCheckIn').on('click', function(){
    $.post('{{ route('clinic.attendance.check-in') }}', { _token: $('meta[name="csrf-token"]').attr('content') }, function(resp){
        Swal.fire('Success', resp.message, 'success').then(() => location.reload());
    }).fail(function(xhr){
        Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
    });
});
$('#btnCheckOut').on('click', function(){
    $.post('{{ route('clinic.attendance.check-out') }}', { _token: $('meta[name="csrf-token"]').attr('content') }, function(resp){
        Swal.fire('Success', resp.message, 'success').then(() => location.reload());
    }).fail(function(xhr){
        Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
    });
});
$('#btnAbsence').on('click', function(){
    Swal.fire({
        title: '{{ __('Request Absence') }}',
        html: '<input id="absAt" type="datetime-local" class="form-control mb-2">' +
              '<textarea id="absNotes" class="form-control mb-2" placeholder="{{ __('Notes (optional)') }}"></textarea>' +
              '<input id="absFiles" type="file" multiple class="form-control" />',
        showCancelButton: true,
        confirmButtonText: '{{ __('Submit') }}'
    }).then((res)=>{
        if(res.isConfirmed) {
            const fd = new FormData();
            fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
            fd.append('at', $('#absAt').val());
            fd.append('notes', $('#absNotes').val());
            const files = $('#absFiles')[0].files;
            for (let i = 0; i < files.length; i++) {
                fd.append('attachments[]', files[i]);
            }
            $.ajax({
                url: '{{ route('clinic.attendance.absence') }}',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function(resp){
                    Swal.fire('Success', resp.message, 'success').then(() => location.reload());
                },
                error: function(xhr){
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
                }
            });
        }
    });
});

function approve(id){
    $.post('{{ route('clinic.attendance.approve', ':id') }}'.replace(':id', id), {
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function(resp){
        Swal.fire('Success', resp.message, 'success').then(() => location.reload());
    }).fail(function(xhr){
        Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
    });
}

function viewAttachments(id){
    $.get('{{ route('clinic.attendance.attachments', ':id') }}'.replace(':id', id), function(resp){
        if (resp.status !== 'success') {
            Swal.fire('Error', resp.message || 'Error', 'error');
            return;
        }
        const items = resp.data || [];
        if (!items.length) {
            Swal.fire('{{ __('Attachments') }}', '<span class="text-muted">{{ __('No attachments') }}</span>', 'info');
            return;
        }
        let html = '<div class="list-group">';
        items.forEach(function(it){
            const isImage = (it.mime || '').startsWith('image/');
            const thumb = isImage ? `<img src="${it.preview_url}" class="me-2" style="height:40px;width:40px;object-fit:cover;border-radius:4px;"/>` : '<i class="mdi mdi-file me-2"></i>';
            html += `<a class="list-group-item list-group-item-action d-flex align-items-center" href="${it.url}" target="_blank">${thumb}<span>${it.name}</span></a>`;
        });
        html += '</div>';
        Swal.fire({
            title: '{{ __('Attachments') }}',
            html: html,
            width: 600,
            showConfirmButton: true
        });
    }).fail(function(xhr){
        Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
    });
}

$('#btnCompute').on('click', function(){
    const params = {
        clinic_user_id: $('#hcUser').val(),
        start: $('#hcStart').val(),
        end: $('#hcEnd').val()
    };
    $.get('{{ route('clinic.attendance.compute') }}', params, function(resp){
        let totalSeconds = parseInt(resp.data.totalSeconds, 10);
        if (isNaN(totalSeconds)) totalSeconds = 0;
        if (totalSeconds < 0) totalSeconds = 0; // guard
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        const fmt = (n) => (n < 10 ? '0' + n : '' + n);
        $('#hcHours').text(fmt(hours) + ':' + fmt(minutes) + ':' + fmt(seconds));
        const anomalies = resp.data.anomalies || [];
        if (anomalies.length) {
            let html = '<ul class="mb-0">';
            anomalies.forEach(a => {
                html += `<li>${a.type} @ ${a.at}</li>`;
            });
            html += '</ul>';
            $('#hcAnomalies').html(html);
        } else {
            $('#hcAnomalies').html('<span class="text-muted">{{ __('No anomalies') }}</span>');
        }
        $('#hcResult').show();
    }).fail(function(xhr){
        Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
    });
});
</script>
@endpush
