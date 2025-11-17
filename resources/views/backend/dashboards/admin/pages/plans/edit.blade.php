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
                <hr>
                <h6 class="mb-3">{{ __('Plan Features') }}</h6>
                <div id="features-container" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                    @php
                        $planFeatureMap = $plan->planFeatures->keyBy('feature_id');
                    @endphp
                    @foreach($features as $feature)
                    @php
                        $planFeature = $planFeatureMap->get($feature->id);
                        $isEnabled = $planFeature && $planFeature->is_enabled;
                        $isLimited = $planFeature && $planFeature->is_limited;
                    @endphp
                    <div class="mb-3 p-3 border rounded feature-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $feature->name }}</h6>
                                <small class="text-muted">{{ $feature->code }} - {{ $feature->value_type }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="features[{{ $feature->id }}][enabled]"
                                    class="form-check-input feature-toggle"
                                    data-feature-id="{{ $feature->id }}"
                                    id="feature_{{$feature->id}}"
                                    {{ $isEnabled ? 'checked' : '' }}>
                                <input type="hidden" name="features[{{ $feature->id }}][feature_id]" value="{{ $feature->id }}">
                                <input type="hidden" name="features[{{ $feature->id }}][is_enabled]" value="{{ $isEnabled ? 1 : 0 }}" class="feature-enabled-input">
                            </div>
                        </div>
                        <div class="feature-options mt-2" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="features[{{ $feature->id }}][is_limited]"
                                            class="form-check-input limit-toggle"
                                            id="limit_{{$feature->id}}"
                                            {{ $isLimited ? 'checked' : '' }}>
                                        <label class="form-check-label" for="limit_{{$feature->id}}">{{ __('Limited') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6 limit-value" style="display: {{ $isLimited ? 'block' : 'none' }};">
                                    <input type="text" name="features[{{ $feature->id }}][value]"
                                        class="form-control form-control-sm"
                                        value="{{ $planFeature ? $planFeature->value : '' }}"
                                        placeholder="{{ __('Limit value') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
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
    // Toggle feature options when enabled
    $(document).on('change', '.feature-toggle', function() {
        const featureId = $(this).data('feature-id');
        const isEnabled = $(this).is(':checked');
        const optionsDiv = $(this).closest('.feature-item').find('.feature-options');
        const enabledInput = $(this).closest('.feature-item').find('.feature-enabled-input');

        enabledInput.val(isEnabled ? 1 : 0);
        optionsDiv.toggle(isEnabled);
    });

    // Toggle limit value input
    $(document).on('change', '.limit-toggle', function() {
        const limitValue = $(this).closest('.feature-options').find('.limit-value');
        limitValue.toggle($(this).is(':checked'));
    });

    // Handle form submission
    $('#plan-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = {};

        for (let [key, value] of formData.entries()) {
            if (key.includes('[')) {
                const match = key.match(/^(\w+)\[(\d+)\]\[(\w+)\]$/);
                if (match) {
                    const [, prefix, id, field] = match;
                    if (!data[prefix]) data[prefix] = {};
                    if (!data[prefix][id]) data[prefix][id] = {};
                    data[prefix][id][field] = value;
                }
            } else {
                data[key] = value;
            }
        }

        const features = [];
        if (data.features) {
            Object.keys(data.features).forEach(id => {
                const feature = data.features[id];
                if (feature.enabled === '1' || feature.enabled === true) {
                    features.push({
                        feature_id: parseInt(id),
                        is_enabled: true,
                        value: feature.value || null,
                        is_limited: feature.is_limited === 'on' || feature.is_limited === true
                    });
                }
            });
        }
        data.features = features;

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

