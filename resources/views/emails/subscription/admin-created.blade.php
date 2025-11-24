@php
    $plan = $subscription->plan;
    $entity = $subscription->subscribable;
    $start = $subscription->start_date?->format('M d, Y');
    $end = $subscription->end_date?->format('M d, Y') ?? __('Lifetime');
    $entityType = class_basename($subscription->subscribable_type);
    $entityName = $entity->name ?? '-';
    $appName = config('app.name');
    $adminUrl = url('/admin/dashboard');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('New Subscription Created') }} - {{ $appName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background-color: #f3f4f6; color: #111827; }
        .wrapper { width: 100%; background-color: #f3f4f6; padding: 24px 0; }
        .container { max-width: 640px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15); }
        .header { background: linear-gradient(135deg, #079184, #10b981); padding: 20px 24px; color: #ffffff; }
        .header-title { margin: 0; font-size: 20px; font-weight: 700; }
        .header-subtitle { margin: 4px 0 0; font-size: 13px; opacity: 0.85; }
        .content { padding: 24px 24px 8px; }
        .summary-card { border-radius: 10px; background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 16px 18px; margin-bottom: 20px; }
        .summary-title { font-size: 14px; font-weight: 600; margin: 0 0 8px; color: #111827; }
        .summary-row { display: flex; justify-content: space-between; font-size: 13px; padding: 2px 0; color: #4b5563; }
        .summary-label { font-weight: 500; }
        .summary-value { text-align: right; }
        .status-pill { display: inline-block; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; background: linear-gradient(135deg, #22c55e, #10b981); color: #064e3b; }
        .entity-name { font-weight: 600; color: #111827; }
        .entity-type { font-size: 12px; color: #4b5563; }
        .cta-wrapper { text-align: center; padding: 0 24px 24px; }
        .cta-button { display: inline-block; padding: 10px 22px; border-radius: 999px; background: linear-gradient(135deg, #079184, #10b981); color: #ffffff !important; text-decoration: none; font-size: 13px; font-weight: 700; }
        .footer { padding: 16px 24px 20px; font-size: 11px; color: #6b7280; text-align: center; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1 class="header-title">{{ __('New Subscription Created') }}</h1>
                <p class="header-subtitle">{{ $appName }} &mdash; {{ __('Realtime subscription activity for admins') }}</p>
            </div>

            <div class="content">
                <p class="summary-title" style="margin-bottom: 12px;">{{ __('Subscriber') }}</p>
                <div class="summary-card">
                    <div class="summary-row">
                        <span class="summary-label">{{ __('Name') }}</span>
                        <span class="summary-value entity-name">{{ $entityName }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">{{ __('Type') }}</span>
                        <span class="summary-value entity-type">{{ $entityType }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">{{ __('Status') }}</span>
                        <span class="summary-value"><span class="status-pill">{{ ucfirst($subscription->status) }}</span></span>
                    </div>
                </div>

                <p class="summary-title" style="margin-bottom: 12px;">{{ __('Plan Overview') }}</p>
                <div class="summary-card">
                    <div class="summary-row">
                        <span class="summary-label">{{ __('Plan Name') }}</span>
                        <span class="summary-value">{{ $plan->name ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">{{ __('Type') }}</span>
                        <span class="summary-value">{{ ucfirst($plan->plan_type ?? '-') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">{{ __('Level') }}</span>
                        <span class="summary-value">{{ ucfirst($plan->level ?? '-') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">{{ __('Price') }}</span>
                        <span class="summary-value">
                            <span class="status-pill" style="background:transparent;color:#111827;font-weight:700;background-color:#ecfdf3;border:1px solid #bbf7d0;">
                                {{ number_format($plan->price ?? 0, 2) }} {{ __('EGP') }}
                            </span>
                        </span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">{{ __('Start Date') }}</span>
                        <span class="summary-value">{{ $start ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">{{ __('End Date') }}</span>
                        <span class="summary-value">{{ $end }}</span>
                    </div>
                </div>
            </div>

            <div class="cta-wrapper">
                <a href="{{ $adminUrl }}" class="cta-button">
                    {{ __('Open Admin Dashboard') }}
                </a>
            </div>

            <div class="footer">
                <p class="muted">{{ __('This message was generated automatically by :app.', ['app' => $appName]) }}</p>
                <p>&copy; {{ date('Y') }} {{ $appName }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
