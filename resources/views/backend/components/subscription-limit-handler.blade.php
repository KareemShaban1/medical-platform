@once
@push('scripts')
<script>
/**
 * Handle subscription/feature limit errors returned from the API.
 * @param {XMLHttpRequest} xhr
 * @param {Object} [options]
 * @param {string} [options.actionUrl] - Fallback URL when user chooses to view plans.
 * @returns {boolean} true if the error was handled and further processing should stop.
 */
function handleSubscriptionError(xhr, options = {}) {
    const response = (xhr && xhr.responseJSON) ? xhr.responseJSON : {};
    const errorType = response.error_type;
    if (errorType !== 'feature_limit_exceeded' && errorType !== 'feature_not_enabled') {
        return false;
    }

    const usage = response.usage || {};
    const used = usage.used ?? 0;
    const limit = usage.limit ?? null;
    const remaining = usage.remaining ?? 0;
    const percentage = usage.percentage ?? (limit ? Math.min(100, (used / limit) * 100) : 0);

    const labels = {
        title: @json(__('Subscription Limit Exceeded')),
        viewPlans: @json(__('View Plans')),
        cancel: @json(__('Cancel')),
        usage: @json(__('Usage')),
        remaining: @json(__('Remaining')),
        defaultMessage: @json(__('Feature limit exceeded'))
    };

    let htmlMessage = `<div class="text-start">
        <p class="mb-3"><strong>${response.message || labels.defaultMessage}</strong></p>`;

    if (limit !== null) {
        htmlMessage += `<div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span>${labels.usage}</span>
                <span><strong>${used} / ${limit}</strong></span>
            </div>
            <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-danger" role="progressbar"
                     style="width: ${Math.min(100, percentage)}%"
                     aria-valuenow="${percentage}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                    ${percentage.toFixed(0)}%
                </div>
            </div>
            <small class="text-muted">${labels.remaining}: ${remaining}</small>
        </div>`;
    }

    htmlMessage += `</div>`;

    const actionUrl = response.action_url || options.actionUrl || @json(route('home') . '#subscriptions-plans');

    Swal.fire({
        icon: 'warning',
        title: labels.title,
        html: htmlMessage,
        confirmButtonText: labels.viewPlans,
        confirmButtonColor: '#079184',
        showCancelButton: true,
        cancelButtonText: labels.cancel,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = actionUrl;
        }
    });

    return true;
}
</script>
@endpush
@endonce
