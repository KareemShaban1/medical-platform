@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">{{ __('Working Hours & Availability') }}</h4>
                <div>
                    <button class="btn btn-light me-1" id="copyWeekBtn"><i class="mdi mdi-content-copy"></i> {{ __('Copy Week') }}</button>
                    <button class="btn btn-light me-1" id="pasteWeekBtn"><i class="mdi mdi-content-paste"></i> {{ __('Paste Week') }}</button>
                    <button class="btn btn-danger me-1" id="clearWeekBtn"><i class="mdi mdi-delete"></i> {{ __('Clear Week') }}</button>
                    <button class="btn btn-primary" id="saveBtn"><i class="mdi mdi-content-save"></i> {{ __('Save Changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('Select User (Doctor/Staff)') }}</label>
            <select class="form-select select2" id="clinicUserSelect">
                @foreach($clinicUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} {{ $user->position_title ? ' - '.$user->position_title : '' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="isRecurring" checked>
                <label class="form-check-label" for="isRecurring">{{ __('Recurring weekly') }}</label>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="weekGrid">
                    <thead>
                        <tr>
                            <th style="width: 12%">{{ __('Sunday') }}</th>
                            <th style="width: 12%">{{ __('Monday') }}</th>
                            <th style="width: 12%">{{ __('Tuesday') }}</th>
                            <th style="width: 12%">{{ __('Wednesday') }}</th>
                            <th style="width: 12%">{{ __('Thursday') }}</th>
                            <th style="width: 12%">{{ __('Friday') }}</th>
                            <th style="width: 12%">{{ __('Saturday') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @for($d=0;$d<7;$d++)
                                <td>
                                    <div class="day-slots" data-day="{{ $d }}">
                                        <div class="input-group mb-1">
                                            <input type="time" class="form-control start">
                                            <span class="input-group-text">-</span>
                                            <input type="time" class="form-control end">
                                            <button class="btn btn-sm btn-outline-danger clear-slot" type="button"><i class="mdi mdi-close"></i></button>
                                        </div>
                                    </div>
                                </td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Make day columns wide enough and inputs readable */
#weekGrid th, #weekGrid td { min-width: 180px; vertical-align: top; }
#weekGrid .day-slots .input-group { gap: 4px; }
#weekGrid .day-slots .input-group input.form-control { min-width: 120px; }
#weekGrid .day-slots .input-group .input-group-text { padding: 0 .5rem; }
@media (max-width: 1400px) {
  #weekGrid th, #weekGrid td { min-width: 200px; }
  #weekGrid .day-slots .input-group input.form-control { min-width: 130px; }
}
</style>
@endpush

@push('scripts')
<script>
$('.select2').select2();

const days = [0,1,2,3,4,5,6];
let clipboardWeek = null;

function renderSlots(day, slots) {
    const container = $(`.day-slots[data-day="${day}"]`);
    const row = container.find('.input-group');
    const first = (slots && slots.length > 0) ? slots[0] : null;
    row.find('input.start').val(first ? first.start_time : '');
    row.find('input.end').val(first ? first.end_time : '');
}

function getGridData() {
    const slots = [];
    days.forEach(day => {
        const container = $(`.day-slots[data-day="${day}"]`);
        container.find('.input-group').each(function() {
            const start = $(this).find('input.start').val();
            const end = $(this).find('input.end').val();
            if (start && end) {
                slots.push({ day_of_week: day, start_time: start, end_time: end });
            }
        });
    });
    return slots;
}

function setGridData(allSlots) {
    const grouped = {0:[],1:[],2:[],3:[],4:[],5:[],6:[]};
    (allSlots || []).forEach(s => { grouped[s.day_of_week].push({start_time: s.start_time, end_time: s.end_time}); });
    days.forEach(d => renderSlots(d, grouped[d]));
}

function loadUserHours(userId) {
    $.get(`{{ route('clinic.working-hours.for-user', ':id') }}`.replace(':id', userId), function(data) {
        setGridData(data);
    });
}

$(document).ready(function() {
    // clear single slot per day
    $(document).on('click', '.clear-slot', function(){
        const row = $(this).closest('.input-group');
        row.find('input.start').val('');
        row.find('input.end').val('');
    });

    // copy/paste
    $('#copyWeekBtn').on('click', function(){ clipboardWeek = getGridData(); Swal.fire('Copied', '{{ __('Week copied to clipboard') }}', 'success');});
    $('#pasteWeekBtn').on('click', function(){ if (clipboardWeek) setGridData(clipboardWeek); });
    $('#clearWeekBtn').on('click', function(){ setGridData([]); });

    // load initial user
    const initialUser = $('#clinicUserSelect').val();
    if (initialUser) loadUserHours(initialUser);
    $('#clinicUserSelect').on('change', function(){ loadUserHours($(this).val()); });

    $('#saveBtn').on('click', function(){
        const clinic_user_id = $('#clinicUserSelect').val();
        const slots = getGridData();
        $.ajax({
            url: '{{ route('clinic.working-hours.bulk-save') }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { clinic_user_id, slots, is_recurring: $('#isRecurring').is(':checked') ? 1 : 0 },
            success: function(resp){
                Swal.fire('Success', resp.message, 'success');
            },
            error: function(xhr){
                let msg = xhr.responseJSON?.message || 'Error';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
@endpush
