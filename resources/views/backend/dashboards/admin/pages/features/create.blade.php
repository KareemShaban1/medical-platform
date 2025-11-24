<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ __('Create Feature') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="feature-form">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('Code') }} <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" required placeholder="e.g. max_products">
                    <small class="text-muted">{{ __('Unique identifier for the feature') }}</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Description') }}</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Value Type') }} <span class="text-danger">*</span></label>
                    <select name="value_type" class="form-select" required>
                        <option value="boolean">{{ __('Boolean') }}</option>
                        <option value="integer">{{ __('Integer') }}</option>
                        <option value="string">{{ __('String') }}</option>
                        <option value="json">{{ __('JSON') }}</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Unit') }}</label>
                    <input type="text" name="unit" class="form-control" placeholder="e.g. products, patients">
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
                <button type="submit" class="btn btn-primary">{{ __('Create Feature') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#feature-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        data.is_active = data.is_active === '1' ? true : false;

        $.ajax({
            url: '{{ route('admin.features.store') }}',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(resp) {
                Swal.fire({
                    title: '{{ __('Success!') }}',
                    text: resp.message || '{{ __('Feature created successfully') }}',
                    icon: 'success',
                    confirmButtonColor: '#079184',
                }).then(() => {
                    $('#feature-modal').modal('hide');
                    if (typeof table !== 'undefined') {
                        table.ajax.reload(null, false);
                    } else {
                        window.location.reload();
                    }
                });
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || '{{ __('Failed to create feature') }}';
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

