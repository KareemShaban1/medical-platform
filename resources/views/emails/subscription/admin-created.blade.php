@php
    $plan = $subscription->plan;
    $entity = $subscription->subscribable;
    $start = $subscription->start_date?->format('M d, Y');
    $end = $subscription->end_date?->format('M d, Y') ?? __('Lifetime');
    $entityType = class_basename($subscription->subscribable_type);
    $entityName = $entity->name ?? '-';
    $appName = config('app.name');
    $adminUrl = url('/admin');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('New Subscription Created') }} - {{ $appName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #0f172a;
            color: #e5e7eb;
        }
        .wrapper {
            width: 100%;
            background-color: #0f172a;
            padding: 24px 0;
        }
        .container {
            max-width: 640px;
            margin: 0 auto;
            background-color: #020617;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.8);
            border: 1px solid #1e293b;
        }
        .header {
            background: radial-gradient(circle at top left, #22c55e, #0891b2);
            padding: 20px 24px;
            color: #f9fafb;
        }
        .header-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .header-subtitle {
            margin: 4px 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 20px 24px 10px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 8px;
        }
        .lead-text {
            font-size: 13px;
            margin-bottom: 18px;
            color: #e5e7eb;
        }
        .summary-card {
            border-radius: 10px;
            background-color: #020617;
            border: 1px solid #1f2937;
            padding: 14px 16px;
            margin-bottom: 18px;
        }
        .summary-title {
            font-size: 13px;
            font-weight: 600;
            margin: 0 0 8px;
            color: #f9fafb;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding: 2px 0;
            color: #9ca3af;
        }
        .summary-label {
            font-weight: 500;
        }
        .summary-value {
            text-align: right;
        }
        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            background-color: #065f46;
            color: #bbf7d0;
        }
        .entity-name {
            font-weight: 600;
            color: #e5e7eb;
        }
        .entity-type {
            font-size: 11px;
            color: #9ca3af;
        }
        .cta-wrapper {
            text-align: center;
            padding: 0 24px 22px;
        }
        .cta-button {
            display: inline-block;
            padding: 9px 20px;
            border-radius: 999px;
            background: linear-gradient(135deg, #22c55e, #0ea5e9);
            color: #0f172a !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }
        .footer {
            padding: 14px 24px 18px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
        }
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
            <p class="greeting">{{ __('Hello Admin,') }}</p>
            <p class="lead-text">
                {{ __('A new subscription has just been created. Here are the key details so you can quickly review it:') }}
            </p>

            <div class="summary-card">
                <p class="summary-title">{{ __('Subscriber') }}</p>
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
                    <span class="summary-value">
                        <span class="status-pill">{{ ucfirst($subscription->status) }}</span>
                    </span>
                </div>
            </div>

            <div class="summary-card">
                <p class="summary-title">{{ __('Plan Details') }}</p>
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
                    <span class="summary-value">{{ number_format($plan->price ?? 0, 2) }} {{ __('EGP') }}</span>
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
                {{ __('Open admin subscriptions') }}
            </a>
        </div>

        <div class="footer">
            <p>{{ __('This message was generated automatically by :app.', ['app' => $appName]) }}</p>
        </div>
    </div>
</div>
</body>
</html>
