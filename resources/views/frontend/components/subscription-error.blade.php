@props(['error', 'upgrade_required' => false, 'usage' => null, 'action_url' => null, 'action_text' => null])

@if(isset($error) && $error)
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-lg shadow-md">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
        </div>
        <div class="ml-3 flex-1">
            <h3 class="text-sm font-semibold text-red-800 mb-2">
                {{ __('Subscription Required') }}
            </h3>
            <p class="text-sm text-red-700 mb-3">
                {{ $error }}
            </p>

            @if($upgrade_required && $usage)
            <div class="bg-white rounded-md p-3 mb-3 border border-red-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">{{ __('Usage') }}</span>
                    <span class="text-sm text-gray-600">
                        {{ $usage['used'] ?? 0 }} / {{ $usage['limit'] ?? __('unlimited') }}
                    </span>
                </div>
                @if(isset($usage['limit']) && $usage['limit'])
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div class="bg-red-500 h-2 rounded-full"
                         style="width: {{ min(100, (($usage['used'] ?? 0) / $usage['limit']) * 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-600">
                    {{ __('Remaining') }}: {{ $usage['remaining'] ?? 0 }}
                </p>
                @endif
            </div>
            @endif

            @if($action_url)
            <a href="{{ $action_url }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary-gradient text-white rounded-lg font-semibold hover:opacity-90 transition text-sm">
                <i class="fas fa-arrow-up"></i>
                {{ $action_text ?? __('Upgrade Plan') }}
            </a>
            @elseif($upgrade_required)
            <a href="{{ route('supplier.subscriptions.plans') ?? route('clinic.subscriptions.plans') ?? route('subscriptions.plans') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary-gradient text-white rounded-lg font-semibold hover:opacity-90 transition text-sm">
                <i class="fas fa-arrow-up"></i>
                {{ __('View Plans') }}
            </a>
            @endif
        </div>
    </div>
</div>
@endif

