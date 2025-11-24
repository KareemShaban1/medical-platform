@php
    $plan = $subscription->plan;
    $entity = $subscription->subscribable;
    $start = $subscription->start_date?->format('M d, Y');
    $end = $subscription->end_date?->format('M d, Y') ?? __('Lifetime');
    $appName = config('app.name');
    $appUrl = url('/');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Subscription Confirmation') }} - {{ $appName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f3f4f6;
            color: #111827;
        }
        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 24px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
        }
        .header {
            background: linear-gradient(135deg, #079184, #10b981);
            padding: 20px 24px;
            color: #ffffff;
            text-align: left;
        }
        .header-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .header-subtitle {
            margin: 4px 0 0;
            font-size: 13px;
            opacity: 0.85;
        }
        .content {
            padding: 24px 24px 8px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 8px;
        }
        .lead-text {
            font-size: 14px;
            margin-bottom: 20px;
            color: #4b5563;
        }
        .summary-card {
            border-radius: 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 16px 18px;
            margin-bottom: 20px;
        }
        .summary-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 8px;
            color: #111827;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 2px 0;
            color: #4b5563;
        }
        .summary-label {
            font-weight: 500;
        }
        .summary-value {
            text-align: right;
        }
        .price-highlight {
            font-size: 20px;
            font-weight: 700;
            color: #079184;
        }
        .cta-wrapper {
            text-align: center;
            padding: 0 24px 24px;
        }
        .cta-button {
            display: inline-block;
            padding: 10px 22px;
            border-radius: 999px;
            background: linear-gradient(135deg, #079184, #10b981);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .footer {
            padding: 16px 24px 20px;
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
        }
        .muted {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1 class="header-title">{{ __('Subscription Activated') }}</h1>
                <p class="header-subtitle">{{ $appName }} &mdash; {{ __('Thank you for trusting our platform') }}</p>
            </div>

            <div class="content">
                <p class="greeting">{{ __('Hello') }},</p>
                <p class="lead-text">
                    {{ __('Your subscription is now active. Here is a quick summary of your plan details:') }}
                </p>

                <div class="summary-card">
                    <p class="summary-title">{{ __('Plan Overview') }}</p>
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
                            <span class="price-highlight">{{ number_format($plan->price ?? 0, 2) }} {{ __('EGP') }}</span>
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

                <p class="lead-text">
                    {{ __('You can now enjoy the features and limits included in this plan. You will be able to upgrade in the future directly from your dashboard.') }}
                </p>
            </div>

            <div class="cta-wrapper">
                <a href="{{ $appUrl }}" class="cta-button">
                    {{ __('Open my dashboard') }}
                </a>
            </div>

            <div class="footer">
                <p class="muted">
                    {{ __('You received this email because you created or updated a subscription on :app.', ['app' => $appName]) }}
                </p>
                <p>&copy; {{ date('Y') }} {{ $appName }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
