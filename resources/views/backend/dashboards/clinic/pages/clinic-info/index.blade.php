@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Clinic Info'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('clinic.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Clinic Info') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Clinic Info') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="clinic-info-form">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $clinic->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">{{ __('Phone') }} <span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $clinic->phone) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('Address') }} <span class="text-danger">*</span></label>
                            <textarea id="address" name="address" rows="3" class="form-control" required>{{ old('address', $clinic->address) }}</textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('#clinic-info-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        $.ajax({
            url: "{{ route('clinic.settings.clinic-info.update') }}",
            type: 'POST',
            data: $form.serialize(),
            success: function (res) {
                if (res.success) {
                    Swal.fire('{{ __("Success!") }}', res.message, 'success');
                } else {
                    Swal.fire('{{ __("Error!") }}', res.message || '{{ __("Something went wrong") }}', 'error');
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = Object.values(errors).flat().join('\n');
                    Swal.fire('{{ __("Validation Error!") }}', errorMessage, 'error');
                } else {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
                }
            }
        });
    });
});
</script>
@endpush

