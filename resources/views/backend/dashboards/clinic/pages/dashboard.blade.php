@extends('backend.dashboards.clinic.layouts.app')


@section('content')
<div class="container-fluid">
  @if(isset($announcement) && $announcement)
  <div class="alert alert-info d-flex justify-content-between align-items-start" role="alert" id="dashboard-announcement" data-id="{{ $announcement->id }}">
    <div>
      <div class="fw-bold">{{ $announcement->title }}</div>
      @if($announcement->body)
      <div class="mt-1">{!! nl2br(e($announcement->body)) !!}</div>
      @endif
      @if($announcement->link_url)
      <div class="mt-2"><a href="{{ $announcement->link_url }}" target="_blank" class="btn btn-sm btn-primary">{{ __('Open Link') }}</a></div>
      @endif
    </div>
    <button type="button" class="btn-close" aria-label="Close" id="dismiss-announcement"></button>
  </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
$(function(){
  $('#dismiss-announcement').on('click', function(){
    var id = $('#dashboard-announcement').data('id');
    $.ajax({
      url: "{{ route('clinic.announcements.dismiss', ':id') }}".replace(':id', id),
      type: 'POST',
      data: { _token: '{{ csrf_token() }}' },
      complete: function(){ $('#dashboard-announcement').remove(); }
    });
  });
});
</script>
@endpush
