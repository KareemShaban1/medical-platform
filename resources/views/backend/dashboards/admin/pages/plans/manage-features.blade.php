<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ __('Manage Plan Features') }} - {{ $plan->name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>{{ __('Plan Information') }}</h6>
                    <p class="mb-1"><strong>{{ __('Type') }}:</strong> {{ ucfirst($plan->plan_type) }}</p>
                    <p class="mb-1"><strong>{{ __('Level') }}:</strong> {{ ucfirst($plan->level) }}</p>
                    <p class="mb-1"><strong>{{ __('Price') }}:</strong> ${{ number_format($plan->price, 2) }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary btn-sm" onclick="addFeatureToPlan({{ $plan->id }})">
                        <i class="mdi mdi-plus"></i> {{ __('Add Feature') }}
                    </button>
                </div>
            </div>

            <hr>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Feature') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Enabled') }}</th>
                            <th>{{ __('Limited') }}</th>
                            <th>{{ __('Value/Limit') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="plan-features-list">
                        @foreach($allFeatures as $feature)
                        @php
                            $planFeature = $planFeatures->get($feature->id);
                        @endphp
                        @if($planFeature)
                        <tr data-feature-id="{{ $feature->id }}">
                            <td>{{ $feature->name }}</td>
                            <td><code>{{ $feature->code }}</code></td>
                            <td>{{ $feature->value_type }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input feature-enabled"
                                        {{ $planFeature->is_enabled ? 'checked' : '' }}
                                        onchange="updatePlanFeature({{ $plan->id }}, {{ $feature->id }}, 'is_enabled', this.checked)">
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input feature-limited"
                                        {{ $planFeature->is_limited ? 'checked' : '' }}
                                        onchange="toggleLimited({{ $feature->id }}, this.checked); updatePlanFeature({{ $plan->id }}, {{ $feature->id }}, 'is_limited', this.checked)">
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm feature-value"
                                    value="{{ $planFeature->value }}"
                                    data-feature-id="{{ $feature->id }}"
                                    {{ !$planFeature->is_limited ? 'disabled' : '' }}
                                    onchange="updatePlanFeature({{ $plan->id }}, {{ $feature->id }}, 'value', this.value)"
                                    placeholder="{{ __('Limit value') }}">
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" onclick="deletePlanFeature({{ $plan->id }}, {{ $feature->id }})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="available-features" class="mt-4" style="display: none;">
                <h6>{{ __('Available Features to Add') }}</h6>
                <select id="new-feature-select" class="form-select">
                    <option value="">{{ __('Select feature to add') }}</option>
                    @foreach($allFeatures as $feature)
                    @if(!$planFeatures->has($feature->id))
                    <option value="{{ $feature->id }}" data-code="{{ $feature->code }}" data-type="{{ $feature->value_type }}">
                        {{ $feature->name }} ({{ $feature->code }})
                    </option>
                    @endif
                    @endforeach
                </select>
                <button class="btn btn-success btn-sm mt-2" onclick="addSelectedFeature({{ $plan->id }})">
                    <i class="mdi mdi-plus"></i> {{ __('Add Selected Feature') }}
                </button>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        </div>
    </div>
</div>

<script>
function toggleLimited(featureId, isLimited) {
    const valueInput = $(`.feature-value[data-feature-id="${featureId}"]`);
    valueInput.prop('disabled', !isLimited);
}

function updatePlanFeature(planId, featureId, field, value) {
    // Convert boolean values properly
    let processedValue = value;
    if (field === 'is_enabled' || field === 'is_limited') {
        processedValue = Boolean(value);
    }

    const data = {
        [field]: processedValue
    };

    $.ajax({
        url: '{{ route('admin.plans.features.update', ['planId' => '__PLAN_ID__', 'featureId' => '__FEATURE_ID__']) }}'
            .replace('__PLAN_ID__', planId)
            .replace('__FEATURE_ID__', featureId),
        method: 'PUT',
        data: JSON.stringify(data),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(resp) {
            if (resp.status === 'success') {
                toastr.success(resp.message || '{{ __('Feature updated successfully') }}');
            } else {
                toastr.error(resp.message || '{{ __('Failed to update feature') }}');
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.message || '{{ __('Failed to update feature') }}';
            toastr.error(error);
        }
    });
}

function deletePlanFeature(planId, featureId) {
    Swal.fire({
        title: '{{ __('Are you sure?') }}',
        text: '{{ __('This will remove the feature from the plan.') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '{{ __('Yes, remove it!') }}',
        cancelButtonText: '{{ __('Cancel') }}',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('admin.plans.features.delete', ['planId' => '__PLAN_ID__', 'featureId' => '__FEATURE_ID__']) }}'
                    .replace('__PLAN_ID__', planId)
                    .replace('__FEATURE_ID__', featureId),
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(resp) {
                    Swal.fire({
                        title: '{{ __('Removed!') }}',
                        text: resp.message || '{{ __('Feature removed successfully') }}',
                        icon: 'success',
                        confirmButtonColor: '#079184',
                    });
                    $(`tr[data-feature-id="${featureId}"]`).fadeOut(300, function() {
                        $(this).remove();
                    });
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || '{{ __('Failed to remove feature') }}';
                    Swal.fire({
                        title: '{{ __('Error') }}',
                        text: error,
                        icon: 'error',
                        confirmButtonColor: '#079184',
                    });
                }
            });
        }
    });
}

function addFeatureToPlan(planId) {
    $('#available-features').slideToggle();
}

function addSelectedFeature(planId) {
    const featureId = $('#new-feature-select').val();
    if (!featureId) {
        toastr.warning('{{ __('Please select a feature') }}');
        return;
    }

    $.ajax({
        url: '{{ route('admin.plans.features.add', ['planId' => '__PLAN_ID__']) }}'.replace('__PLAN_ID__', planId),
        method: 'POST',
        data: JSON.stringify({
            feature_id: featureId,
            is_enabled: true,
            is_limited: false,
            value: null
        }),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(resp) {
            Swal.fire({
                title: '{{ __('Success!') }}',
                text: resp.message || '{{ __('Feature added successfully') }}',
                icon: 'success',
                confirmButtonColor: '#079184',
            }).then(() => {
                location.reload();
            });
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.message || '{{ __('Failed to add feature') }}';
            Swal.fire({
                title: '{{ __('Error') }}',
                text: error,
                icon: 'error',
                confirmButtonColor: '#079184',
            });
        }
    });
}

window.managePlanFeatures = function(planId) {
    $.get('{{ route('admin.plans.features', ['id' => '__ID__']) }}'.replace('__ID__', planId), function(resp) {
        if (resp.success && resp.html) {
            $('#plan-modal').html(resp.html).modal('show');
        }
    });
};
</script>

