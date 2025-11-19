<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ __('Create Plan') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="plan-form">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Plan Type') }} <span class="text-danger">*</span></label>
                        <select name="plan_type" class="form-select" required>
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="doctor">{{ __('Doctor') }}</option>
                            <option value="clinic">{{ __('Clinic') }}</option>
                            <option value="supplier">{{ __('Supplier') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Level') }} <span class="text-danger">*</span></label>
                        <select name="level" class="form-select" required>
                            <option value="">{{ __('Select Level') }}</option>
                            <option value="free">{{ __('Free') }}</option>
                            <option value="basic">{{ __('Basic') }}</option>
                            <option value="advanced">{{ __('Advanced') }}</option>
                            <option value="vip">{{ __('VIP') }}</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Price') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Duration (Days)') }}</label>
                        <input type="number" min="1" name="duration_in_days" class="form-control" placeholder="{{ __('Leave empty for lifetime') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Description') }}</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Create Plan') }}</button>
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
            url: '{{ route('admin.plans.store') }}',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(resp) {
                Swal.fire({
                    title: '{{ __('Success!') }}',
                    text: resp.message || '{{ __('Plan created successfully') }}',
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
                const error = xhr.responseJSON?.message || '{{ __('Failed to create plan') }}';
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

