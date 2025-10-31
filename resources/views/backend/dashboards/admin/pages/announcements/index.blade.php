@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Announcements'))

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="page-title-box">
        <div class="page-title-right">
          <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Announcements') }}</li>
          </ol>
        </div>
        <h4 class="page-title">{{ __('Announcements') }}</h4>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-sm-4">
              <a href="{{ route('admin.announcements.create') }}" class="btn btn-danger mb-2 me-2">
                <i class="mdi mdi-plus-circle me-2"></i> {{ __('Create Announcement') }}
              </a>
            </div>
            <div class="col-sm-8 text-sm-end">
              <button type="button" class="btn btn-success mb-2" onclick="refreshTable()"><i class="mdi mdi-refresh"></i> {{ __('Refresh') }}</button>
            </div>
          </div>

          <div class="table-responsive">
            <table id="announcements-table" class="table table-centered w-100 dt-responsive nowrap">
              <thead class="table-light">
                <tr>
                  <th>{{ __('ID') }}</th>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Audience') }}</th>
                  <th>{{ __('Start') }}</th>
                  <th>{{ __('End') }}</th>
                  <th>{{ __('Status') }}</th>
                  <th style="width: 85px;">{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody></tbody>
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
function refreshTable(){ $('#announcements-table').DataTable().ajax.reload(null,false); }
function deleteAnnouncement(id){
    if(!confirm('{{ __('Are you sure?') }}')) return;
    $.ajax({
        url: "{{ route('admin.announcements.destroy', ':id') }}".replace(':id', id),
        type: 'POST',
        data: { _method:'DELETE', _token: '{{ csrf_token() }}' },
        success: function(res){
            if(res.success){ refreshTable(); Swal.fire('{{ __('Success!') }}', res.message, 'success'); }
            else { Swal.fire('{{ __('Error!') }}', res.message || 'Error', 'error'); }
        },
        error: function(xhr){ Swal.fire('{{ __('Error!') }}', xhr.responseJSON?.message || 'Error', 'error'); }
    });
}

$(function(){
  $('#announcements-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.announcements.data') }}",
    columns: [
      {data:'id', name:'id'},
      {data:'title', name:'title'},
      {data:'audience', name:'audience', orderable:false, searchable:false},
      {data:'start_at', name:'start_at'},
      {data:'end_at', name:'end_at'},
      {data:'status', name:'status', orderable:false, searchable:false},
      {data:'action', name:'action', orderable:false, searchable:false},
    ],
    order: [[0,'desc']],
    language: { paginate: { previous: "<i class='mdi mdi-chevron-left'>", next: "<i class='mdi mdi-chevron-right'>" } },
    drawCallback: function () { $('.dataTables_paginate > .pagination').addClass('pagination-rounded'); }
  });
});
</script>
@endpush
