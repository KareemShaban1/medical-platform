<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ __('Create Subscription') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="subscription-form">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Plan') }} <span class="text-danger">*</span></label>
                        <select name="plan_id" id="plan-select" class="form-select" required>
                            <option value="">{{ __('Select Plan') }}</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" data-type="{{ $plan->plan_type }}">
                                {{ $plan->name }} ({{ ucfirst($plan->plan_type) }} - {{ ucfirst($plan->level) }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Entity Type') }} <span class="text-danger">*</span></label>
                        <select name="entity_type" id="entity-type" class="form-select" required>
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="clinic">{{ __('Clinic') }}</option>
                            <option value="doctor">{{ __('Standalone Doctor') }}</option>
                            <option value="supplier">{{ __('Supplier') }}</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">{{ __('Entity') }} <span class="text-danger">*</span></label>
                        <select name="entity_id" id="entity-select" class="form-select" required>
                            <option value="">{{ __('Select entity type first') }}</option>
                        </select>
                        <small class="text-muted">{{ __('Select entity type above to load entities') }}</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" class="form-control">
                        <small class="text-muted">{{ __('Leave empty for lifetime subscription') }}</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="expired">{{ __('Expired') }}</option>
                            <option value="canceled">{{ __('Canceled') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="auto_renew" class="form-check-input" id="auto_renew" value="1">
                            <label class="form-check-label" for="auto_renew">{{ __('Auto Renew') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Create Subscription') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#entity-type').on('change', function() {
        const type = $(this).val();
        const entitySelect = $('#entity-select');
        entitySelect.html('<option value="">{{ __('Loading...') }}</option>').prop('disabled', true);

        if (!type) {
            entitySelect.html('<option value="">{{ __('Select entity type first') }}</option>').prop('disabled', true);
            return;
        }

        $.get('{{ route('admin.subscriptions.entities') }}', { type: type }, function(data) {
            let options = '<option value="">{{ __('Select') }}</option>';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function(item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
            } else {
                options += '<option value="">{{ __('No entities found') }}</option>';
            }
            entitySelect.html(options).prop('disabled', false);
        }).fail(function() {
            entitySelect.html('<option value="">{{ __('Failed to load entities') }}</option>').prop('disabled', false);
        });
    });

    // Validate plan type matches entity type
    $('#plan-select, #entity-type').on('change', function() {
        const planType = $('#plan-select option:selected').data('type');
        const entityType = $('#entity-type').val();

        if (planType && entityType) {
            const valid = (
                (planType === 'clinic' && entityType === 'clinic') ||
                (planType === 'doctor' && entityType === 'doctor') ||
                (planType === 'supplier' && entityType === 'supplier')
            );

            if (!valid) {
                Swal.fire({
                    title: '{{ __('Warning') }}',
                    text: '{{ __('Plan type does not match entity type. Please select matching types.') }}',
                    icon: 'warning',
                    confirmButtonColor: '#079184',
                });
            }
        }
    });

    $('#subscription-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        data.auto_renew = data.auto_renew === '1' ? true : false;

        $.ajax({
            url: '{{ route('admin.subscriptions.store') }}',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(resp) {
                Swal.fire({
                    title: '{{ __('Success!') }}',
                    text: resp.message || '{{ __('Subscription created successfully') }}',
                    icon: 'success',
                    confirmButtonColor: '#079184',
                }).then(() => {
                    $('#subscription-modal').modal('hide');
                    if (typeof table !== 'undefined') {
                        table.ajax.reload(null, false);
                    } else {
                        window.location.reload();
                    }
                });
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || '{{ __('Failed to create subscription') }}';
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

