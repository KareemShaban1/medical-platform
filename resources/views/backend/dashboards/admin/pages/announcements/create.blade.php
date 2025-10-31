@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Create Announcement'))

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="page-title-box">
        <div class="page-title-right">
          <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.announcements.index') }}">{{ __('Announcements') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create') }}</li>
          </ol>
        </div>
        <h4 class="page-title">{{ __('Create Announcement') }}</h4>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.announcements.store') }}">
    @csrf
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Message') }}</label>
              <textarea name="body" class="form-control" rows="4"></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('Start At') }}</label>
                <input type="datetime-local" name="start_at" class="form-control">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('End At') }}</label>
                <input type="datetime-local" name="end_at" class="form-control">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Link URL') }}</label>
              <input type="url" name="link_url" class="form-control" placeholder="https://...">
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="active" name="status" value="1" checked>
              <label class="form-check-label" for="active">{{ __('Active') }}</label>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="mb-3">{{ __('Audience') }}</h5>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="target_clinics_all" name="target_clinics_all" value="1">
                <label class="form-check-label" for="target_clinics_all">{{ __('All Clinics') }}</label>
              </div>
              <label class="form-label mt-2">{{ __('Specific Clinics') }}</label>
            <select name="clinic_ids[]" class="form-control select2" multiple size="8">
              @foreach($clinics as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
              @endforeach
            </select>
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="target_suppliers_all" name="target_suppliers_all" value="1">
                <label class="form-check-label" for="target_suppliers_all">{{ __('All Suppliers') }}</label>
              </div>
              <label class="form-label mt-2">{{ __('Specific Suppliers') }}</label>
            <select name="supplier_ids[]" class="form-control select2" multiple size="8">
              @foreach($suppliers as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
              @endforeach
            </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container { width: 100% !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
  $('select[name="clinic_ids[]"], select[name="supplier_ids[]"]').select2({ width: '100%' });
});
</script>
@endpush
