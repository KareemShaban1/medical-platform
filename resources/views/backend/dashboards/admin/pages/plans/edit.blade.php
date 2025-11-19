<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ __('Edit Plan') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="plan-form">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Plan Type') }} <span class="text-danger">*</span></label>
                        <select name="plan_type" class="form-select" required>
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="doctor" {{ $plan->plan_type === 'doctor' ? 'selected' : '' }}>{{ __('Doctor') }}</option>
                            <option value="clinic" {{ $plan->plan_type === 'clinic' ? 'selected' : '' }}>{{ __('Clinic') }}</option>
                            <option value="supplier" {{ $plan->plan_type === 'supplier' ? 'selected' : '' }}>{{ __('Supplier') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Level') }} <span class="text-danger">*</span></label>
                        <select name="level" class="form-select" required>
                            <option value="">{{ __('Select Level') }}</option>
                            <option value="free" {{ $plan->level === 'free' ? 'selected' : '' }}>{{ __('Free') }}</option>
                            <option value="basic" {{ $plan->level === 'basic' ? 'selected' : '' }}>{{ __('Basic') }}</option>
                            <option value="advanced" {{ $plan->level === 'advanced' ? 'selected' : '' }}>{{ __('Advanced') }}</option>
                            <option value="vip" {{ $plan->level === 'vip' ? 'selected' : '' }}>{{ __('VIP') }}</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Price') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ $plan->price }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Duration (Days)') }}</label>
                        <input type="number" min="1" name="duration_in_days" class="form-control" value="{{ $plan->duration_in_days }}" placeholder="{{ __('Leave empty for lifetime') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Description') }}</label>
                    <textarea name="description" class="form-control" rows="3">{{ $plan->description }}</textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Update Plan') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#plan-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = {};

        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        data.is_active = formData.has('is_active') ? 1 : 0;

        $.ajax({
            url: '{{ route('admin.plans.update', ['id' => $plan->id]) }}',
            method: 'PUT',
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(resp) {
                Swal.fire({
                    title: '{{ __('Success!') }}',
                    text: resp.message || '{{ __('Plan updated successfully') }}',
                    icon: 'success',
                    confirmButtonColor: '#079184',
                }).then(() => {
                    $('#plan-modal').modal('hide');
                    if (typeof table !== 'undefined') {
                        table.ajax.reload(null, false);
                    } else {
                        window.location.reload();
                    }
                });
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || '{{ __('Failed to update plan') }}';
                Swal.fire({
                    title: '{{ __('Error') }}',
                    text: error,
                    icon: 'error',
                    confirmButtonColor: '#079184',
                });
            }
        });
    });
});
</script>

